<?php
/**
 * Real-time Monitoring API
 * Long polling for TV status updates
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'status';

switch ($action) {
    case 'status':
        getTVStatus($conn);
        break;
    case 'poll':
        longPoll($conn);
        break;
    case 'summary':
        getSummary($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();

/**
 * Get current TV status
 */
function getTVStatus($conn) {
    $query = "SELECT 
                t.id,
                t.name,
                t.location,
                t.folder,
                t.status,
                t.ip_address,
                t.last_heartbeat,
                t.current_content_id,
                m.name as current_content_name,
                m.type as current_content_type,
                m.file_path as current_content_path,
                m.thumbnail_path as current_content_thumbnail,
                TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) as seconds_since_heartbeat,
                CASE 
                    WHEN t.last_heartbeat IS NULL THEN 'offline'
                    WHEN TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) > 300 THEN 'offline'
                    ELSE 'online'
                END as actual_status
              FROM tvs t
              LEFT JOIN media m ON t.current_content_id = m.id
              ORDER BY t.id ASC";
    
    $result = $conn->query($query);
    $tvs = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['is_online'] = ($row['actual_status'] === 'online');
        $row['last_seen'] = $row['last_heartbeat'] ? date('H:i:s d/m/Y', strtotime($row['last_heartbeat'])) : 'Chưa kết nối';
        $row['offline_duration'] = $row['seconds_since_heartbeat'] ? formatDuration($row['seconds_since_heartbeat']) : null;
        $tvs[] = $row;
    }
    
    // Get summary
    $onlineCount = count(array_filter($tvs, function($tv) { return $tv['is_online']; }));
    $offlineCount = count($tvs) - $onlineCount;
    
    echo json_encode([
        'success' => true,
        'tvs' => $tvs,
        'summary' => [
            'total' => count($tvs),
            'online' => $onlineCount,
            'offline' => $offlineCount
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Long polling for real-time updates
 */
function longPoll($conn) {
    $lastCheck = isset($_GET['last_check']) ? $_GET['last_check'] : null;
    $timeout = 30; // seconds
    $interval = 2; // check every 2 seconds
    $startTime = time();
    
    while (time() - $startTime < $timeout) {
        // Check for changes
        $changes = checkForChanges($conn, $lastCheck);
        
        if (!empty($changes['tvs']) || !empty($changes['notifications'])) {
            echo json_encode([
                'success' => true,
                'has_changes' => true,
                'changes' => $changes,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            return;
        }
        
        sleep($interval);
    }
    
    // Timeout - no changes
    echo json_encode([
        'success' => true,
        'has_changes' => false,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Check for changes since last check
 */
function checkForChanges($conn, $lastCheck) {
    $changes = [
        'tvs' => [],
        'notifications' => []
    ];
    
    // Check TV status changes
    if ($lastCheck) {
        $stmt = $conn->prepare("SELECT id, name, status, last_heartbeat FROM tvs WHERE last_heartbeat > ?");
        $stmt->bind_param("s", $lastCheck);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $changes['tvs'][] = $row;
        }
    }
    
    // Check for new notifications
    $userId = $_SESSION['user_id'];
    if ($lastCheck) {
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND created_at > ? AND is_read = 0");
        $stmt->bind_param("is", $userId, $lastCheck);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $changes['notifications'][] = $row;
        }
    }
    
    return $changes;
}

/**
 * Get monitoring summary
 */
function getSummary($conn) {
    // TV Stats
    $tvStats = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN last_heartbeat IS NOT NULL AND TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) <= 300 THEN 1 ELSE 0 END) as online,
            SUM(CASE WHEN last_heartbeat IS NULL OR TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > 300 THEN 1 ELSE 0 END) as offline
        FROM tvs
    ")->fetch_assoc();
    
    // Media Stats
    $mediaStats = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN type = 'image' THEN 1 ELSE 0 END) as images,
            SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) as videos
        FROM media WHERE status = 'active'
    ")->fetch_assoc();
    
    // Schedule Stats
    $today = date('Y-m-d');
    $scheduleStats = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN schedule_date = '{$today}' THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
        FROM schedules
    ")->fetch_assoc();
    
    // Recent Activity
    $recentActivity = [];
    $activityResult = $conn->query("
        SELECT al.*, u.full_name as user_name 
        FROM activity_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        ORDER BY al.created_at DESC 
        LIMIT 5
    ");
    while ($row = $activityResult->fetch_assoc()) {
        $row['time_ago'] = timeAgo($row['created_at']);
        $recentActivity[] = $row;
    }
    
    // Offline TVs
    $offlineTVs = [];
    $offlineResult = $conn->query("
        SELECT id, name, location, last_heartbeat 
        FROM tvs 
        WHERE last_heartbeat IS NULL OR TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > 300
    ");
    while ($row = $offlineResult->fetch_assoc()) {
        $row['offline_since'] = $row['last_heartbeat'] ? timeAgo($row['last_heartbeat']) : 'Chưa từng kết nối';
        $offlineTVs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'summary' => [
            'tv' => $tvStats,
            'media' => $mediaStats,
            'schedule' => $scheduleStats
        ],
        'recent_activity' => $recentActivity,
        'offline_tvs' => $offlineTVs,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Format duration
 */
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . ' giây';
    } elseif ($seconds < 3600) {
        return floor($seconds / 60) . ' phút';
    } elseif ($seconds < 86400) {
        return floor($seconds / 3600) . ' giờ';
    } else {
        return floor($seconds / 86400) . ' ngày';
    }
}

/**
 * Time ago helper
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Vừa xong';
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
