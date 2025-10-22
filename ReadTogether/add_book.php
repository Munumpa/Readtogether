<?php
// ===================================================================
// ADD_BOOK.PHP - หน้าฟอร์มสำหรับเพิ่มหนังสือใหม่
// ===================================================================

// 1. เรียกใช้ส่วนหัวของเว็บ (จัดการ Session และแสดงเมนู)
// เราวางโค้ดนี้ไว้บนสุดเสมอ
include 'partials/header.php';

// 2. "ยามเฝ้าประตู" - ตรวจสอบสถานะการล็อกอิน
// โค้ดส่วนนี้จะทำงานหลังจาก header.php ได้เรียก session_start() แล้ว
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้ล็อกอิน ให้ส่งกลับไปหน้า login พร้อมข้อความแจ้งเตือน
    header("Location: login.php?error=กรุณาเข้าสู่ระบบก่อนเพิ่มหนังสือ");
    exit(); // หยุดการทำงานของสคริริปต์ทันที
}

// ถ้าผ่านการตรวจสอบมาได้ โค้ด HTML ด้านล่างจะถูกแสดงผล
?>

<!-- 
    3. Layout และฟอร์ม (ใช้ Bootstrap)
    เราไม่จำเป็นต้องใส่ <div class="card..."> ที่นี่อีกแล้ว
    เพราะ "กรอบเนื้อหา" ได้ถูกสร้างไว้ใน header.php และ footer.php แล้ว
    ทำให้หน้านี้มี Layout ที่สอดคล้องกับหน้าอื่นๆ โดยอัตโนมัติ
-->
<div class="row justify-content-center">
    <div class="col-lg-10"> <!-- ขยายความกว้างเล็กน้อยเพื่อให้ฟอร์มดูไม่แคบไป -->

        <h2 class="text-center mb-4">เพิ่มหนังสือเล่มใหม่เข้าระบบ</h2>
        
        <form action="action/add_book_action.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="title" class="form-label">ชื่อหนังสือ</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="mb-3">
                <label for="author" class="form-label">ผู้แต่ง</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">เรื่องย่อ</label>
                <textarea class="form-control" id="description" name="description" rows="5"></textarea>
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">ปกหนังสือ (ไฟล์ JPG, PNG, GIF)</label>
                <!-- Bootstrap จะจัดสไตล์ให้ input type="file" สวยงามขึ้นโดยอัตโนมัติ -->
                <input class="form-control" type="file" id="cover_image" name="cover_image">
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">ยืนยันการเพิ่มหนังสือ</button>
            </div>

        </form>

    </div>
</div>

<?php
// 4. เรียกใช้ส่วนท้ายของเว็บ (จะทำการปิด "กรอบเนื้อหา" ให้เราโดยอัตโนมัติ)
include 'partials/footer.php';
?>