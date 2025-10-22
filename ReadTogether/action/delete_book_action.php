<?php
// action/delete_book_action.php
session_start();
include '../db.php';

// 1. ตรวจสอบสิทธิ์แอดมิน (เพราะหน้านี้ถูกเรียกจาก Admin Dashboard)
// และตรวจสอบการ Login ทั่วไป
if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}

// 2. รับ ID หนังสือที่ต้องการลบ
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Book ID.");
}
$book_id = $_GET['id'];

// 3. (แก้ไข) ดึงข้อมูลเจ้าของหนังสือเพื่อตรวจสอบสิทธิ์
//    เราต้อง SELECT ข้อมูลมาก่อน ถึงจะใช้ตัวแปรได้
$check_sql = "SELECT added_by FROM books WHERE book_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $book_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$book_owner = $check_result->fetch_assoc();

if (!$book_owner) {
    die("ไม่พบหนังสือที่ต้องการลบ");
}

// 4. ตรวจสอบสิทธิ์ (Admin ลบได้หมด, User ลบได้แค่ของตัวเอง)
if ($_SESSION['role'] != 'admin' && $book_owner['added_by'] != $_SESSION['user_id']) {
    die("คุณไม่มีสิทธิ์ลบหนังสือเล่มนี้");
}
$check_stmt->close();


// 5. เตรียมคำสั่ง SQL DELETE (ตอนนี้จะทำงานได้แล้วเพราะเราตั้ง ON DELETE CASCADE)
$sql = "DELETE FROM books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);

// 6. สั่งให้ทำงานและส่งกลับ
if ($stmt->execute()) {
    // ส่งกลับไปหน้า books.php เพราะหนังสือเล่มนี้ไม่มีรายละเอียดให้ดูอีกต่อไป
    header("Location: ../books.php?status=delete_success");
    exit();
} else {
    header("Location: ../admin_dashboard.php?error=delete_failed");
    exit();
}
?>