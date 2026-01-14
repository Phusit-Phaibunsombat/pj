<?php
require_once 'functions.php';

// Handle form submissions
$message = '';
$messageType = '';
$registration = null;

if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'register':
                $result = registerParticipant($_POST);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                if ($result['success']) {
                    $message .= "<br><strong>หมายเลขผู้เข้าแข่งขัน:</strong> " . $result['bib_number'];
                }
                break;
                
            case 'check_status':
                $registration = checkRegistrationStatus($_POST['search']);
                if (!$registration) {
                    $message = 'ไม่พบข้อมูลการสมัคร กรุณาตรวจสอบเลขบัตรประชาชน หรือ อีเมลอีกครั้ง';
                    $messageType = 'warning';
                }
                break;
        }
    }
}

$categories = getCategories();
$priceRates = getPriceRates();
$shippingOptions = getShippingOptions();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียนวิ่งมาราธอน | Marathon Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.php" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-running"></i> Marathon 2026</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">หน้าแรก</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">ประเภทการแข่ง</a></li>
                    <li class="nav-item"><a class="nav-link" href="#register">ลงทะเบียน</a></li>
                    <li class="nav-item"><a class="nav-link" href="#check-status">ตรวจสอบสถานะ</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show m-0" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">Bangkok Marathon 2026</h1>
                    <p class="lead text-white mb-4">
                        ร่วมวิ่งมาราธอนครั้งยิ่งใหญ่ในใจกลางกรุงเทพฯ พร้อมรับประสบการณ์ที่ไม่เหมือนใคร
                    </p>
                    <div class="mb-4">
                        <span class="badge bg-light text-dark fs-6 me-2">
                            <i class="fas fa-calendar"></i> 15 มีนาคม 2026
                        </span>
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-map-marker-alt"></i> ลุมพินีปาร์ค
                        </span>
                    </div>
                    <a href="#register" class="btn btn-warning btn-lg px-5 py-3">
                        <i class="fas fa-user-plus"></i> ลงทะเบียนเลย
                    </a>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <i class="fas fa-running display-1 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Race Categories -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">ประเภทการแข่งขัน</h2>
            <div class="row">
                <?php 
                $icons = ['fas fa-walking', 'fas fa-running', 'fas fa-medal'];
                $colors = ['success', 'warning', 'danger'];
                foreach ($categories as $index => $category): 
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card category-card h-100 shadow">
                        <div class="card-body text-center p-4">
                            <div class="category-icon mb-3">
                                <i class="<?php echo $icons[$index]; ?>"></i>
                            </div>
                            <h4 class="card-title fw-bold"><?php echo $category['name']; ?></h4>
                            <span class="badge bg-dark text-white fs-6 mb-3">
                                <?php echo $category['distance_km']; ?> กม.
                            </span>
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> เริ่ม <?php echo substr($category['start_time'], 0, 5); ?> น.<br>
                                    <i class="fas fa-hourglass-half"></i> เวลาจำกัด <?php echo substr($category['time_limit'], 0, 5); ?> ชม.
                                </small>
                            </div>
                            <p class="card-text"><?php echo $category['giveaway_type']; ?></p>
                            <div class="fw-bold">
                                เริ่มต้น <?php echo number_format($priceRates[$category['id']]['Disabled']); ?> บาท
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Registration Form -->
    <section id="register" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0"><i class="fas fa-user-plus"></i> ลงทะเบียนสมัครวิ่ง</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="register">
                                
                                <h5 class="mb-3">ข้อมูลส่วนตัว</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ชื่อ *</label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">นามสกุล *</label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">วันเกิด *</label>
                                        <input type="date" class="form-control" name="date_of_birth" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เพศ *</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="">เลือกเพศ</option>
                                            <option value="M">ชาย</option>
                                            <option value="F">หญิง</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลขบัตรประชาชน *</label>
                                        <input type="text" class="form-control" name="citizen_id" maxlength="13" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เบอร์โทรศัพท์ *</label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">อีเมล *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ที่อยู่ *</label>
                                    <textarea class="form-control" name="address" rows="3" required></textarea>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_disabled" value="1">
                                        <label class="form-check-label">
                                            ผู้พิการ (ได้รับส่วนลดพิเศษ)
                                        </label>
                                    </div>
                                </div>

                                <h5 class="mb-3">ข้อมูลการแข่งขัน</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ประเภทการแข่ง *</label>
                                        <select class="form-select" name="category" required>
                                            <option value="">เลือกประเภทการแข่ง</option>
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo $category['name']; ?> (<?php echo $category['distance_km']; ?> กม.)
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ไซส์เสื้อ *</label>
                                        <select class="form-select" name="shirt_size" required>
                                            <option value="">เลือกไซส์เสื้อ</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">วิธีรับของที่ระลึก *</label>
                                    <select class="form-select" name="shipping" required>
                                        <option value="">เลือกวิธีรับของที่ระลึก</option>
                                        <?php foreach ($shippingOptions as $option): ?>
                                        <option value="<?php echo $option['id']; ?>">
                                            <?php echo $option['detail']; ?> - <?php echo $option['cost'] == 0 ? 'ฟรี' : number_format($option['cost']) . ' บาท'; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check"></i> ยืนยันการลงทะเบียน
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Check Status -->
    <section id="check-status" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header text-white">
                            <h4 class="mb-0"><i class="fas fa-search"></i> ตรวจสอบสถานะการสมัคร</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="check_status">
                                <div class="mb-3">
                                    <label class="form-label">เลขบัตรประชาชน หรือ อีเมล</label>
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="กรอกเลขบัตรประชาชน หรือ อีเมล" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search"></i> ค้นหา
                                    </button>
                                </div>
                            </form>
                            
                            <?php if ($registration): ?>
                            <div class="mt-4">
                                <div class="card status-card status-<?php echo strtolower($registration['reg_status']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title">พบข้อมูลการสมัคร</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>ชื่อ:</strong> <?php echo $registration['first_name'] . ' ' . $registration['last_name']; ?></p>
                                                <p><strong>หมายเลขผู้เข้าแข่งขัน:</strong> <?php echo $registration['bib_number']; ?></p>
                                                <p><strong>ประเภทการแข่ง:</strong> <?php echo $registration['category_name']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>วันที่สมัคร:</strong> <?php echo formatThaiDate($registration['reg_date']); ?></p>
                                                <p><strong>จำนวนเงิน:</strong> <?php echo number_format($registration['total_amount']); ?> บาท</p>
                                                <p><strong>สถานะ:</strong> 
                                                    <span class="badge <?php echo $registration['reg_status'] == 'Paid' ? 'bg-dark text-white' : 'bg-secondary text-white'; ?>">
                                                        <?php echo $registration['reg_status'] == 'Paid' ? 'ชำระเงินแล้ว' : 'รอชำระเงิน'; ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Bangkok Marathon 2026</h5>
                    <p>งานวิ่งมาราธอนที่ยิ่งใหญ่ที่สุดในประเทศไทย</p>
                </div>
                <div class="col-md-6">
                    <h5>ติดต่อเรา</h5>
                    <p><i class="fas fa-phone"></i> 02-123-4567<br>
                    <i class="fas fa-envelope"></i> info@bangkokmarathon.com</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.php"></script>
</body>
</html>