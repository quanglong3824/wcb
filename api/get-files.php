<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// TODO: Lấy dữ liệu từ database
// $conn = getDBConnection();
// $query = "SELECT * FROM media ORDER BY created_at DESC";
// $result = $conn->query($query);
// $files = [];
// while ($row = $result->fetch_assoc()) {
//     $files[] = [
//         'id' => $row['id'],
//         'name' => $row['file_name'],
//         'type' => $row['type'],
//         'size' => $row['file_size'],
//         'url' => $row['file_path'],
//         'date' => $row['created_at']
//     ];
// }

$files = [];

echo json_encode([
    'success' => true,
    'files' => $files,
    'message' => 'Chưa có file nào. Vui lòng upload file mới.'
]);
