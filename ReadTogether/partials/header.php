<?php

// 1. เริ่มต้นการทำงานของ Session
//เพื่อให้เว็บสามารถ "จดจำ" สถานะการล็อกอินได้
session_start();

include 'functions.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReadTogether</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header>
        <!-- ======================= 5. Navbar ของ Bootstrap ======================= -->
        <!-- navbar-expand-lg: ให้เมนูขยายเต็มแถวในจอขนาดใหญ่ (lg) ขึ้นไป และพับเป็น Hamburger ในจอที่เล็กกว่า -->
        <!-- navbar-dark: ทำให้ตัวอักษรใน Navbar เป็นสีสว่าง เหมาะกับพื้นหลังสีเข้ม -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">

                <!-- 5.1. โลโก้/ชื่อเว็บไซต์ -->
                <a class="navbar-brand" href="index.php">READTOGETHER</a>

                <!-- 5.2. ปุ่ม Hamburger (สำหรับจอมือถือ) -->
                <!-- data-bs-target="#mainNavbar": คือการบอกว่าปุ่มนี้จะไปควบคุมการเปิด/ปิด <div> ที่มี id="mainNavbar" -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- 5.3. ส่วนของเมนูที่จะพับเก็บได้ -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- ms-auto: จัดให้เมนูไปอยู่ชิดขวา -->
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                        <!-- เมนูที่แสดงผลตลอดเวลา -->
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">หน้าแรก</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="books.php">รายการหนังสือ</a>
                        </li>

                        <?php // --- 5.4. จุดตรวจสอบการ Login ---
                        if (isset($_SESSION['user_id'])): ?>
                            <!-- === เมนูสำหรับ "สมาชิก" ที่ล็อกอินแล้ว === -->

                            <li class="nav-item">
                                <a class="nav-link" href="add_book.php">เพิ่มหนังสือ</a>
                            </li>

                            <?php // --- 5.5. จุดตรวจสอบ "บทบาท" (Role) ---
                                if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <!-- เมนูนี้จะแสดงให้ "แอดมิน" เห็นเท่านั้น -->
                                <li class="nav-item">
                                    <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                                </li>
                            <?php endif; ?>

                            <!-- 5.6. เมนูโปรไฟล์แบบ Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <!-- แสดงรูปโปรไฟล์เล็กๆ และเรียกใช้ฟังก์ชันที่เราสร้างไว้ -->
                                    <img src="<?php echo getUserProfileImage($_SESSION['profile_pic'] ?? null); ?>"
                                        class="rounded-circle me-2" width="30" height="30" alt="Profile">
                                    <!-- แสดงชื่อผู้ใช้ -->
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item"
                                            href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">ดูโปรไฟล์</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="action/logout.php">ออกจากระบบ</a></li>
                                </ul>
                            </li>

                        <?php else: ?>
                            <!-- === เมนูสำหรับ "แขก" ที่ยังไม่ได้ล็อกอิน === -->
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">เข้าสู่ระบบ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">สมัครสมาชิก</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="py-4"> <!-- py-4: เพิ่มระยะห่างบน-ล่างให้กับเนื้อหาหลัก -->
        <?php
        // ======================= 6. Logic การสร้างกรอบเนื้อหา =======================
        // ตรวจสอบชื่อไฟล์ปัจจุบัน
        $current_page = basename($_SERVER['PHP_SELF']);

        // ถ้าหน้าปัจจุบัน "ไม่ใช่" index.php ให้แสดงกรอบ Card
        if ($current_page != 'index.php') {
            echo '<div class="container">';
            echo '<div class="content-wrapper card bg-dark text-white">'; // ไม่ต้องมี my-4 เพราะ main มี py-4 แล้ว
            echo '<div class="card-body p-4 p-md-5">'; // p-md-5 ทำให้ padding ใหญ่ขึ้นบนจอขนาดกลางขึ้นไป
        } else {
            // ถ้าเป็นหน้า index.php ให้ใช้ container ธรรมดา (สำหรับส่วน Top Books)
            // ส่วน Banner จะอยู่นอก Container เพื่อให้แสดงผลเต็มความกว้าง
            echo '<div class="container">';
        }
        ?>