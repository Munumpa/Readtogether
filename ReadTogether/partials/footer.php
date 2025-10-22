<?php
// ===================================================================
// FOOTER.PHP - ไฟล์ส่วนท้ายของเว็บไซต์
// ===================================================================
// ไฟล์นี้จะถูกเรียกใช้เป็นไฟล์สุดท้ายในทุกหน้าที่แสดงผล
// ทำหน้าที่ปิดแท็ก HTML ที่เปิดไว้ใน header.php และเรียกใช้ไฟล์ JavaScript

        // ======================= 1. Logic การปิดกรอบเนื้อหา =======================
        // ตรวจสอบชื่อไฟล์ปัจจุบัน (ต้องใช้ Logic เดียวกันกับใน header.php)
        $current_page = basename($_SERVER['PHP_SELF']);
        
        // ถ้าหน้าปัจจุบัน "ไม่ใช่" index.php ให้ทำการปิด div 3 ชั้น
        if ($current_page != 'index.php') {
            echo '</div>'; // ปิด .card-body
            echo '</div>'; // ปิด .content-wrapper (กรอบ Card)
            echo '</div>'; // ปิด .container
        } else {
            // ถ้าเป็นหน้า index.php ให้ปิดแค่ .container
            echo '</div>'; // ปิด .container
        }
        ?>
    </main>

    <footer class="mt-auto py-3"> <!-- mt-auto: ช่วยดัน footer ลงล่างในหน้าที่เนื้อหาน้อย -->
        <div class="container">
            <!-- 2. เพิ่มข้อความลิขสิทธิ์และชื่อผู้จัดทำ -->
            <p class="text-center mb-0 ">
                &copy; <?php echo date("Y"); ?> ReadTogether | A CSD3202 Project by Nantaphong & Tanakorn
            </p>
        </div>
    </footer>

    <!-- 3. เรียกใช้ Bootstrap JavaScript Bundle -->
    <!-- ต้องวางไว้ก่อนปิด </body> เสมอ เพื่อให้หน้าเว็บโหลดโครงสร้าง HTML เสร็จก่อน -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>