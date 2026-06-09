# 🚗 Carwash Queue System (XAMPP-ready)

ระบบเรียกคิวล้างรถ — **deploy ง่าย** เพียงคัดลอกโฟลเดอร์ลง `htdocs` ของ XAMPP

## 🧰 Stack
- **PHP 8.x** (vanilla, ไม่ใช้ Composer / framework)
- **MySQL 8.x / MariaDB** (มากับ XAMPP)
- **HTML + CSS + Vanilla JS** (โหลด jQuery จาก CDN)
- **Realtime แบบ Polling** (fetch API ทุก 3 วินาที — ไม่ต้องตั้ง WebSocket server)

> ทำไมไม่ใช้ React / WebSocket? เพราะอยากให้ขึ้นโฮสเสร็จในขั้นตอนเดียว: copy → import SQL → ใช้งานได้ ไม่ต้อง `npm run build`, ไม่ต้องเปิด port WebSocket, รองรับ Shared Hosting ทั่วไปด้วย

## 📂 Project Structure

```
carwash-queue-system/         <- วางทั้งโฟลเดอร์ใน C:\xampp\htdocs\
├── index.php                 # หน้าแรกลูกค้า (จองคิว)
├── board.php                 # จอแสดงคิว realtime (ติดที่ร้าน)
├── my-queue.php              # ดูสถานะคิวของฉัน
│
├── admin/                    # หลังบ้าน
│   ├── login.php             # ฟอร์ม login
│   ├── logout.php
│   ├── index.php             # Dashboard
│   ├── queue.php             # จัดการคิว (เรียก/เริ่ม/เสร็จ/ยกเลิก)
│   ├── services.php          # จัดการบริการ + ราคา
│   ├── users.php             # จัดการผู้ใช้
│   └── reports.php           # รายงานรายวัน/เดือน
│
├── api/                      # AJAX endpoints (return JSON)
│   ├── book.php              # POST: จองคิว
│   ├── queue-list.php        # GET: คิวปัจจุบันทั้งหมด (สำหรับ polling)
│   ├── queue-status.php      # GET: สถานะคิวเฉพาะ ?number=A012
│   ├── update-status.php     # POST: admin เปลี่ยนสถานะ
│   └── dashboard-stats.php   # GET: สถิติสำหรับ dashboard
│
├── includes/                 # โค้ดร่วม include ทุกหน้า
│   ├── db.php                # เปิด PDO connection
│   ├── auth.php              # ตรวจ session admin
│   ├── functions.php         # generate queue number, format date
│   ├── header.php            # navbar
│   └── footer.php
│
├── config/
│   └── config.php            # ตั้ง DB credentials, base URL ที่นี่ที่เดียว
│
├── database/
│   ├── schema.sql            # สำหรับ import เข้า phpMyAdmin
│   └── seed.sql              # ข้อมูลตัวอย่าง + admin เริ่มต้น
│
├── assets/
│   ├── css/style.css
│   ├── js/main.js            # polling logic
│   └── img/
│
├── uploads/                  # ไฟล์อัปโหลด (สลิป ฯลฯ)
└── .htaccess                 # ป้องกันเข้า /includes /config โดยตรง
```

## 🚀 ติดตั้งบน XAMPP (3 ขั้น)

### 1. คัดลอกโฟลเดอร์
```
คัดลอก carwash-queue-system\  ->  C:\xampp\htdocs\
```

### 2. Import ฐานข้อมูล
- เปิด XAMPP Control Panel → Start `Apache` + `MySQL`
- เข้า http://localhost/phpmyadmin
- กด **New** → ตั้งชื่อ DB: `carwash_queue` → Create
- เลือก DB → แท็บ **Import** → เลือก `database/schema.sql` → Go
- Import `database/seed.sql` ซ้ำอีกครั้ง (เพิ่ม admin + บริการตัวอย่าง)

### 3. ตั้งค่าใน `config/config.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'carwash_queue');
define('DB_USER', 'root');
define('DB_PASS', '');                    // XAMPP default
define('BASE_URL', 'http://localhost/carwash-queue-system');
```

เปิด: **http://localhost/carwash-queue-system/**

## 🔑 Default Admin
- Username: `admin`
- Password: `admin1234`
- ⚠️ **เปลี่ยนรหัสทันที**ที่ `admin/users.php` หลังเข้าครั้งแรก

## ✨ Features
- ✅ ลูกค้าจองคิวออนไลน์ (เลือกบริการ, กรอกทะเบียน)
- ✅ รับเลขคิวอัตโนมัติ (เช่น A001, A002 ...)
- ✅ จอแสดงคิว realtime (โหลดทุก 3 วิ ไม่ต้องรีเฟรช)
- ✅ ลูกค้าดูสถานะคิวตัวเองได้
- ✅ Admin login + จัดการคิวทั้งระบบ
- ✅ จัดการบริการ + ราคา
- ✅ รายงานรายได้รายวัน/รายเดือน

## 🌐 Deploy ขึ้นโฮสจริง (Shared Hosting)
1. **Upload ผ่าน FTP/cPanel File Manager** → วางใน `public_html/`
2. สร้าง MySQL database ใน cPanel → import `schema.sql` + `seed.sql`
3. แก้ `config/config.php` ให้ตรงกับ DB ของโฮส
4. แก้ `BASE_URL` เป็นโดเมนจริง เช่น `https://yourdomain.com`
5. เสร็จ — ไม่ต้อง build, ไม่ต้อง install dependencies

## 🔒 Security Checklist
- เปลี่ยนรหัส admin เริ่มต้น
- ตั้งค่าให้โฟลเดอร์ `uploads/` ไม่รัน PHP (มี `.htaccess` ให้แล้ว)
- ใช้ HTTPS เมื่อ deploy จริง
- เปลี่ยน `DB_USER` ให้ไม่ใช่ root บนโฮสจริง

## 📝 License
MIT
