<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// TODO: Lấy dữ liệu từ database với view
// $conn = getDBConnection();
// $query = "SELECT * FROM view_tv_status ORDER BY id ASC";
// $result = $conn->query($query);
// $tvs = [];
// while ($row = $result->fetch_assoc()) {
//     $tvs[] = [
//         'id' => $row['id'],
//         'name' => $row['name'],
//         'location' => $row['location'],
//         'status' => $row['status'],
//         'currentContent' => $row['current_content_name'],
//         'contentType' => $row['current_content_type'],
//         'contentUrl' => $row['current_content_path'],
//         'folder' => $row['folder']
//     ];
// }

$tvs = [];

echo json_encode([
    'success' => true,
    'tvs' => $tvs,
    'message' => 'Chưa có dữ liệu TV. Vui lòng thêm TV từ database.'
]);
