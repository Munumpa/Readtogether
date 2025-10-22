<?php
// ===================================================================
// LOGIN.PHP - หน้าเข้าสู่ระบบ
// ===================================================================
// ไฟล์นี้ทำหน้าที่แสดงฟอร์มสำหรับให้ผู้ใช้ที่มีบัญชีอยู่แล้วเข้าสู่ระบบ

// 1. เรียกใช้ส่วนหัวของเว็บ (จัดการ Session และแสดงเมนู)
include 'partials/header.php'; 
?>

<!-- 2. ใช้ Bootstrap Grid System เพื่อจัด Layout ให้อยู่กึ่งกลาง -->
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <!-- 3. ใช้ Bootstrap Card สร้างกรอบฟอร์ม -->
        <div class="card bg-dark text-white mt-5">
            <div class="card-body p-4 p-md-5">

                <h2 class="card-title text-center mb-4">เข้าสู่ระบบ</h2>
                
                <!-- 4. ส่วนแสดงข้อความแจ้งเตือน (Status & Error Messages) -->
                <!-- 4.1. แสดงข้อความ "สำเร็จ" หลังจากสมัครสมาชิก -->
                <?php if (isset($_GET['status']) && $_GET['status'] == 'register_success'): ?>
                    <div class="alert alert-success">
                        สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ
                    </div>
                <?php endif; ?>
                
                <!-- 4.2. แสดงข้อความ "ผิดพลาด" ถ้าการล็อกอินล้มเหลว -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- 5. ฟอร์มเข้าสู่ระบบ -->
                <form action="action/login_action.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้ (Username)</label>
                        <!-- required: บังคับให้ผู้ใช้ต้องกรอกข้อมูลในช่องนี้ -->
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">เข้าสู่ระบบ</button>
                    </div>

                </form>

                <!-- 6. ลิงก์สำหรับผู้ใช้ที่ยังไม่มีบัญชี -->
                <div class="text-center mt-3">
                    <p class="text-muted">ยังไม่มีบัญชี? <a href="register.php">สร้างบัญชีใหม่ที่นี่</a></p>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
// 7. เรียกใช้ส่วนท้ายของเว็บ
include 'partials/footer.php'; 
?>