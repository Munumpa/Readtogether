# ReadTogether

ReadTogether เป็นเว็บชุมชนคนรักหนังสือ เขียนด้วย PHP และ MySQL (server-side rendered).

โค้ดหลักของโปรเจ็กต์อยู่ในโฟลเดอร์ ReadTogether/ ภายใน repository นี้

สรุปสั้น ๆ
- หน้าเว็บ: สมัคร / เข้าสู่ระบบ / เพิ่มหนังสือ / ดูรายละเอียดหนังสือ / โหวต
- บทบาทของผู้พัฒนา (คุณ): พัฒนา backend ด้วย PHP, ออกแบบฐานข้อมูลเบื้องต้น, ทำ UI ด้วย Bootstrap

Tech stack
- PHP (plain PHP)
- MySQL (mysqli)
- Bootstrap (CDN)

Quick start (local)
1. Clone repo and change into the project folder:
```bash
git clone git@github.com:Munumpa/Readtogether.git
cd Readtogether/ReadTogether
```

2. Copy `.env.example` to `.env` and set your DB credentials (do NOT commit `.env`):
```bash
cp ../.env.example .env
# then edit .env and fill DB_HOST, DB_NAME, DB_USER, DB_PASS
```

3. Create database and tables (use `schema.sql` located in the repo root):
- Open your MySQL client or phpMyAdmin and run `schema.sql`.

4. Serve with PHP built-in server (for development):
```bash
php -S localhost:8000 -t .
```
Open http://localhost:8000 in your browser.

Notes
- Database config should be set in `.env` (do not commit your real credentials).
- The application stores uploaded images in `ReadTogether/uploads/` — these are user-generated files and may contain personal data. Consider removing them from the repository or regenerating demo images before sharing publicly.

