<?php
// ===================================================================
// BOOKS.PHP - หน้ารายการหนังสือทั้งหมด
// ===================================================================
// ไฟล์นี้ทำหน้าที่ดึงข้อมูลหนังสือ "ทุกเล่ม" ในฐานข้อมูลออกมาแสดงผล
// ในรูปแบบ Grid Layout ที่สวยงามและ Responsive

// 1. เรียกใช้ไฟล์ที่จำเป็น
include 'partials/header.php';
include 'db.php';

// --- 2. ดึงข้อมูลหนังสือทั้งหมดพร้อมคะแนนเฉลี่ย ---
// Query นี้จะดึงหนังสือทุกเล่ม และเรียงลำดับจากคะแนนเฉลี่ยสูงสุดไปต่ำสุด
$sql = "SELECT b.*, AVG(v.score) as avg_score, COUNT(v.vote_id) as vote_count FROM books b LEFT JOIN votes v ON b.book_id = v.book_id GROUP BY b.book_id ORDER BY avg_score DESC, vote_count DESC";

// (Refinement) เปลี่ยนมาใช้ prepare() เพื่อความสอดคล้อง
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- 3. สร้างหัวข้อของหน้า -->
<div class="center-heading mt-4">
    <h2 class="page-title">หนังสือทั้งหมดในระบบ</h2>
</div>

<!-- 4. ใช้ Bootstrap Grid System ในการแสดงผล -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4 mb-5">
    <?php if ($result->num_rows > 0): ?>
        <?php // วนลูปเพื่อแสดงผลหนังสือแต่ละเล่ม
        while($row = $result->fetch_assoc()): ?>
            <div class="col">
                <!-- 4.1. ใช้ Bootstrap Card Component (h-100 ทำให้การ์ดสูงเท่ากัน) -->
                <div class="card h-100 bg-dark text-white">
                    
                    <a href="book_detail.php?id=<?php echo $row['book_id']; ?>">
                        <img src="<?php echo ($row['cover_image'] != 'default_cover.jpg' && file_exists("uploads/" . $row['cover_image'])) ? 'uploads/' . htmlspecialchars($row['cover_image']) : 'images/default_cover.jpg'; ?>" class="card-img-top book-cover-img" alt="ปกหนังสือ">
                    </a>
                    
                    <!-- d-flex flex-column: ทำให้เราสามารถใช้ mt-auto ดันของลงล่างได้ -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <a href="book_detail.php?id=<?php echo $row['book_id']; ?>"><?php echo htmlspecialchars($row["title"]); ?></a>
                        </h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($row["author"]); ?></p>
                        
                        <!-- mt-auto: คือคลาส "เวทมนตร์" ที่จะดันส่วนนี้ให้ไปอยู่ติดขอบล่างของการ์ดเสมอ -->
                        <p class="card-text mt-auto text-warning"> 
                            ⭐ 
                            <?php 
                            if ($row['vote_count'] > 0) {
                                echo number_format($row['avg_score'], 1);
                            } else {
                                echo "N/A";
                            }
                            ?>
                        </p>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>
    <?php else: // กรณีที่ไม่มีหนังสือในระบบเลย ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                ยังไม่มีหนังสือในระบบเลย! <a href="add_book.php" class="alert-link">เพิ่มเป็นคนแรกเลยสิ</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$conn->close();
include 'partials/footer.php';
?>