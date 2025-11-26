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
// $query = "SELECT m.*, 
//           GROUP_CONCAT(t.name SEPARATOR ', ') as assigned_to
//           FROM media m
//           LEFT JOIN tv_media_assignments tma ON m.id = tma.media_id
//           LEFT JOIN tvs t ON tma.tv_id = t.id
//           GROUP BY m.id
//           ORDER BY m.created_at DESC";
// $result = $conn->query($query);
// $wcbs = [];
// while ($row = $result->fetch_assoc()) {
//     $wcbs[] = $row;
// }

$wcbs = [];

echo json_encode([
    'success' => true,
    'wcbs' => $wcbs,
    'message' => 'Chưa có nội dung WCB nào. Vui lòng upload nội dung mới.'
]);
