<?php
// ===================================================================
// ADMIN_DASHBOARD.PHP - หน้าแดชบอร์ดสำหรับผู้ดูแลระบบ
// ===================================================================
// หน้านี้สงวนไว้สำหรับผู้ใช้ที่มี role เป็น 'admin' เท่านั้น
// ใช้สำหรับดูภาพรวมของเว็บไซต์และจัดการข้อมูลผู้ใช้กับหนังสือ

include 'partials/header.php';
include 'db.php';

// --- 1. "ยามเฝ้าประตู" - ตรวจสอบสิทธิ์การเข้าถึง ---
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die('<div class="alert alert-danger text-center mt-5">Access Denied: You do not have permission to view this page.</div>');
}

// --- 2. ดึงข้อมูลสรุป (Stats) - (Performance Refinement) ---
// เราจะใช้ SELECT COUNT(*) ซึ่งเร็วกว่าการดึงข้อมูลทั้งหมดแล้วค่อยนับ
$user_count_result = $conn->query("SELECT COUNT(user_id) as total FROM users");
$user_count = $user_count_result->fetch_assoc()['total'];

$book_count_result = $conn->query("SELECT COUNT(book_id) as total FROM books");
$book_count = $book_count_result->fetch_assoc()['total'];

$admin_count_result = $conn->query("SELECT COUNT(user_id) as total FROM users WHERE role = 'admin'");
$admin_count = $admin_count_result->fetch_assoc()['total'];


// --- 3. ดึงข้อมูลสำหรับแสดงในตาราง ---
// 3.1. ดึงข้อมูลผู้ใช้ทั้งหมด
$users_stmt = $conn->prepare("SELECT user_id, username, email, role, created_at FROM users ORDER BY user_id DESC");
$users_stmt->execute();
$all_users_result = $users_stmt->get_result();

// 3.2. ดึงข้อมูลหนังสือทั้งหมด
$books_stmt = $conn->prepare("SELECT b.book_id, b.title, b.author, u.username FROM books b JOIN users u ON b.added_by = u.user_id ORDER BY b.created_at DESC");
$books_stmt->execute();
$all_books_result = $books_stmt->get_result();
?>

<div class="center-heading mt-4">
    <h2 class="page-title">Admin Dashboard</h2>
    <p class="text-muted">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
</div>

<!-- ======================= 4. Dashboard Stats Cards ======================= -->
<div class="row mb-5">
    <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white text-center p-3">
            <div class="card-body">
                <h5 class="card-title">ผู้ใช้ทั้งหมด</h5>
                <p class="display-4 fw-bold"><?php echo $user_count; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white text-center p-3">
            <div class="card-body">
                <h5 class="card-title">หนังสือทั้งหมด</h5>
                <p class="display-4 fw-bold"><?php echo $book_count; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-dark text-white text-center p-3">
            <div class="card-body">
                <h5 class="card-title">แอดมินทั้งหมด</h5>
                <p class="display-4 fw-bold"><?php echo $admin_count; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- ======================= 5. Users Table ======================= -->
<h3 class="mb-3">จัดการผู้ใช้ (ล่าสุด)</h3>
<div class="table-responsive">
    <table class="table table-dark table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody>
            <?php while($user = $all_users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><a href="profile.php?id=<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['username']); ?></a></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-secondary'; ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                <td><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- ======================= 6. Books Table ======================= -->
<h3 class="mt-5 mb-3">จัดการหนังสือ (ล่าสุด)</h3>
<div class="table-responsive">
    <table class="table table-dark table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Added By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($book = $all_books_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $book['book_id']; ?></td>
                <td><a href="book_detail.php?id=<?php echo $book['book_id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a></td>
                <td><?php echo htmlspecialchars($book['author']); ?></td>
                <td><a href="profile.php?id=<?php echo $book['added_by'] ?? '#'; ?>"><?php echo htmlspecialchars($book['username']); ?></a></td>
                <td>
                    <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                    <a href="action/delete_book_action.php?id=<?php echo $book['book_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ADMIN ACTION: Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
// 7. ปิด statement และการเชื่อมต่อ
$users_stmt->close();
$books_stmt->close();
$conn->close();
include 'partials/footer.php';
?>