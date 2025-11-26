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

// Thống kê từ database (TODO: Implement)
$stats = [
    [
        'icon' => 'fas fa-tv',
        'value' => '--',
        'label' => 'Tổng số TV',
        'color' => 'blue'
    ],
    [
        'icon' => 'fas fa-check-circle',
        'value' => '--',
        'label' => 'TV đang hoạt động',
        'color' => 'green'
    ],
    [
        'icon' => 'fas fa-images',
        'value' => '--',
        'label' => 'Nội dung WCB',
        'color' => 'orange'
    ],
    [
        'icon' => 'fas fa-calendar-check',
        'value' => '--',
        'label' => 'Lịch chiếu hôm nay',
        'color' => 'purple'
    ]
];
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        <p>Chào mừng đến với hệ thống quản lý Welcome Board - Aurora Hotel</p>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid">
        <?php foreach ($stats as $stat): ?>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-icon">
                        <i class="<?php echo $stat['icon']; ?>"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?php echo $stat['value']; ?></div>
                <div class="stat-card-label"><?php echo $stat['label']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-bolt"></i> Thao tác nhanh</h2>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-tv text-gold"></i> Quản lý TV</h3>
                <p class="text-muted mb-3">Giám sát và điều khiển các màn hình TV</p>
                <a href="tv.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-image text-gold"></i> Quản lý WCB</h3>
                <p class="text-muted mb-3">Tải lên và quản lý nội dung hiển thị</p>
                <a href="manage-wcb.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-calendar-alt text-gold"></i> Lịch chiếu</h3>
                <p class="text-muted mb-3">Lên lịch hiển thị nội dung tự động</p>
                <a href="schedule.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Hoạt động gần đây</h2>
            <a href="#" class="btn btn-sm btn-secondary">Xem tất cả</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-inbox" style="font-size: 3em; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                            <p>Chưa có hoạt động nào</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';
?>
