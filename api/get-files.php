<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Dữ liệu mẫu - sau này sẽ lấy từ database và thư mục uploads
$files = [
    [
        'id' => 1,
        'name' => 'welcome_banner_01.jpg',
        'type' => 'image/jpeg',
        'size' => 2048576,
        'url' => '../uploads/welcome_banner_01.jpg',
        'date' => '2024-01-15'
    ],
    [
        'id' => 2,
        'name' => 'hotel_promo.mp4',
        'type' => 'video/mp4',
        'size' => 15728640,
        'url' => '../uploads/hotel_promo.mp4',
        'date' => '2024-01-14'
    ],
    [
        'id' => 3,
        'name' => 'welcome_banner_02.jpg',
        'type' => 'image/jpeg',
        'size' => 1835008,
        'url' => '../uploads/welcome_banner_02.jpg',
        'date' => '2024-01-13'
    ],
    [
        'id' => 4,
        'name' => 'menu_display.jpg',
        'type' => 'image/jpeg',
        'size' => 3145728,
        'url' => '../uploads/menu_display.jpg',
        'date' => '2024-01-12'
    ]
];

echo json_encode([
    'success' => true,
    'files' => $files
]);
