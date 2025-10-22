<?php
// ===================================================================
// INDEX.PHP - หน้าแรกของเว็บไซต์
// ===================================================================
// ไฟล์นี้เป็นหน้าแรกที่ผู้ใช้จะเห็น ทำหน้าที่แสดง Banner ต้อนรับ
// และดึงข้อมูล 5 อันดับหนังสือที่ได้รับความนิยมสูงสุดมาแสดง

// 1. เรียกใช้ไฟล์ที่จำเป็น
include 'partials/header.php';
include 'db.php';

// --- 2. ดึงข้อมูล 5 อันดับหนังสือยอดนิยม ---
// SQL Query นี้จะเชื่อมตาราง books และ votes เพื่อคำนวณคะแนนเฉลี่ย (avg_score) และจำนวนโหวต (vote_count)
// ORDER BY: เรียงลำดับจากคะแนนเฉลี่ยสูงสุดก่อน ถ้าเท่ากัน ให้ดูที่จำนวนโหวต
// LIMIT 5: จำกัดผลลัพธ์ให้แสดงแค่ 5 แถวแรก
$top_books_sql = "SELECT b.*, AVG(v.score) as avg_score, COUNT(v.vote_id) as vote_count FROM books b LEFT JOIN votes v ON b.book_id = v.book_id GROUP BY b.book_id ORDER BY avg_score DESC, vote_count DESC LIMIT 5";

// (Refinement) เปลี่ยนมาใช้ prepare() เพื่อความสอดคล้องและปลอดภัย
$top_books_stmt = $conn->prepare($top_books_sql);
$top_books_stmt->execute();
$top_books_result = $top_books_stmt->get_result();

?>

<!-- ======================= 3. Main Banner ======================= -->
<div class="main-banner">
    <!-- Banner จะอยู่นอก .container หลักที่เปิดใน header.php เพื่อให้แสดงผลเต็มความกว้าง -->
    <div class="banner-content">
        <h4><em>Welcome To</em> ReadTogether</h4>
        <h1><span>COMMUNITY</span> FOR BOOK LOVERS</h1>
        <!-- (Refinement) เปลี่ยนมาใช้คลาส .btn .btn-primary ของ Bootstrap -->
        <a href="books.php" class="btn btn-primary btn-lg mt-3">สำรวจหนังสือทั้งหมด</a>
    </div>
</div>

<!-- ======================= 4. Top 5 Books Section ======================= -->
<div class="top-books-section">
    <div class="center-heading">
        <h2 class="page-title">5 อันดับหนังสือยอดนิยม</h2>
    </div>
    
    <!-- 4.1. ใช้ Bootstrap Grid System ในการแสดงผล -->
    <!-- row-cols-*: กำหนดจำนวนคอลัมน์ในขนาดจอต่างๆ (lg=5, md=3, default=1) -->
    <!-- g-4: กำหนดระยะห่าง (gap) ระหว่างการ์ด -->
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4 mb-5">
        <?php if ($top_books_result->num_rows > 0): ?>
            <?php // วนลูปเพื่อแสดงผลหนังสือแต่ละเล่ม
            while($row = $top_books_result->fetch_assoc()): ?>
                <div class="col">
                    <!-- 4.2. ใช้ Bootstrap Card Component -->
                    <div class="card h-100 bg-dark text-white">
                        <a href="book_detail.php?id=<?php echo $row['book_id']; ?>">
                            <img src="<?php echo ($row['cover_image'] != 'default.jpg' && file_exists("uploads/" . $row['cover_image'])) ? 'uploads/' . htmlspecialchars($row['cover_image']) : 'images/default.jpg'; ?>" class="card-img-top book-cover-img" alt="ปกหนังสือ">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="book_detail.php?id=<?php echo $row['book_id']; ?>"><?php echo htmlspecialchars($row["title"]); ?></a>
                            </h5>
                            <p class="card-text text-warning">
                                ⭐ 
                                <?php 
                                if ($row['vote_count'] > 0) {
                                    // number_format(): จัดรูปแบบทศนิยมให้มี 1 ตำแหน่ง
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
        <?php else: ?>
            <div class="col-12">
                 <p class="text-center text-muted">ยังไม่มีหนังสือในระบบเลย</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// 5. เรียกใช้ส่วนท้ายของเว็บ
include 'partials/footer.php'; 
?>