<?php
// README Documentation for Marathon Registration System
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marathon Registration System - Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .code { background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace; }
        .file-tree { background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Marathon Registration Website</h1>
        <p class="lead">เว็บไซต์ลงทะเบียนสมัครวิ่งมาราธอน พร้อมระบบจัดการข้อมูลผู้สมัครและการชำระเงิน</p>

        <h2 class="mt-5">คุณสมบัติหลัก</h2>
        
        <h3 class="mt-4">🏃‍♂️ สำหรับผู้สมัคร</h3>
        <ul>
            <li><strong>ลงทะเบียนออนไลน์</strong> - สมัครวิ่งผ่านเว็บไซต์ได้ง่ายๆ</li>
            <li><strong>เลือกประเภทการแข่ง</strong> - Mini Marathon, Half Marathon, Full Marathon</li>
            <li><strong>คำนวณราคาอัตโนมัติ</strong> - ราคาตามอายุและสถานะ (ปกติ/ผู้สูงอายุ/ผู้พิการ)</li>
            <li><strong>เลือกวิธีรับของที่ระลึก</strong> - รับที่งาน หรือ จัดส่ง EMS</li>
            <li><strong>ตรวจสอบสถานะ</strong> - ตรวจสอบการสมัครด้วยเลขบัตรประชาชนหรืออีเมล</li>
        </ul>

        <h3 class="mt-4">💻 คุณสมบัติทางเทคนิค</h3>
        <ul>
            <li><strong>Pure PHP</strong> - ไฟล์ทั้งหมดเป็น PHP</li>
            <li><strong>Responsive Design</strong> - ใช้งานได้ทุกอุปกรณ์</li>
            <li><strong>Modern UI/UX</strong> - ออกแบบสวยงาม โทนสีขาวดำ</li>
            <li><strong>Database Integration</strong> - เชื่อมต่อฐานข้อมูล MySQL</li>
            <li><strong>Demo Mode</strong> - ทำงานได้โดยไม่ต้องมีฐานข้อมูล</li>
        </ul>

        <h2 class="mt-5">โครงสร้างไฟล์</h2>
        <div class="file-tree">
marathon-registration/<br>
├── index.php                           # หน้าเว็บหลัก<br>
├── config.php                          # การตั้งค่าฐานข้อมูล<br>
├── functions.php                       # ฟังก์ชันสำหรับจัดการข้อมูล<br>
├── api.php                            # PHP API สำหรับ Backend<br>
├── styles.php                         # CSS Styles<br>
├── script.php                         # JavaScript<br>
├── marathon_registration_schema.php    # โครงสร้างฐานข้อมูล<br>
├── sample_data.php                    # ข้อมูลตัวอย่าง<br>
├── useful_queries.php                 # คำสั่ง SQL ที่มีประโยชน์<br>
├── demo.php                           # หน้าทดสอบ<br>
└── readme.php                         # คู่มือการใช้งาน
        </div>

        <h2 class="mt-5">การติดตั้งและใช้งาน</h2>

        <h3 class="mt-4">1. ติดตั้งฐานข้อมูล (��้าต้องการ)</h3>
        <div class="code">
-- สร้างฐานข้อมูล<br>
CREATE DATABASE marathon_registration;<br><br>
-- เข้าไปดูโครงสร้างตาราง<br>
http://localhost:8000/marathon_registration_schema.php<br><br>
-- เข้าไปดูข้อมูลตัวอย่าง<br>
http://localhost:8000/sample_data.php
        </div>

        <h3 class="mt-4">2. ตั้งค่า Web Server</h3>
        <div class="code">
# เริ่มต้น PHP Development Server<br>
php -S localhost:8000 index.php<br><br>
# หรือใช้ XAMPP/WAMP และวางไฟล์ในโฟลเดอร์ htdocs
        </div>

        <h3 class="mt-4">3. แก้ไขการตั้งค่า (ถ้าจำเป็น)</h3>
        <div class="code">
// ในไฟล์ config.php<br>
define('DB_HOST', 'localhost');<br>
define('DB_NAME', 'marathon_registration');<br>
define('DB_USER', 'root');<br>
define('DB_PASS', '');
        </div>

        <h2 class="mt-5">ประเภทการแข่งขันและราคา</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ประเภท</th>
                        <th>ระยะทาง</th>
                        <th>ราคาปกติ</th>
                        <th>ราคาผู้สูงอายุ</th>
                        <th>ราคาผู้พิการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mini Marathon</td>
                        <td>10.5 กม.</td>
                        <td>800 บาท</td>
                        <td>600 บาท</td>
                        <td>400 บาท</td>
                    </tr>
                    <tr>
                        <td>Half Marathon</td>
                        <td>21.1 กม.</td>
                        <td>1,200 บาท</td>
                        <td>900 บาท</td>
                        <td>600 บาท</td>
                    </tr>
                    <tr>
                        <td>Full Marathon</td>
                        <td>42.2 กม.</td>
                        <td>1,800 บาท</td>
                        <td>1,350 บาท</td>
                        <td>900 บาท</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-5">วิธีการจัดส่ง</h2>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ประเภท</th>
                        <th>ค่าใช้จ่าย</th>
                        <th>รายละเอียด</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>รับที่งาน</td>
                        <td>ฟรี</td>
                        <td>Central World ชั้น G</td>
                    </tr>
                    <tr>
                        <td>EMS ปกติ</td>
                        <td>50 บาท</td>
                        <td>ส่งถึงบ้าน 3-5 วัน</td>
                    </tr>
                    <tr>
                        <td>EMS Express</td>
                        <td>150 บาท</td>
                        <td>ส่งถึงบ้าน 1-2 วัน</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="mt-5">การทดสอบ</h2>
        <ul>
            <li><strong>หน้าหลัก:</strong> <a href="index.php">index.php</a></li>
            <li><strong>หน้าทดสอบ:</strong> <a href="demo.php">demo.php</a></li>
            <li><strong>API:</strong> <a href="api.php">api.php</a></li>
            <li><strong>Queries:</strong> <a href="useful_queries.php">useful_queries.php</a></li>
        </ul>

        <h3 class="mt-4">ข้อมูลทดสอบ</h3>
        <ul>
            <li>เลขบัตรประชาชน: <code>1234567890123</code></li>
            <li>อีเมล: <code>somchai@email.com</code></li>
        </ul>

        <h2 class="mt-5">การปรับแต่งและพัฒนาต่อ</h2>

        <h3 class="mt-4">เพิ่มประเภทการแข่งใหม่</h3>
        <ol>
            <li>เพิ่มข้อมูลในตาราง <code>RACE_CATEGORY</code></li>
            <li>เพิ่ม <code>AGE_GROUP</code> และ <code>PRICE_RATE</code> ที่เกี่ยวข้อง</li>
            <li>อัปเดตข้อมูลตัวอย่างในไฟล์ <code>config.php</code></li>
        </ol>

        <h3 class="mt-4">เพิ่มระบบชำระเงินออนไลน์</h3>
        <ul>
            <li>เชื่อมต่อกับ Payment Gateway (เช่น Omise, 2C2P)</li>
            <li>เพิ่ม webhook สำหรับรับการแจ้งเตือนการชำระเงิน</li>
            <li>อัปเดตสถานะการชำระเงินอัตโนมัติ</li>
        </ul>

        <h2 class="mt-5">การสนับสนุน</h2>
        <p>หากมีปัญหาหรือข้อสงสัย สามารถติดต่อได้ที่:</p>
        <ul>
            <li>Email: support@marathonregistration.com</li>
            <li>Tel: 02-123-4567</li>
        </ul>

        <h2 class="mt-5">License</h2>
        <p>MIT License - ใช้งานได้อย่างอิสระ</p>

        <div class="mt-5 text-center">
            <a href="index.php" class="btn btn-primary btn-lg">← กลับไปหน้าหลัก</a>
        </div>
    </div>
</body>
</html>
<?php
}
?>