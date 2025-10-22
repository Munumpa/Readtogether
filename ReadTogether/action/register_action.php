<?php
// action/register_action.php
session_start();
include '../db.php';

// 1. รับข้อมูลและตรวจสอบค่าว่าง
if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
    header("Location: ../register.php?error=กรุณากรอกข้อมูลให้ครบทุกช่อง");
    exit();
}

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// 2. *** ส่วนที่สำคัญที่สุด: ตรวจสอบข้อมูลซ้ำในฐานข้อมูล ***
$sql_check = "SELECT user_id FROM users WHERE username = ? OR email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $username, $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // ถ้าเจอข้อมูลซ้ำ ให้ส่งกลับไปพร้อม Error
    header("Location: ../register.php?error=ชื่อผู้ใช้หรืออีเมลนี้มีอยู่ในระบบแล้ว");
    exit();
}
$stmt_check->close();


// 3. เข้ารหัสรหัสผ่าน
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 4. กำหนด Role (ย้ายมาไว้ตรงนี้)
$role = 'user'; // ค่าเริ่มต้น
// (ถ้าคุณต้องการใช้ฟีเจอร์สมัครเป็นแอดมินอัตโนมัติ)
// if ($email == 'admin@yourdomain.com') {
//     $role = 'admin';
// }


// 5. *** ทำการ INSERT ข้อมูลที่สมบูรณ์เพียงครั้งเดียว ***
$sql_insert = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
// "ssss" -> String 4 ตัว
$stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);

// 6. ตรวจสอบผลลัพธ์
if ($stmt_insert->execute()) {
    // ถ้าสำเร็จ ให้ส่งไปหน้า login
    header("Location: ../login.php?status=register_success");
    exit();
} else {
    // ถ้าไม่สำเร็จ ให้ส่งกลับไปพร้อม Error
    header("Location: ../register.php?error=เกิดข้อผิดพลาดในการบันทึกข้อมูล");
    exit();
}

?>