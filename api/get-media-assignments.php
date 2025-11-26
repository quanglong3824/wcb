<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Lấy media_id từ query string
$mediaId = isset($_GET['media_id']) ? intval($_GET['media_id']) : 0;

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

if ($mediaId > 0) {
    // Lấy assignments cho 1 media cụ thể
    $query = "SELECT 
                tma.id,
                tma.tv_id,
                tma.media_id,
                tma.is_default,
                tma.assigned_at,
                t.name as tv_name,
                t.location as tv_location,
                t.status as tv_status,
                u.full_name as assigned_by_name
              FROM tv_media_assignments tma
              INNER JOIN tvs t ON tma.tv_id = t.id
              LEFT JOIN users u ON tma.assigned_by = u.id
              WHERE tma.media_id = ?
              ORDER BY tma.is_default DESC, t.name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $mediaId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Lấy tất cả assignments
    $query = "SELECT 
                tma.id,
                tma.tv_id,
                tma.media_id,
                tma.is_default,
                tma.assigned_at,
                t.name as tv_name,
                t.location as tv_location,
                t.status as tv_status,
                m.name as media_name,
                m.type as media_type,
                u.full_name as assigned_by_name
              FROM tv_media_assignments tma
              INNER JOIN tvs t ON tma.tv_id = t.id
              INNER JOIN media m ON tma.media_id = m.id
              LEFT JOIN users u ON tma.assigned_by = u.id
              WHERE m.status = 'active'
              ORDER BY t.name ASC, tma.is_default DESC";
    
    $result = $conn->query($query);
}

$assignments = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['assigned_at_formatted'] = date('d/m/Y H:i', strtotime($row['assigned_at']));
        $assignments[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'assignments' => $assignments,
    'total' => count($assignments)
]);

$conn->close();
