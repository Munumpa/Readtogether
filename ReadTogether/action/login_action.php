<?php
// ===================================================================
// LOGIN_ACTION.PHP - ไฟล์ประมวลผลการเข้าสู่ระบบ
// ===================================================================
// ไฟล์นี้ทำหน้าที่รับ Username/Password จากฟอร์ม login.php
// ตรวจสอบกับฐานข้อมูล, สร้าง Session, และส่งผู้ใช้ไปยังหน้าที่เหมาะสม

// 1. เริ่มต้น Session เพื่อเตรียมสร้าง "บัตรประจำตัว" ให้ผู้ใช้
session_start();
include '../db.php';

// --- 2. การตรวจสอบข้อมูลเบื้องต้น ---

// 2.1. ตรวจสอบว่ามีการส่งข้อมูลมาครบหรือไม่
if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: ../login.php?error=กรุณากรอกชื่อผู้ใช้และรหัสผ่าน");
    exit();
}
$username = $_POST['username'];
$password = $_POST['password'];


// --- 3. ค้นหาผู้ใช้ในฐานข้อมูล ---

// 3.1. เตรียมคำสั่ง SQL (เพิ่มการดึง profile_pic เข้ามาด้วย!)
$sql = "SELECT user_id, username, password, role, profile_pic FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


// --- 4. ตรวจสอบผลลัพธ์ ---

// 4.1. เช็คว่าเจอผู้ใช้ตรงกับ username ที่กรอกมา 1 คนหรือไม่
if ($result->num_rows === 1) {
    
    // ถ้าเจอ: ดึงข้อมูลผู้ใช้คนนั้นออกมาเก็บในตัวแปร $user
    $user = $result->fetch_assoc();
    
    // 4.2. *** หัวใจของการ Login: ตรวจสอบรหัสผ่าน ***
    // ใช้ password_verify() เพื่อเปรียบเทียบรหัสผ่านที่ผู้ใช้กรอก ($password)
    // กับรหัสผ่านที่ถูกเข้ารหัสไว้ในฐานข้อมูล ($user['password'])
    if (password_verify($password, $user['password'])) {
        
        // --- 5. (สำเร็จ!) สร้าง Session ---
        // เมื่อรหัสผ่านถูกต้อง เราจะบันทึกข้อมูลสำคัญของผู้ใช้ไว้ใน "บัตรประจำตัว" (Session)
        // เพื่อให้หน้าอื่นๆ สามารถรู้ได้ว่าใครกำลังใช้งานอยู่
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile_pic'] = $user['profile_pic']; // (Refinement) เพิ่มรูปโปรไฟล์ใน Session

        // Regenerate Session ID เพื่อป้องกัน Session Fixation attacks
        session_regenerate_id(true);
        
        // 6. ส่งผู้ใช้ไปยังหน้าแรกหลัง login สำเร็จ
        header("Location: ../index.php");
        exit();

    } else {
        // (ล้มเหลว) กรณีรหัสผ่านไม่ถูกต้อง
        header("Location: ../login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
        exit();
    }

} else {
    // (ล้มเหลว) กรณีไม่พบชื่อผู้ใช้นี้ในระบบ
    // เราใช้ข้อความ Error เดียวกันเพื่อความปลอดภัย
    header("Location: ../login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
    exit();
}
?>