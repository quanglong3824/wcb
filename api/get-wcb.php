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
$wcbs = [
    [
        'id' => 1,
        'name' => 'Welcome Banner 01',
        'type' => 'image',
        'thumbnail' => '../uploads/welcome_banner_01.jpg',
        'url' => '../uploads/welcome_banner_01.jpg',
        'description' => 'Banner chào mừng khách hàng',
        'assignedTo' => 'TV Basement, TV Jasmine',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'name' => 'Hotel Promo Video',
        'type' => 'video',
        'thumbnail' => '../uploads/promo_thumb.jpg',
        'url' => '../uploads/hotel_promo.mp4',
        'description' => 'Video quảng cáo khách sạn',
        'assignedTo' => 'TV Chrysan, TV FO 1',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'name' => 'Menu Display',
        'type' => 'image',
        'thumbnail' => '../uploads/menu_display.jpg',
        'url' => '../uploads/menu_display.jpg',
        'description' => 'Thực đơn nhà hàng',
        'assignedTo' => 'TV Restaurant',
        'status' => 'active'
    ],
    [
        'id' => 4,
        'name' => 'Welcome Banner 02',
        'type' => 'image',
        'thumbnail' => '../uploads/welcome_banner_02.jpg',
        'url' => '../uploads/welcome_banner_02.jpg',
        'description' => 'Banner chào mừng phiên bản 2',
        'assignedTo' => null,
        'status' => 'inactive'
    ]
];

echo json_encode([
    'success' => true,
    'wcbs' => $wcbs
]);
