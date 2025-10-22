<?php
// ===================================================================
// EDIT_BOOK.PHP - หน้าฟอร์มสำหรับแก้ไขข้อมูลหนังสือ
// ===================================================================

// 1. เรียกใช้ไฟล์ที่จำเป็น
include 'partials/header.php';
include 'db.php';

// 2. ตรวจสอบสถานะการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=กรุณาเข้าสู่ระบบก่อน");
    exit();
}

// 3. ตรวจสอบและรับ ID หนังสือจาก URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ไม่พบ ID หนังสือ");
}
$book_id = $_GET['id'];

// 4. ดึงข้อมูลหนังสือที่ต้องการแก้ไข
$sql = "SELECT * FROM books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

// ถ้าไม่เจอหนังสือ ให้หยุดทำงาน
if (!$book) {
    die('<div class="alert alert-danger text-center">ไม่พบหนังสือที่คุณกำลังมองหา!</div>');
}

// 5. ตรวจสอบสิทธิ์ความเป็นเจ้าของ หรือสิทธิ์แอดมิน
if ($book['added_by'] != $_SESSION['user_id'] && $_SESSION['role'] != 'admin') {
    die('<div class="alert alert-danger text-center">คุณไม่มีสิทธิ์แก้ไขหนังสือเล่มนี้</div>');
}
?>

<!-- 6. Layout และฟอร์ม (ปรับปรุงให้รองรับการเปลี่ยนรูป) -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <h2 class="text-center mb-4">แก้ไขข้อมูลหนังสือ</h2>
        
        <form action="action/edit_book_action.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">

            <!-- 6.1. แสดงรูปปกปัจจุบัน -->
            <div class="text-center mb-4">
                <img src="<?php echo ($book['cover_image'] && file_exists("uploads/" . $book['cover_image'])) ? 'uploads/' . htmlspecialchars($book['cover_image']) : 'images/default.jpg'; ?>" class="img-fluid rounded shadow-sm" style="max-height: 300px;" alt="Current Cover">
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- ข้อมูลหนังสือ (Title, Author, Desc) -->
                    <div class="mb-3">
                        <label for="title" class="form-label">ชื่อหนังสือ</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">ผู้แต่ง</label>
                        <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- ช่องสำหรับเปลี่ยนรูปปกใหม่ -->
                    <div class="mb-3">
                        <label for="cover_image" class="form-label">เปลี่ยนปกหนังสือ (Optional)</label>
                        <input class="form-control" type="file" id="cover_image" name="cover_image">
                        <small class="form-text text-muted">ปล่อยว่างไว้หากไม่ต้องการเปลี่ยน</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">เรื่องย่อ</label>
                <textarea class="form-control" id="description" name="description" rows="8"><?php echo htmlspecialchars($book['description']); ?></textarea>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include 'partials/footer.php';
?>
