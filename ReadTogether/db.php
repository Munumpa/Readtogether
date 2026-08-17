<?php
// ===================================================================
// DB.PHP - ไฟล์เชื่อมต่อฐานข้อมูล (ปรับให้ใช้ environment variables)
// ===================================================================

// อ่านค่าจาก environment variables (หรือใช้ค่า default ถ้าไม่ได้ตั้ง)
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: '';
$dbname     = getenv('DB_NAME') ?: 'readtogether_db';

// ตั้งค่าให้ mysqli แสดงข้อผิดพลาดแบบ exception เพื่อดีบั๊กง่ายขึ้น
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // บันทึกข้อผิดพลาดไว้ใน log และแสดงข้อความมิตรแก่ผู้ใช้
    error_log("MySQL Connection Error: " . $e->getMessage());
    die("<h1>เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล</h1><p>กรุณาตรวจสอบการตั้งค่าและลองอีกครั้ง</p>");
}

?>
