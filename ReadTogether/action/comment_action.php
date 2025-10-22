<?php
// ===================================================================
// COMMENT_ACTION.PHP - ไฟล์ประมวลผลการส่งความคิดเห็น
// ===================================================================
// ไฟล์นี้ทำหน้าที่รับข้อความคอมเมนต์จากฟอร์มในหน้า book_detail.php
// แล้วบันทึกลงในฐานข้อมูล

session_start();
include '../db.php';

// --- 1. ตรวจสอบสิทธิ์การใช้งาน ---
if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อนแสดงความคิดเห็น");
}
$user_id = $_SESSION['user_id'];

// --- 2. รับข้อมูลและตรวจสอบความถูกต้อง (Input Validation) ---

// 2.1. ตรวจสอบว่ามีข้อมูลส่งมาครบหรือไม่
if (empty($_POST['book_id']) || empty($_POST['content'])) {
    die("ข้อมูลไม่ครบถ้วน");
}

// 2.2. รับข้อมูลและแปลง/ทำความสะอาด
$book_id = (int)$_POST['book_id'];
// trim() จะช่วยตัดช่องว่างที่ไม่จำเป็นออกจากหน้าและหลังข้อความ
$content = trim($_POST['content']);

// 2.3. (Refinement) ตรวจสอบว่าคอมเมนต์ไม่ใช่ค่าว่างจริงๆ หลังจาก trim() แล้ว
if (empty($content)) {
    header("Location: ../book_detail.php?id=" . $book_id . "&error=กรุณาพิมพ์ข้อความในความคิดเห็น");
    exit();
}


// --- 3. บันทึกข้อมูลลงฐานข้อมูล (INSERT) ---

$sql = "INSERT INTO comments (book_id, user_id, content) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// "iis" -> Integer, Integer, String
$stmt->bind_param("iis", $book_id, $user_id, $content);


// --- 4. ตรวจสอบผลลัพธ์และส่งผู้ใช้กลับ (Redirect) ---

if ($stmt->execute()) {
    // ถ้าสำเร็จ: ส่งผู้ใช้กลับไปที่หน้ารายละเอียดของหนังสือเล่มเดิม
    header("Location: ../book_detail.php?id=" . $book_id . "&status=comment_success#comments");
    exit();
} else {
    // (UX Refinement) ถ้าไม่สำเร็จ: ส่งกลับไปที่หน้าเดิมพร้อมข้อความ Error
    // ในระบบจริง ควรบันทึก Error Log: error_log("Comment Error: " . $stmt->error);
    header("Location: ../book_detail.php?id=" . $book_id . "&error=เกิดข้อผิดพลาดในการส่งความคิดเห็น");
    exit();
}
?>