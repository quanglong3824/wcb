<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// TODO: Lấy dữ liệu từ database
// $conn = getDBConnection();
// $query = "SELECT s.*, t.name as tv_name, m.name as media_name 
//           FROM schedules s 
//           LEFT JOIN tvs t ON s.tv_id = t.id 
//           LEFT JOIN media m ON s.media_id = m.id 
//           ORDER BY s.schedule_date DESC, s.start_time DESC";
// $result = $conn->query($query);
// $schedules = [];
// while ($row = $result->fetch_assoc()) {
//     $schedules[] = $row;
// }

$schedules = [];

echo json_encode([
    'success' => true,
    'schedules' => $schedules,
    'message' => 'Chưa có lịch chiếu nào. Vui lòng tạo lịch chiếu mới.'
]);
