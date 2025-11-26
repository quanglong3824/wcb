<?php
session_start();

// Xác định base path
$basePath = './';

// Kiểm tra xem người dùng đã đăng nhập chưa
$isLoggedIn = isset($_SESSION['user_id']);

// Nếu chưa đăng nhập, chuyển đến trang login
if (!$isLoggedIn) {
    header('Location: auth/login.php');
    exit;
}

// Set page title
$pageTitle = 'Dashboard - Welcome Board System';

// Include header
include 'includes/header.php';

// Include sidebar
include 'includes/sidebar.php';

// Thống kê mẫu (sau này sẽ lấy từ database)
$stats = [
    [
        'icon' => 'fas fa-tv',
        'value' => '12',
        'label' => 'Tổng số TV',
        'color' => 'blue'
    ],
    [
        'icon' => 'fas fa-check-circle',
        'value' => '10',
        'label' => 'TV đang hoạt động',
        'color' => 'green'
    ],
    [
        'icon' => 'fas fa-images',
        'value' => '45',
        'label' => 'Nội dung WCB',
        'color' => 'orange'
    ],
    [
        'icon' => 'fas fa-calendar-check',
        'value' => '8',
        'label' => 'Lịch chiếu hôm nay',
        'color' => 'purple'
    ]
];
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Chào mừng đến với hệ thống quản lý Welcome Board</p>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <?php foreach ($stats as $stat): ?>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon <?php echo $stat['color']; ?>">
                        <i class="<?php echo $stat['icon']; ?>"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?php echo $stat['value']; ?></div>
                <div class="stat-card-label"><?php echo $stat['label']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Actions -->
    <div class="dashboard-grid">
        <div class="stat-card">
            <h3 style="margin-bottom: 15px; color: #667eea;">
                <i class="fas fa-tv"></i> Quản lý TV
            </h3>
            <p style="color: #666; margin-bottom: 15px;">Giám sát và điều khiển các màn hình TV</p>
            <a href="admin/index.php?page=tv" style="color: #667eea; text-decoration: none;">
                Xem chi tiết <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card">
            <h3 style="margin-bottom: 15px; color: #4caf50;">
                <i class="fas fa-image"></i> Quản lý WCB
            </h3>
            <p style="color: #666; margin-bottom: 15px;">Tải lên và quản lý nội dung hiển thị</p>
            <a href="manage-wcb.php" style="color: #4caf50; text-decoration: none;">
                Xem chi tiết <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="stat-card">
            <h3 style="margin-bottom: 15px; color: #ff9800;">
                <i class="fas fa-calendar-alt"></i> Lịch chiếu
            </h3>
            <p style="color: #666; margin-bottom: 15px;">Lên lịch hiển thị nội dung tự động</p>
            <a href="admin/index.php?page=schedule" style="color: #ff9800; text-decoration: none;">
                Xem chi tiết <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';
?>
