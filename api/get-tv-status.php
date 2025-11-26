<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Dữ liệu 7 màn hình - mặc định không có nội dung
$tvs = [
    [
        'id' => 1,
        'name' => 'TV Basement',
        'location' => 'Tầng hầm',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'basement'
    ],
    [
        'id' => 2,
        'name' => 'TV Chrysan',
        'location' => 'Phòng Chrysan',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'chrysan'
    ],
    [
        'id' => 3,
        'name' => 'TV Jasmine',
        'location' => 'Phòng Jasmine',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'jasmine'
    ],
    [
        'id' => 4,
        'name' => 'TV Lotus',
        'location' => 'Phòng Lotus',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'lotus'
    ],
    [
        'id' => 5,
        'name' => 'TV Restaurant',
        'location' => 'Nhà hàng',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'restaurant'
    ],
    [
        'id' => 6,
        'name' => 'TV FO 1',
        'location' => 'Lễ tân 1',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'fo/tv1'
    ],
    [
        'id' => 7,
        'name' => 'TV FO 2',
        'location' => 'Lễ tân 2',
        'status' => 'online',
        'currentContent' => null,
        'contentType' => null,
        'contentUrl' => null,
        'folder' => 'fo/tv2'
    ]
];

echo json_encode([
    'success' => true,
    'tvs' => $tvs
]);
