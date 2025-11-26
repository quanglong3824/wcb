<?php
// Xác định trang hiện tại
$currentPage = basename($_SERVER['PHP_SELF']);
$currentParam = isset($_GET['page']) ? $_GET['page'] : '';

// Xác định base path
$basePath = isset($basePath) ? $basePath : '../';

// Menu items
$menuItems = [
    [
        'icon' => 'fas fa-home',
        'label' => 'Dashboard',
        'url' => $basePath . 'index.php',
        'active' => ($currentPage == 'index.php' && empty($currentParam))
    ],
    [
        'icon' => 'fas fa-desktop',
        'label' => 'Giám sát TV',
        'url' => $basePath . 'view.php',
        'active' => ($currentPage == 'view.php')
    ],
    [
        'icon' => 'fas fa-tv',
        'label' => 'Quản lý TV',
        'url' => $basePath . 'admin/index.php?page=tv',
        'active' => ($currentParam == 'tv')
    ],
    [
        'icon' => 'fas fa-image',
        'label' => 'Quản lý WCB',
        'url' => $basePath . 'manage-wcb.php',
        'active' => ($currentPage == 'manage-wcb.php')
    ],
    [
        'icon' => 'fas fa-cloud-upload-alt',
        'label' => 'Upload',
        'url' => $basePath . 'uploads.php',
        'active' => ($currentPage == 'uploads.php')
    ],
    [
        'icon' => 'fas fa-calendar-alt',
        'label' => 'Quản lý chiếu',
        'url' => $basePath . 'admin/index.php?page=schedule',
        'active' => ($currentParam == 'schedule')
    ],
    [
        'icon' => 'fas fa-cog',
        'label' => 'Cài đặt',
        'url' => $basePath . 'admin/index.php?page=settings',
        'active' => ($currentParam == 'settings')
    ]
];
?>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <nav>
        <ul class="sidebar-menu">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($item['url']); ?>" 
                       class="<?php echo $item['active'] ? 'active' : ''; ?>">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                        <span><?php echo htmlspecialchars($item['label']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>