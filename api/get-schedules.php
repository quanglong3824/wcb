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
$schedules = [
    [
        'id' => 1,
        'tvId' => 1,
        'tvName' => 'TV Basement',
        'contentId' => 1,
        'contentName' => 'Welcome Banner 01',
        'date' => '2024-01-20',
        'startTime' => '08:00',
        'endTime' => '18:00',
        'repeat' => 'daily',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'tvId' => 2,
        'tvName' => 'TV Chrysan',
        'contentId' => 2,
        'contentName' => 'Hotel Promo Video',
        'date' => '2024-01-20',
        'startTime' => '09:00',
        'endTime' => '17:00',
        'repeat' => 'daily',
        'status' => 'active'
    ],
    [
        'id' => 3,
        'tvId' => 5,
        'tvName' => 'TV Restaurant',
        'contentId' => 3,
        'contentName' => 'Menu Display',
        'date' => '2024-01-20',
        'startTime' => '11:00',
        'endTime' => '22:00',
        'repeat' => 'daily',
        'status' => 'active'
    ],
    [
        'id' => 4,
        'tvId' => 3,
        'tvName' => 'TV Jasmine',
        'contentId' => 1,
        'contentName' => 'Welcome Banner 01',
        'date' => '2024-01-21',
        'startTime' => '08:00',
        'endTime' => '20:00',
        'repeat' => 'none',
        'status' => 'pending'
    ]
];

echo json_encode([
    'success' => true,
    'schedules' => $schedules
]);
