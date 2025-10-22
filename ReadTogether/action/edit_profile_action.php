<?php
// ===================================================================
// EDIT_PROFILE_ACTION.PHP - ไฟล์ประมวลผลการแก้ไขข้อมูลส่วนตัว
// ===================================================================

session_start();
include '../db.php';

// --- 1. ตรวจสอบสิทธิ์การใช้งาน ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=เซสชั่นหมดอายุ กรุณาเข้าสู่ระบบอีกครั้ง");
    exit();
}
$user_id = $_SESSION['user_id'];

// --- 2. รับข้อมูล Bio จากฟอร์ม ---
// ใช้ trim() เพื่อตัดช่องว่างหน้า-หลังที่ไม่จำเป็นออก
$bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';


// --- 3. จัดการไฟล์รูปโปรไฟล์ใหม่ (ถ้ามีการอัปโหลด) ---

$new_profile_pic_name = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0 && $_FILES['profile_pic']['size'] > 0) {
    
    $target_dir = "../uploads/profiles/";
    $file_extension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $new_filename = 'user' . $user_id . '_' . uniqid('', true) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    // 3.1. ตรวจสอบชนิดและขนาดไฟล์
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        header("Location: ../edit_profile.php?error=ไฟล์ต้องเป็น JPG, PNG, GIF เท่านั้น");
        exit();
    }
    if ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) { // giới hạn 2MB
        header("Location: ../edit_profile.php?error=ขนาดไฟล์ต้องไม่เกิน 2MB");
        exit();
    }

    // 3.2. (สำคัญ!) ลบรูปโปรไฟล์เก่าทิ้ง
    // ดึงชื่อไฟล์รูปเก่าจาก Session ที่เรามีอยู่แล้ว เพื่อลดการ Query ฐานข้อมูล
    $old_pic_name = $_SESSION['profile_pic'] ?? null;
    if ($old_pic_name && file_exists($target_dir . $old_pic_name)) {
        unlink($target_dir . $old_pic_name);
    }

    // 3.3. ย้ายไฟล์ใหม่เข้าที่
    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $new_profile_pic_name = $new_filename;
    } else {
        header("Location: ../edit_profile.php?error=เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
        exit();
    }
}


// --- 4. อัปเดตข้อมูลลงฐานข้อมูล ---

if ($new_profile_pic_name) {
    // ถ้ามีรูปใหม่: อัปเดตทั้ง bio และ profile_pic
    $sql = "UPDATE users SET bio = ?, profile_pic = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $bio, $new_profile_pic_name, $user_id);
} else {
    // ถ้าไม่มีรูปใหม่: อัปเดตเฉพาะ bio
    $sql = "UPDATE users SET bio = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $bio, $user_id);
}


// --- 5. ตรวจสอบผลลัพธ์และอัปเดต Session ---

if ($stmt->execute()) {
    // (Feature Enhancement) ถ้ามีการเปลี่ยนรูป ให้ทำการอัปเดต Session ด้วย
    if ($new_profile_pic_name) {
        $_SESSION['profile_pic'] = $new_profile_pic_name;
    }
    
    // ส่งผู้ใช้กลับไปที่หน้าโปรไฟล์
    header("Location: ../profile.php?id=" . $user_id . "&status=update_success");
    exit();
} else {
    // ในระบบจริง ควรบันทึก Error: error_log("Edit Profile Error: " . $stmt->error);
    header("Location: ../edit_profile.php?error=เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    exit();
}
?>