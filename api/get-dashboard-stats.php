<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

try {
    // 1. Thống kê TV
    $tvStats = $conn->query("
        SELECT 
            COUNT(*) as total_tvs,
            SUM(CASE WHEN status = 'online' THEN 1 ELSE 0 END) as online_tvs,
            SUM(CASE WHEN status = 'offline' THEN 1 ELSE 0 END) as offline_tvs
        FROM tvs
    ")->fetch_assoc();
    
    // 2. Thống kê Media/WCB
    $mediaStats = $conn->query("
        SELECT 
            COUNT(*) as total_media,
            SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as total_images,
            SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) as total_videos
        FROM media 
        WHERE status = 'active'
    ")->fetch_assoc();
    
    // 3. Thống kê Schedules
    $scheduleStats = $conn->query("
        SELECT 
            COUNT(*) as total_schedules,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_schedules,
            SUM(CASE WHEN schedule_date = CURDATE() THEN 1 ELSE 0 END) as today_schedules
        FROM schedules
    ")->fetch_assoc();
    
    // 4. Thống kê Assignments
    $assignmentStats = $conn->query("
        SELECT COUNT(*) as total_assignments
        FROM tv_media_assignments
    ")->fetch_assoc();
    
    // 5. Hoạt động gần đây (10 records)
    $recentActivities = [];
    $activityQuery = "
        SELECT 
            al.id,
            al.action,
            al.entity_type,
            al.entity_id,
            al.description,
            al.created_at,
            u.full_name as user_name,
            u.username
        FROM activity_logs al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 10
    ";
    
    $activityResult = $conn->query($activityQuery);
    while ($row = $activityResult->fetch_assoc()) {
        $row['created_at_formatted'] = date('d/m/Y H:i', strtotime($row['created_at']));
        $row['time_ago'] = timeAgo($row['created_at']);
        $recentActivities[] = $row;
    }
    
    // 6. TV Status Details
    $tvDetails = [];
    $tvQuery = "
        SELECT 
            t.id,
            t.name,
            t.location,
            t.status,
            t.last_heartbeat,
            m.name as current_content_name,
            m.type as current_content_type
        FROM tvs t
        LEFT JOIN media m ON t.current_content_id = m.id
        ORDER BY t.id ASC
    ";
    
    $tvResult = $conn->query($tvQuery);
    while ($row = $tvResult->fetch_assoc()) {
        $row['last_heartbeat_formatted'] = $row['last_heartbeat'] ? date('d/m/Y H:i', strtotime($row['last_heartbeat'])) : 'Chưa kết nối';
        $tvDetails[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'tv' => $tvStats,
            'media' => $mediaStats,
            'schedule' => $scheduleStats,
            'assignment' => $assignmentStats
        ],
        'recent_activities' => $recentActivities,
        'tv_details' => $tvDetails
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();

// Helper function
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . ' giây trước';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' phút trước';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' giờ trước';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' ngày trước';
    } else {
        return date('d/m/Y H:i', $timestamp);
    }
}
