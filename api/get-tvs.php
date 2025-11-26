<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Dữ liệu 7 màn hình dựa trên các folder có sẵn
// Mặc định tất cả đều không có nội dung (màn hình đen)
$tvs = [
    [
        'id' => 1,
        'name' => 'TV Basement',
        'location' => 'Tầng hầm',
        'ipAddress' => '192.168.1.101',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại khu vực tầng hầm',
        'folder' => 'basement'
    ],
    [
        'id' => 2,
        'name' => 'TV Chrysan',
        'location' => 'Phòng Chrysan',
        'ipAddress' => '192.168.1.102',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại phòng Chrysan',
        'folder' => 'chrysan'
    ],
    [
        'id' => 3,
        'name' => 'TV Jasmine',
        'location' => 'Phòng Jasmine',
        'ipAddress' => '192.168.1.103',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại phòng Jasmine',
        'folder' => 'jasmine'
    ],
    [
        'id' => 4,
        'name' => 'TV Lotus',
        'location' => 'Phòng Lotus',
        'ipAddress' => '192.168.1.104',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại phòng Lotus',
        'folder' => 'lotus'
    ],
    [
        'id' => 5,
        'name' => 'TV Restaurant',
        'location' => 'Nhà hàng',
        'ipAddress' => '192.168.1.105',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại nhà hàng',
        'folder' => 'restaurant'
    ],
    [
        'id' => 6,
        'name' => 'TV FO 1',
        'location' => 'Lễ tân 1',
        'ipAddress' => '192.168.1.106',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại quầy lễ tân 1',
        'folder' => 'fo/tv1'
    ],
    [
        'id' => 7,
        'name' => 'TV FO 2',
        'location' => 'Lễ tân 2',
        'ipAddress' => '192.168.1.107',
        'status' => 'online',
        'currentContent' => null,
        'contentUrl' => null,
        'description' => 'TV tại quầy lễ tân 2',
        'folder' => 'fo/tv2'
    ]
];

echo json_encode([
    'success' => true,
    'tvs' => $tvs
]);
