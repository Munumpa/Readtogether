<?php
// ===================================================================
// EDIT_PROFILE.PHP - หน้าฟอร์มสำหรับแก้ไขข้อมูลส่วนตัว
// ===================================================================
// ไฟล์นี้อนุญาตให้ผู้ใช้ที่ล็อกอินอยู่ แก้ไขข้อมูลส่วนตัวของตนเองได้
// เช่น Bio และรูปโปรไฟล์

// 1. เรียกใช้ไฟล์ที่จำเป็น
include 'partials/header.php';
include 'db.php';

// 2. ตรวจสอบสถานะการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 3. ดึงข้อมูลปัจจุบันของผู้ใช้เพื่อนำมาแสดงในฟอร์ม
$sql = "SELECT username, email, bio, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ถ้าเกิดกรณีผิดพลาด หา user ไม่เจอ (ซึ่งไม่น่าเกิดขึ้นได้ถ้า login อยู่)
if (!$user) {
    die('<div class="alert alert-danger text-center">เกิดข้อผิดพลาด: ไม่พบข้อมูลผู้ใช้</div>');
}
?>

<!-- 
    4. Layout และฟอร์ม (ปรับปรุงให้ใช้ "กรอบเนื้อหาหลัก")
    เราลบ Card Wrapper ที่ไม่จำเป็นออก เพื่อให้หน้านี้มี Layout เหมือนหน้าอื่นๆ
-->
<div class="row justify-content-center">
    <div class="col-lg-8">

        <h2 class="text-center mb-4">แก้ไขโปรไฟล์</h2>
        
        <form action="action/edit_profile_action.php" method="POST" enctype="multipart/form-data">

            <!-- 4.1. แสดงรูปโปรไฟล์ปัจจุบันและช่องอัปโหลด -->
            <div class="text-center mb-4">
                <img src="<?php echo getUserProfileImage($user['profile_pic']); ?>" class="rounded-circle" width="150" height="150" alt="Profile Picture" style="object-fit: cover;">
            </div>
            <div class="mb-3">
                <label for="profile_pic" class="form-label">เปลี่ยนรูปโปรไฟล์ (Optional)</label>
                <input class="form-control" type="file" id="profile_pic" name="profile_pic">
                <small class="form-text text-muted">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยน</small>
            </div>

            <!-- 4.2. แสดงข้อมูลที่ไม่สามารถแก้ไขได้ -->
            <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled readonly>
            </div>

            <!-- 4.3. ช่องสำหรับแก้ไข Bio -->
            <div class="mb-3">
                <label for="bio" class="form-label">แนะนำตัว</label>
                <textarea id="bio" name="bio" class="form-control" rows="6"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>

            <!-- 4.4. ปุ่ม Submit -->
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>

    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'partials/footer.php';
?>