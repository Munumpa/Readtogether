<?php
// ===================================================================
// VOTE_ACTION.PHP - ไฟล์ประมวลผลการโหวตให้คะแนนหนังสือ
// ===================================================================
// ไฟล์นี้รับข้อมูลการโหวตจากฟอร์มดาว (Star Rating) และบันทึกลงฐานข้อมูล
// โดยใช้เทคนิคพิเศษเพื่อจัดการทั้งการโหวตครั้งแรกและการแก้ไขโหวต

session_start();
include '../db.php';

// --- 1. ตรวจสอบสิทธิ์การใช้งาน ---
if (!isset($_SESSION['user_id'])) {
    // ใช้ die() หรือ header() ก็ได้ แต่ die() จะหยุดทันทีและชัดเจนกว่า
    die("กรุณาเข้าสู่ระบบก่อนทำการโหวต");
}
$user_id = $_SESSION['user_id'];

// --- 2. รับข้อมูลและตรวจสอบความถูกต้อง (Input Validation) ---

// 2.1. ตรวจสอบว่ามีข้อมูลส่งมาครบหรือไม่
if (empty($_POST['book_id']) || empty($_POST['score'])) {
    die("ข้อมูลการโหวตไม่ครบถ้วน");
}

// 2.2. รับข้อมูลและแปลงเป็นตัวเลขเพื่อความปลอดภัย
$book_id = (int)$_POST['book_id'];
$score = (int)$_POST['score'];

// 2.3. (Refinement) ตรวจสอบว่าคะแนนอยู่ในช่วงที่ถูกต้อง (1-5)
if ($score < 1 || $score > 5) {
    die("ค่าคะแนนไม่ถูกต้อง");
}

// --- 3. บันทึกข้อมูลลงฐานข้อมูลด้วยเทคนิค "UPSERT" ---
// "UPSERT" = UPDATE or INSERT

// 3.1. เตรียมคำสั่ง SQL ที่ฉลาด: INSERT ... ON DUPLICATE KEY UPDATE
// - พยายาม INSERT: เพิ่มแถวข้อมูลการโหวตใหม่
// - ถ้าล้มเหลวเพราะข้อมูลซ้ำ (จาก UNIQUE KEY ที่เราตั้งไว้ในตาราง votes):
//   ให้เปลี่ยนไปทำคำสั่ง UPDATE score = ? แทน
$sql = "INSERT INTO votes (user_id, book_id, score) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE score = VALUES(score)";

$stmt = $conn->prepare($sql);

// 3.2. ผูกค่าตัวแปร (Binding Parameters)
// "iii" -> Integer 3 ตัว (user_id, book_id, score)
// เราใช้ VALUES(score) ใน SQL เพื่ออ้างถึงค่า score ใหม่ที่พยายามจะ INSERT
// ทำให้ไม่ต้อง bind parameter ตัวที่ 4
$stmt->bind_param("iii", $user_id, $book_id, $score);

// 4. สั่งให้ทำงานและส่งผู้ใช้กลับ (Redirect)
if ($stmt->execute()) {
    // ถ้าสำเร็จ: ส่งผู้ใช้กลับไปที่หน้ารายละเอียดของหนังสือเล่มเดิม
    header("Location: ../book_detail.php?id=" . $book_id . "&status=vote_success");
    exit();
} else {
    // (UX Refinement) ถ้าไม่สำเร็จ: ส่งกลับไปที่หน้าเดิมพร้อมข้อความ Error
    // ในระบบจริง ควรบันทึก Error Log: error_log("Vote Error: " . $stmt->error);
    header("Location: ../book_detail.php?id=" . $book_id . "&error=เกิดข้อผิดพลาดในการบันทึกคะแนน");
    exit();
}

// ไม่จำเป็นต้องมี close() ที่นี่ เพราะ exit() จะหยุดการทำงานไปก่อนแล้ว
?>