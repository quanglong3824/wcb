<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Dữ liệu mẫu - sau này sẽ lấy từ database
$tvs = [
    [
        'id' => 1,
        'name' => 'TV Basement',
        'location' => 'Tầng hầm',
        'status' => 'online',
        'currentContent' => 'Welcome_Banner_01.jpg',
        'contentType' => 'image',
        'contentUrl' => '../uploads/welcome_01.jpg'
    ],
    [
        'id' => 2,
        'name' => 'TV Chrysan',
        'location' => 'Phòng Chrysan',
        'status' => 'online',
        'currentContent' => 'Hotel_Promo.mp4',
        'contentType' => 'video',
        'contentUrl' => '../uploads/promo.mp4'
    ],
    [
        'id' => 3,
        'name' => 'TV Jasmine',
        'location' => 'Phòng Jasmine',
        'status' => 'online',
        'currentContent' => 'Welcome_Banner_02.jpg',
        'contentType' => 'image',
        'contentUrl' => '../uploads/welcome_02.jpg'
    ],
    [
        'id' => 4,
        'name' => 'TV Lotus',
        'location' => 'Phòng Lotus',
        'status' => 'offline',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null
    ],
    [
        'id' => 5,
        'name' => 'TV Restaurant',
        'location' => 'Nhà hàng',
        'status' => 'online',
        'currentContent' => 'Menu_Display.jpg',
        'contentType' => 'image',
        'contentUrl' => '../uploads/menu.jpg'
    ],
    [
        'id' => 6,
        'name' => 'TV FO 1',
        'location' => 'Lễ tân 1',
        'status' => 'online',
        'currentContent' => 'Welcome_Video.mp4',
        'contentType' => 'video',
        'contentUrl' => '../uploads/welcome_video.mp4'
    ]
];

echo json_encode([
    'success' => true,
    'tvs' => $tvs
]);
