<?php
// ===================================================================
// ADD_BOOK_ACTION.PHP - ไฟล์ประมวลผลการเพิ่มหนังสือใหม่
// ===================================================================
// ไฟล์นี้ทำหน้าที่รับข้อมูลจากฟอร์ม add_book.php, จัดการการอัปโหลดไฟล์,
// บันทึกลงฐานข้อมูล, และส่งผู้ใช้กลับไปยังหน้าที่เหมาะสม

session_start();
include '../db.php';

// --- 1. ตรวจสอบสิทธิ์การใช้งาน ---
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้ล็อกอิน ให้ส่งกลับไปหน้า login
    header("Location: ../login.php?error=เซสชั่นหมดอายุ กรุณาเข้าสู่ระบบอีกครั้ง");
    exit();
}

// --- 2. รับข้อมูลและตรวจสอบค่าว่าง ---
if (empty($_POST['title']) || empty($_POST['author'])) {
    header("Location: ../add_book.php?error=กรุณากรอกชื่อหนังสือและผู้แต่ง");
    exit();
}
$title = $_POST['title'];
$author = $_POST['author'];
$description = $_POST['description'];
$added_by = $_SESSION['user_id'];
$cover_image_name = null; // (Bug Fix) กำหนดค่าเริ่มต้นเป็น NULL ให้ตรงกับฐานข้อมูล

// --- 3. จัดการไฟล์ที่อัปโหลด (ถ้ามี) ---
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0 && $_FILES['cover_image']['size'] > 0) {

    $target_dir = "../uploads/";
    $file_extension = strtolower(pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = 'cover_' . uniqid('', true) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    // 3.1. ตรวจสอบชนิดไฟล์
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        // (UX Refinement) ส่ง Error กลับไปที่ฟอร์มแทนการ die()
        header("Location: ../add_book.php?error=อัปโหลดได้เฉพาะไฟล์ JPG, PNG, GIF เท่านั้น");
        exit();
    }

    // 3.2. ตรวจสอบขนาดไฟล์ (ตัวอย่าง: ไม่เกิน 5MB)
    if ($_FILES["cover_image"]["size"] > 5 * 1024 * 1024) { // 5 MB
        header("Location: ../add_book.php?error=ขนาดไฟล์ต้องไม่เกิน 5MB");
        exit();
    }

    // 3.3. ย้ายไฟล์ไปยังโฟลเดอร์ปลายทาง
    if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
        // ถ้าอัปโหลดสำเร็จ ให้กำหนดชื่อไฟล์ใหม่เพื่อเตรียมบันทึก
        $cover_image_name = $new_filename;
    } else {
        header("Location: ../add_book.php?error=เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
        exit();
    }
}

// --- 4. บันทึกข้อมูลลงฐานข้อมูล ---
$sql = "INSERT INTO books (title, author, description, added_by, cover_image) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
// "sssis" -> String, String, String, Integer, String
$stmt->bind_param("sssis", $title, $author, $description, $added_by, $cover_image_name);

if ($stmt->execute()) {
    // ถ้าสำเร็จ: ส่งกลับไปหน้า books.php พร้อมข้อความแจ้งสถานะ
    header("Location: ../books.php?status=add_success");
    exit();
} else {
    // ถ้าไม่สำเร็จ: ส่งกลับไปที่ฟอร์มพร้อมข้อความ Error
    // ในระบบจริง เราควรบันทึก Error Log: error_log("Add Book Error: " . $stmt->error);
    header("Location: ../add_book.php?error=เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    exit();
}
?>