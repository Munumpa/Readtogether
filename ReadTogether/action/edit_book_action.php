<?php
// ===================================================================
// EDIT_BOOK_ACTION.PHP - ไฟล์ประมวลผลการแก้ไขข้อมูลหนังสือ
// ===================================================================
// ไฟล์นี้จะรับข้อมูลจากฟอร์ม edit_book.php, จัดการการเปลี่ยนรูปปก (ถ้ามี),
// ตรวจสอบสิทธิ์, และอัปเดตข้อมูลลงฐานข้อมูล

session_start();
include '../db.php';

// --- 1. ตรวจสอบสิทธิ์และรับข้อมูลเบื้องต้น ---

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=เซสชั่นหมดอายุ กรุณาเข้าสู่ระบบอีกครั้ง");
    exit();
}

if (empty($_POST['book_id']) || empty($_POST['title']) || empty($_POST['author'])) {
    // ใช้ javascript:history.back() ถ้าไม่ต้องการส่ง ID กลับไป
    die("ข้อมูลไม่ครบถ้วน กรุณากลับไปกรอกข้อมูลให้ครบ");
}
$book_id = $_POST['book_id'];
$title = $_POST['title'];
$author = $_POST['author'];
$description = $_POST['description'];


// --- 2. ตรวจสอบสิทธิ์ความเป็นเจ้าของ (Security Check) ---

// ดึงข้อมูล "เจ้าของ" และ "ชื่อรูปปกเก่า" ของหนังสือเล่มนี้
$check_sql = "SELECT added_by, cover_image FROM books WHERE book_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $book_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$book_data = $check_result->fetch_assoc();

if (!$book_data) {
    die("ไม่พบหนังสือที่ต้องการแก้ไข");
}

if ($book_data['added_by'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
    die("คุณไม่มีสิทธิ์แก้ไขหนังสือเล่มนี้");
}
$check_stmt->close();


// --- 3. จัดการไฟล์รูปปกใหม่ (ถ้ามีการอัปโหลด) ---

$new_cover_image_name = null; // เตรียมตัวแปรสำหรับเก็บชื่อไฟล์ใหม่
$target_dir = "../uploads/";

if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0 && $_FILES['cover_image']['size'] > 0) {
    
    // Logic การจัดการไฟล์ (เหมือนกับใน add_book_action.php)
    $file_extension = strtolower(pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = 'cover_' . uniqid('', true) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        header("Location: ../edit_book.php?id=" . $book_id . "&error=ไฟล์ต้องเป็น JPG, PNG, GIF เท่านั้น");
        exit();
    }

    // (สำคัญ!) ลบรูปปกเก่าทิ้ง (ถ้ามี และไม่ใช่รูป default)
    $old_cover_image = $book_data['cover_image'];
    if ($old_cover_image && $old_cover_image != 'default.jpg' && file_exists($target_dir . $old_cover_image)) {
        unlink($target_dir . $old_cover_image); // คำสั่งลบไฟล์
    }

    // ย้ายไฟล์ใหม่เข้าที่
    if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
        $new_cover_image_name = $new_filename;
    } else {
        header("Location: ../edit_book.php?id=" . $book_id . "&error=เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
        exit();
    }
}


// --- 4. อัปเดตข้อมูลลงฐานข้อมูล (UPDATE) ---

// เราจะสร้าง Query แบบไดนามิก ขึ้นอยู่กับว่ามีการอัปโหลดรูปใหม่หรือไม่
if ($new_cover_image_name) {
    // ถ้ามีรูปใหม่: อัปเดตทุกอย่าง รวมถึง cover_image
    $sql_update = "UPDATE books SET title = ?, author = ?, description = ?, cover_image = ? WHERE book_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $title, $author, $description, $new_cover_image_name, $book_id);
} else {
    // ถ้าไม่มีรูปใหม่: อัปเดตเฉพาะข้อมูลตัวอักษร
    $sql_update = "UPDATE books SET title = ?, author = ?, description = ? WHERE book_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $title, $author, $description, $book_id);
}

// 5. ตรวจสอบผลลัพธ์และส่งผู้ใช้กลับ
if ($stmt_update->execute()) {
    header("Location: ../book_detail.php?id=" . $book_id . "&status=update_success");
    exit();
} else {
    // ในระบบจริง ควรบันทึก Error: error_log("Edit Book Error: " . $stmt_update->error);
    header("Location: ../edit_book.php?id=" . $book_id . "&error=เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    exit();
}
?>