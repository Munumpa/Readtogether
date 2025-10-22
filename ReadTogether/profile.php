<?php
// ===================================================================
// PROFILE.PHP - หน้าโปรไฟล์ของผู้ใช้
// ===================================================================
// ไฟล์นี้แสดงข้อมูลส่วนตัวของผู้ใช้ และรายการหนังสือทั้งหมดที่ผู้ใช้คนนั้นได้เพิ่มเข้ามา

include 'partials/header.php';
include 'db.php';

// --- 1. ตรวจสอบและรับ ID ผู้ใช้จาก URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger text-center mt-5">Error: ไม่พบ ID ผู้ใช้</div>');
}
$user_id = $_GET['id'];

// --- 2. ดึงข้อมูลผู้ใช้จากตาราง users ---
$user_sql = "SELECT username, bio, profile_pic FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// ถ้าไม่เจอผู้ใช้ ให้หยุดทำงาน
if (!$user) {
    die('<div class="alert alert-danger text-center mt-5">ไม่พบผู้ใช้ที่คุณกำลังมองหา</div>');
}

// --- 3. ดึงข้อมูลหนังสือทั้งหมดที่ผู้ใช้นี้เพิ่ม จากตาราง books ---
$books_sql = "SELECT book_id, title, cover_image FROM books WHERE added_by = ? ORDER BY created_at DESC";
$books_stmt = $conn->prepare($books_sql);
$books_stmt->bind_param("i", $user_id);
$books_stmt->execute();
$books_result = $books_stmt->get_result();
?>

<!-- ======================= 4. ส่วนหัวของโปรไฟล์ (Profile Header) ======================= -->
<div class="card bg-dark text-white my-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            
            <!-- 4.1. แสดงรูปโปรไฟล์ -->
            <img src="<?php echo getUserProfileImage($user['profile_pic']); ?>" class="rounded-circle me-3" width="80" height="80" alt="Profile Picture">
            
            <!-- 4.2. แสดงชื่อและสถานะ -->
            <div class="flex-grow-1">
                <h2 class="card-title mb-0"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="card-text text-muted">สมาชิก ReadTogether</p>
            </div>
            
            <!-- 4.3. แสดงปุ่ม "แก้ไข" เฉพาะเจ้าของโปรไฟล์เท่านั้น -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                <div>
                    <a href="edit_profile.php" class="btn btn-secondary">แก้ไขโปรไฟล์</a>
                </div>
            <?php endif; ?>
        </div>
        
        <hr>

        <!-- 4.4. แสดงข้อมูลเพิ่มเติม -->
        <p><strong>แนะนำตัว:</strong></p>
        <p class="card-text"><?php echo $user['bio'] ? nl2br(htmlspecialchars($user['bio'])) : '<i class="text-muted">ยังไม่มีข้อมูลแนะนำตัว</i>'; ?></p>
        <p class="card-text mt-3"><strong>จำนวนหนังสือที่เพิ่ม:</strong> <?php echo $books_result->num_rows; ?> เล่ม</p>
    </div>
</div>

<!-- ======================= 5. ส่วนรายการหนังสือของผู้ใช้ ======================= -->
<h3 class="mt-5 mb-4">หนังสือที่เพิ่มโดย <?php echo htmlspecialchars($user['username']); ?></h3>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 mb-5">
    <?php if ($books_result->num_rows > 0): ?>
        <?php while($row = $books_result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 bg-dark text-white">
                    <a href="book_detail.php?id=<?php echo $row['book_id']; ?>">
                        <!-- (Refinement) สร้างฟังก์ชันสำหรับรูปปกหนังสือ (ดูใน functions.php) -->
                        <img src="<?php echo ($row['cover_image'] && file_exists("uploads/" . $row['cover_image'])) ? 'uploads/' . htmlspecialchars($row['cover_image']) : 'images/default.jpg'; ?>" class="card-img-top book-cover-img" alt="ปกหนังสือ">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="book_detail.php?id=<?php echo $row['book_id']; ?>"><?php echo htmlspecialchars($row["title"]); ?></a>
                        </h5>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="text-muted text-center">ผู้ใช้นี้ยังไม่ได้เพิ่มหนังสือเลย</p>
        </div>
    <?php endif; ?>
</div>

<?php
// 6. ปิดการเชื่อมต่อ statement ที่ไม่ใช้แล้ว
$user_stmt->close();
$books_stmt->close();
$conn->close();
include 'partials/footer.php';
?>