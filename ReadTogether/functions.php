<?php
// ===================================================================
// FUNCTIONS.PHP - ไฟล์รวมฟังก์ชันเสริม
// ===================================================================
// ไฟล์นี้ใช้สำหรับเก็บฟังก์ชันที่เราสร้างขึ้นเอง เพื่อนำไปใช้ซ้ำๆ ในส่วนต่างๆ ของเว็บไซต์
// ช่วยลดความซ้ำซ้อนของโค้ดและทำให้ง่ายต่อการบำรุงรักษา

// --- 1. (แนะนำ) กำหนดค่าคงที่สำหรับ Path หลักๆ ---
// define() ใช้สำหรับสร้าง "ค่าคงที่" ซึ่งเป็นตัวแปรที่ไม่สามารถเปลี่ยนแปลงค่าได้
// __DIR__ คือค่าพิเศษใน PHP ที่จะให้ Path เต็มของโฟลเดอร์ที่ไฟล์นี้อยู่
// DIRECTORY_SEPARATOR คือเครื่องหมาย / หรือ \ ตามระบบปฏิบัติการ (Windows/Linux)
// define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR); // Path หลักของโปรเจกต์
// define('UPLOADS_PATH', 'uploads/profiles/');      // Path ไปยังโฟลเดอร์อัปโหลด
// define('IMAGES_PATH', 'images/');                // Path ไปยังโฟลเดอร์รูปภาพของเว็บ
// **หมายเหตุ:** การใช้ define() เป็นขั้นสูง ถ้าตอนนี้ดูซับซ้อนไป สามารถข้ามไปก่อนได้ครับ

/**
 * ฟังก์ชันสำหรับแสดงผล URL ของรูปโปรไฟล์ที่ถูกต้อง
 * 
 * ฟังก์ชันนี้จะตรวจสอบว่าผู้ใช้มีรูปโปรไฟล์ที่อัปโหลดไว้หรือไม่
 * ถ้ามีและไฟล์นั้นอยู่จริง จะคืนค่า Path ไปยังรูปนั้น
 * มิฉะนั้น จะคืนค่า Path ไปยังรูปโปรไฟล์เริ่มต้น (Default Avatar)
 *
 * @param string|null $profile_pic_filename ชื่อไฟล์รูปจากฐานข้อมูล (อาจเป็น NULL)
 * @return string URL ของรูปภาพที่จะใช้แสดงผล
 */
function getUserProfileImage($profile_pic_filename) {
    // กำหนด Path ของรูปโปรไฟล์เริ่มต้น
    $default_avatar = 'images/default-avatar.png';
    
    // กำหนด Path เต็มไปยังไฟล์รูปโปรไฟล์ของผู้ใช้
    $user_avatar_path = 'uploads/profiles/' . $profile_pic_filename;

    // --- ตรวจสอบเงื่อนไข ---
    // 1. $profile_pic_filename ต้องไม่ใช่ค่าว่าง (null)
    // 2. ต้องมีไฟล์นั้นอยู่จริงในเซิร์ฟเวอร์ (ใช้ file_exists() ตรวจสอบ)
    if ($profile_pic_filename && file_exists($user_avatar_path)) {
        // ถ้าเงื่อนไขเป็นจริง: คืนค่า Path ไปยังรูปของผู้ใช้
        return htmlspecialchars($user_avatar_path);
    } else {
        // ถ้าเงื่อนไขเป็นเท็จ: คืนค่า Path ไปยังรูปเริ่มต้น
        return $default_avatar;
    }

    /*
    // --- (ทางเลือก) เขียนแบบสั้นด้วย Ternary Operator ---
    // โค้ดข้างบนสามารถย่อให้เหลือเพียงบรรทัดเดียวได้ดังนี้ (ทำงานเหมือนกันทุกประการ)
    return ($profile_pic_filename && file_exists($user_avatar_path)) 
           ? htmlspecialchars($user_avatar_path) 
           : $default_avatar;
    */
}

/**
 * (ตัวอย่างฟังก์ชันในอนาคต) ฟังก์ชันสำหรับตัดข้อความให้สั้นลง
 * 
 * @param string $text ข้อความที่ต้องการตัด
 * @param int $length ความยาวสูงสุดที่ต้องการ
 * @return string ข้อความที่ถูกตัดแล้วพร้อมกับ "..."
 */
function excerpt($text, $length = 150) {
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length, 'UTF-8') . '...';
    }
    return $text;
}
?>