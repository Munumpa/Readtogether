<?php
// ===================================================================
// BOOK_DETAIL.PHP - หน้ารายละเอียดหนังสือ
// ===================================================================
// นี่คือหน้าที่ซับซ้อนที่สุด ทำหน้าที่แสดงข้อมูลเจาะลึกของหนังสือหนึ่งเล่ม
// รวมถึงระบบโหวต, ระบบคอมเมนต์, และปุ่มจัดการต่างๆ

include 'partials/header.php';
include 'db.php';

// --- 1. ตรวจสอบและรับ ID หนังสือจาก URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div class="alert alert-danger text-center mt-5">Error: ไม่พบ ID หนังสือ</div>');
}
$book_id = $_GET['id'];

// --- 2. ดึงข้อมูลหลักของหนังสือและคะแนนเฉลี่ย ---
$sql_book = "SELECT b.*, AVG(v.score) as avg_score, COUNT(v.vote_id) as vote_count FROM books b LEFT JOIN votes v ON b.book_id = v.book_id WHERE b.book_id = ? GROUP BY b.book_id";
$stmt_book = $conn->prepare($sql_book);
$stmt_book->bind_param("i", $book_id);
$stmt_book->execute();
$result_book = $stmt_book->get_result();
$book = $result_book->fetch_assoc();

// ถ้าไม่เจอหนังสือ ให้หยุดทำงานทันที
if (!$book) {
    die('<div class="alert alert-danger text-center mt-5">ไม่พบข้อมูลหนังสือที่คุณกำลังมองหา</div>');
}

// --- 3. ดึงข้อมูลคอมเมนต์ทั้งหมดของหนังสือเล่มนี้ ---
$sql_comments = "SELECT c.*, u.username, u.profile_pic FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.book_id = ? ORDER BY c.created_at DESC";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("i", $book_id);
$stmt_comments->execute();
$comments_result = $stmt_comments->get_result();

// --- 4. ดึงคะแนนโหวต "ปัจจุบัน" ของผู้ใช้ที่กำลังล็อกอินอยู่ ---
$user_vote = null; // กำหนดค่าเริ่มต้นเป็น null
if (isset($_SESSION['user_id'])) {
    $sql_user_vote = "SELECT score FROM votes WHERE book_id = ? AND user_id = ?";
    $stmt_user_vote = $conn->prepare($sql_user_vote);
    $stmt_user_vote->bind_param("ii", $book_id, $_SESSION['user_id']);
    $stmt_user_vote->execute();
    $result_user_vote = $stmt_user_vote->get_result();
    if ($result_user_vote->num_rows > 0) {
        $user_vote = $result_user_vote->fetch_assoc()['score'];
    }
    $stmt_user_vote->close(); // (Bug Fix) ปิด statement ที่ไม่ใช้แล้ว
}
?>

<!-- ======================= 5. โครงสร้าง Layout 2 คอลัมน์ ======================= -->
<div class="row mt-4 mb-5">

    <!-- === 5.1. คอลัมน์ซ้าย: รูปปก และ ปุ่มจัดการ === -->
    <div class="col-md-4 mb-4">
        <img src="<?php echo ($book['cover_image'] != 'default_cover.jpg' && file_exists("uploads/" . $book['cover_image'])) ? 'uploads/' . htmlspecialchars($book['cover_image']) : 'images/default_cover.jpg'; ?>" class="img-fluid rounded shadow-sm" alt="ปกหนังสือ">
        
        <!-- (UI Refinement) ย้ายปุ่มแก้ไข/ลบมาไว้ที่นี่ -->
        <div class="admin-actions mt-3">
            <?php if ((isset($_SESSION['user_id']) && $_SESSION['user_id'] == $book['added_by']) || (isset($_SESSION['role']) && $_SESSION['role'] == 'admin')): ?>
                <div class="d-grid gap-2">
                    <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-secondary">แก้ไขหนังสือ</a>
                    <a href="action/delete_book_action.php?id=<?php echo $book['book_id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหนังสือเล่มนี้?');">ลบหนังสือ</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- === 5.2. คอลัมน์ขวา: ข้อมูลทั้งหมด === -->
    <div class="col-md-8">
        <h2><?php echo htmlspecialchars($book['title']); ?></h2>
        <p class="text-muted">โดย <?php echo htmlspecialchars($book['author']); ?></p>

        <div class="score-display mb-3">
            <strong class="text-warning">⭐ <?php echo ($book['vote_count'] > 0) ? number_format($book['avg_score'], 1) : "N/A"; ?></strong>
            <span class='text-muted small'>(จาก <?php echo $book['vote_count']; ?> โหวต)</span>
        </div>

        <h4 class="mt-4">ให้คะแนน</h4>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="action/vote_action.php" method="POST" class="vote-form">
                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                <div class="star-rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?php echo $i; ?>" name="score" value="<?php echo $i; ?>" <?php if ($user_vote == $i) echo 'checked'; ?>>
                        <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> ดาว">★</label>
                    <?php endfor; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">ส่งคะแนน</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">เข้าสู่ระบบ</a> เพื่อให้คะแนนหนังสือเล่มนี้</p>
        <?php endif; ?>

        <h4 class="mt-4">เรื่องย่อ</h4>
        <p class="book-description"><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
    </div>
</div>

<!-- ======================= 6. ส่วนคอมเมนต์ ======================= -->
<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-body p-4 p-md-5">
                <h3 class="card-title mb-4">แสดงความคิดเห็น</h3>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="action/comment_action.php" method="POST" class="mb-5">
                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                        <div class="mb-2">
                            <textarea name="content" class="form-control" rows="3" required placeholder="เขียนความคิดเห็นของคุณที่นี่..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">ส่งความคิดเห็น</button>
                    </form>
                <?php else: ?>
                    <p class="mb-5"><a href="login.php">เข้าสู่ระบบ</a> เพื่อแสดงความคิดเห็น</p>
                <?php endif; ?>
                <h4 class="mb-3">ความคิดเห็นทั้งหมด (<?php echo $comments_result->num_rows; ?>)</h4>
                <div class="comments-section">
                    <?php if ($comments_result->num_rows > 0): ?>
                        <?php while ($comment = $comments_result->fetch_assoc()): ?>
                            <div class="comment-item py-3 border-bottom border-secondary">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <a href="profile.php?id=<?php echo $comment['user_id']; ?>">
                                            <img src="<?php echo getUserProfileImage($comment['profile_pic']); ?>" class="rounded-circle" width="50" height="50" alt="<?php echo htmlspecialchars($comment['username']); ?>">
                                        </a>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">
                                            <a href="profile.php?id=<?php echo $comment['user_id']; ?>" class="fw-bold text-white"><?php echo htmlspecialchars($comment['username']); ?></a>
                                            <small class="text-muted"> - <?php echo date("d M Y, H:i", strtotime($comment['created_at'])); ?></small>
                                        </p>
                                        <p class="mb-0 text-light"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีความคิดเห็นสำหรับหนังสือเล่มนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// --- 7. (Bug Fix) ปิดการเชื่อมต่อทั้งหมด ---
$stmt_book->close();
$stmt_comments->close();
$conn->close();
include 'partials/footer.php';
?>