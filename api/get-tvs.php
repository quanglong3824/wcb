<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Lấy dữ liệu 7 TV từ database với thông tin nội dung đang chiếu
$conn = getDBConnection();

if ($conn) {
    // Lấy thông tin TV kèm nội dung chi tiết
    $query = "SELECT 
                t.id,
                t.name,
                t.location,
                t.folder,
                t.display_url,
                t.status,
                t.ip_address,
                t.description,
                t.last_heartbeat,
                t.current_content_id,
                t.default_content_id,
                IFNULL(t.is_paused, 0) as is_paused,
                m.name as current_content_name,
                m.type as current_content_type,
                m.file_path as current_content_path,
                m.thumbnail_path as current_content_thumbnail,
                dm.name as default_content_name,
                dm.type as default_content_type,
                dm.file_path as default_content_path,
                TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) as seconds_since_heartbeat,
                CASE 
                    WHEN t.last_heartbeat IS NULL THEN 'offline'
                    WHEN TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) > 300 THEN 'offline'
                    ELSE t.status
                END as actual_status
              FROM tvs t
              LEFT JOIN media m ON t.current_content_id = m.id AND m.status = 'active'
              LEFT JOIN media dm ON t.default_content_id = dm.id AND dm.status = 'active'
              ORDER BY t.id ASC";
    
    $result = $conn->query($query);
    $tvs = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Format dữ liệu
            $row['is_online'] = ($row['actual_status'] === 'online');
            $row['last_seen'] = $row['last_heartbeat'] ? date('d/m/Y H:i:s', strtotime($row['last_heartbeat'])) : 'Chưa kết nối';
            
            // Thêm URL đầy đủ cho display
            if ($row['display_url']) {
                $row['full_display_url'] = BASE_URL . $row['display_url'];
            }
            
            // Lấy danh sách tất cả WCB đã gán cho TV này
            $tvId = $row['id'];
            $assignQuery = "SELECT 
                                tma.is_default,
                                m.id,
                                m.name,
                                m.type,
                                m.file_path,
                                m.thumbnail_path
                            FROM tv_media_assignments tma
                            INNER JOIN media m ON tma.media_id = m.id
                            WHERE tma.tv_id = ? AND m.status = 'active'
                            ORDER BY tma.is_default DESC, m.name ASC";
            
            $assignStmt = $conn->prepare($assignQuery);
            $assignStmt->bind_param("i", $tvId);
            $assignStmt->execute();
            $assignResult = $assignStmt->get_result();
            
            $assignedMedia = [];
            while ($assignRow = $assignResult->fetch_assoc()) {
                $assignedMedia[] = $assignRow;
            }
            
            $assignStmt->close();
            
            $row['assigned_media'] = $assignedMedia;
            $row['assigned_media_count'] = count($assignedMedia);
            
            $tvs[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'tvs' => $tvs,
            'total' => count($tvs),
            'online_count' => count(array_filter($tvs, function($tv) { return $tv['is_online']; })),
            'offline_count' => count(array_filter($tvs, function($tv) { return !$tv['is_online']; }))
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'tvs' => [],
            'message' => 'Lỗi truy vấn database: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'tvs' => [],
        'message' => 'Không thể kết nối database'
    ]);
}
