<?php
// ===================================================================
// REGISTER.PHP - หน้าสมัครสมาชิก
// ===================================================================
// ไฟล์นี้ทำหน้าที่แสดงฟอร์มสำหรับให้ผู้ใช้ใหม่สร้างบัญชี

// 1. เรียกใช้ส่วนหัวของเว็บ (จัดการ Session และแสดงเมนู)
include 'partials/header.php'; 
?>

<!-- 2. ใช้ Bootstrap Grid System เพื่อจัด Layout ให้อยู่กึ่งกลาง -->
<!-- justify-content-center: จัดคอลัมน์ให้อยู่ตรงกลางแนวนอน -->
<!-- col-lg-6 col-md-8: กำหนดความกว้างของคอลัมน์ในขนาดจอต่างๆ -->
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">

        <!-- 3. ใช้ Bootstrap Card สร้างกรอบฟอร์มที่สวยงาม -->
        <!-- bg-dark, text-white: คลาสสำหรับธีมสีเข้ม -->
        <!-- mt-5: สร้างระยะห่างด้านบน (Margin Top) -->
        <div class="card bg-dark text-white mt-5">
            <div class="card-body p-4 p-md-5">

                <h2 class="card-title text-center mb-4">สร้างบัญชีผู้ใช้ใหม่</h2>
                
                <!-- 4. ส่วนแสดงข้อความแจ้งเตือน (Error) -->
                <!-- โค้ดนี้จะทำงานก็ต่อเมื่อมีการส่ง 'error' กลับมาใน URL -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- 5. ฟอร์มสมัครสมาชิก -->
                <!-- action: ระบุไฟล์หลังบ้านที่จะประมวลผลข้อมูล -->
                <form action="action/register_action.php" method="POST">
                    
                    <!-- แต่ละช่อง Input จะถูกห่อด้วย <div class="mb-3"> เพื่อสร้างระยะห่างที่เหมาะสม -->
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้ (Username)</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">อีเมล</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <!-- d-grid: ทำให้ปุ่มขยายเต็มความกว้าง -->
                    <div class="d-grid mt-4">
                        <!-- 6. (Refinement) เปลี่ยน .btn-primary เป็น .btn-secondary หรือ .btn-danger เพื่อให้เข้ากับธีม -->
                        <!-- หรือสร้างคลาส .btn-theme ของเราเองใน CSS -->
                        <button type="submit" class="btn btn-primary btn-lg">สมัครสมาชิก</button>
                    </div>

                </form>

                <!-- 7. ลิงก์สำหรับผู้ใช้ที่มีบัญชีอยู่แล้ว -->
                <div class="text-center mt-3">
                    <p class="text-muted">เป็นสมาชิกอยู่แล้ว? <a href="login.php">เข้าสู่ระบบที่นี่</a></p>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
// 8. เรียกใช้ส่วนท้ายของเว็บ
include 'partials/footer.php'; 
?>