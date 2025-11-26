<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Lấy dữ liệu từ database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode([
        'success' => false,
        'wcbs' => [],
        'message' => 'Không thể kết nối database'
    ]);
    exit;
}

$query = "SELECT 
            m.*,
            u.full_name as uploaded_by_name,
            GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') as assigned_to_tvs,
            COUNT(DISTINCT tma.tv_id) as assigned_tv_count
          FROM media m
          LEFT JOIN users u ON m.uploaded_by = u.id
          LEFT JOIN tv_media_assignments tma ON m.id = tma.media_id
          LEFT JOIN tvs t ON tma.tv_id = t.id
          GROUP BY m.id
          ORDER BY m.created_at DESC";

$result = $conn->query($query);
$wcbs = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format file size
        $row['file_size_formatted'] = formatFileSize($row['file_size']);
        
        // Format date
        $row['created_at_formatted'] = date('d/m/Y H:i', strtotime($row['created_at']));
        
        $wcbs[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'wcbs' => $wcbs,
    'total' => count($wcbs),
    'message' => count($wcbs) > 0 ? '' : 'Chưa có nội dung WCB nào. Vui lòng upload nội dung mới.'
]);

$conn->close();

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
