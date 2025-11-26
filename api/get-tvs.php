<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Dữ liệu mẫu
$tvs = [
    [
        'id' => 1,
        'name' => 'TV Basement',
        'location' => 'Tầng hầm',
        'ipAddress' => '192.168.1.101',
        'status' => 'online',
        'currentContent' => 'Welcome Banner 01',
        'contentUrl' => '../uploads/welcome_01.jpg',
        'description' => 'TV tại khu vực tầng hầm'
    ],
    [
        'id' => 2,
        'name' => 'TV Chrysan',
        'location' => 'Phòng Chrysan',
        'ipAddress' => '192.168.1.102',
        'status' => 'online',
        'currentContent' => 'Hotel Promo',
        'contentUrl' => '../uploads/promo.mp4',
        'description' => 'TV tại phòng Chrysan'
    ],
    [
        'id' => 3,
        'name' => 'TV Jasmine',
        'location' => 'Phòng Jasmine',
        'ipAddress' => '192.168.1.103',
        'status' => 'online',
        'currentContent' => 'Welcome Banner 02',
        'contentUrl' => '../uploads/welcome_02.jpg',
        'description' => 'TV tại phòng Jasmine'
    ],
    [
        'id' => 4,
        'name' => 'TV Lotus',
        'location' => 'Phòng Lotus',
        'ipAddress' => '192.168.1.104',
        'status' => 'offline',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại phòng Lotus'
    ],
    [
        'id' => 5,
        'name' => 'TV Restaurant',
        'location' => 'Nhà hàng',
        'ipAddress' => '192.168.1.105',
        'status' => 'online',
        'currentContent' => 'Menu Display',
        'contentUrl' => '../uploads/menu.jpg',
        'description' => 'TV tại nhà hàng'
    ],
    [
        'id' => 6,
        'name' => 'TV FO 1',
        'location' => 'Lễ tân 1',
        'ipAddress' => '192.168.1.106',
        'status' => 'online',
        'currentContent' => 'Welcome Video',
        'contentUrl' => '../uploads/welcome_video.mp4',
        'description' => 'TV tại quầy lễ tân 1'
    ]
];

echo json_encode([
    'success' => true,
    'tvs' => $tvs
]);
