<?php
// Include permission system
require_once __DIR__ . '/permissions.php';

// Xác định trang hiện tại
$currentPage = basename($_SERVER['PHP_SELF']);
$currentParam = isset($_GET['page']) ? $_GET['page'] : '';

// Xác định base path
$basePath = isset($basePath) ? $basePath : '../';

// Menu items với module permission
$menuItems = [
    [
        'icon' => 'fas fa-home',
        'label' => 'Dashboard',
        'url' => $basePath . 'index.php',
        'active' => ($currentPage == 'index.php' && empty($currentParam)),
        'module' => 'dashboard'
    ],
    [
        'icon' => 'fas fa-desktop',
        'label' => 'Giám sát TV',
        'url' => $basePath . 'view.php',
        'active' => ($currentPage == 'view.php'),
        'module' => 'tv_monitor'
    ],
    [
        'icon' => 'fas fa-tv',
        'label' => 'Quản lý TV',
        'url' => $basePath . 'tv.php',
        'active' => ($currentPage == 'tv.php'),
        'module' => 'tv_manage'
    ],
    [
        'icon' => 'fas fa-image',
        'label' => 'Quản lý WCB',
        'url' => $basePath . 'manage-wcb.php',
        'active' => ($currentPage == 'manage-wcb.php'),
        'module' => 'wcb_manage'
    ],
    [
        'icon' => 'fas fa-cloud-upload-alt',
        'label' => 'Upload',
        'url' => $basePath . 'uploads.php',
        'active' => ($currentPage == 'uploads.php'),
        'module' => 'upload'
    ],
    [
        'icon' => 'fas fa-calendar-alt',
        'label' => 'Lịch chiếu',
        'url' => $basePath . 'schedule.php',
        'active' => ($currentPage == 'schedule.php'),
        'module' => 'schedule'
    ],
    [
        'icon' => 'fas fa-cog',
        'label' => 'Cài đặt',
        'url' => $basePath . 'settings.php',
        'active' => ($currentPage == 'settings.php'),
        'module' => 'settings'
    ]
];

// Admin menu items - hiển thị cho tất cả nhưng readonly cho non-admin
$adminMenuItems = [
    [
        'icon' => 'fas fa-users',
        'label' => 'Quản lý Users',
        'url' => $basePath . 'users.php',
        'active' => ($currentPage == 'users.php'),
        'module' => 'users'
    ],
    [
        'icon' => 'fas fa-history',
        'label' => 'Activity Logs',
        'url' => $basePath . 'logs.php',
        'active' => ($currentPage == 'logs.php'),
        'module' => 'logs'
    ],
    [
        'icon' => 'fas fa-database',
        'label' => 'Backup',
        'url' => $basePath . 'backup.php',
        'active' => ($currentPage == 'backup.php'),
        'module' => 'backup'
    ]
];
?>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-tv"></i>
            <div>
                <h2>WCB System</h2>
                <p>Aurora Hotel</p>
            </div>
        </div>
    </div>
    
    <?php $isSuperAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin'); ?>
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main Menu</div>
            <?php foreach ($menuItems as $item): 
                $perms = getModulePermissionIcons($item['module']);
                $isReadonly = isReadOnly($item['module']);
            ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>" 
                   class="nav-item <?php echo $item['active'] ? 'active' : ''; ?>"
                   title="<?php echo !$isSuperAdmin ? $perms['title'] : ''; ?>">
                    <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                    <?php if (!$isSuperAdmin && $isReadonly): ?>
                        <i class="fas fa-lock perm-readonly-icon" title="Chỉ xem"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <?php foreach ($adminMenuItems as $item): 
                $perms = getModulePermissionIcons($item['module']);
                $isReadonly = isReadOnly($item['module']);
            ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>" 
                   class="nav-item <?php echo $item['active'] ? 'active' : ''; ?>"
                   title="<?php echo !$isSuperAdmin ? $perms['title'] : ''; ?>">
                    <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                    <span><?php echo htmlspecialchars($item['label']); ?></span>
                    <?php if (!$isSuperAdmin && $isReadonly): ?>
                        <i class="fas fa-lock perm-readonly-icon" title="Chỉ xem"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Account</div>
            <a href="<?php echo $basePath; ?>profile.php" class="nav-item <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="<?php echo $basePath; ?>auth/logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </nav>
</aside>