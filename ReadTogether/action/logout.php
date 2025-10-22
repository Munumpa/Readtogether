<?php
// logout.php

session_start(); // เริ่ม session เพื่อเข้าไปจัดการ

// ล้างข้อมูล session ทั้งหมด
session_unset();

// ทำลาย session ทิ้งอย่างถาวร
session_destroy();

// ส่งผู้ใช้กลับไปที่หน้าแรก
header("Location: ../index.php");
exit();
?>