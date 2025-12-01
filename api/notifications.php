<?php
/**
 * Notifications API
 * Get and manage in-app notifications
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        if ($action === 'unread_count') {
            getUnreadCount($conn);
        } else {
            getNotifications($conn);
        }
        break;
    case 'POST':
        if ($action === 'mark_read') {
            markAsRead($conn);
        } elseif ($action === 'mark_all_read') {
            markAllAsRead($conn);
        } else {
            createNotification($conn);
        }
        break;
    case 'DELETE':
        deleteNotification($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();

/**
 * Get notifications for current user
 */
function getNotifications($conn) {
    $userId = $_SESSION['user_id'];
    $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 20;
    $unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] === '1';
    
    $where = "WHERE user_id = ? OR user_id IS NULL";
    if ($unreadOnly) {
        $where .= " AND is_read = 0";
    }
    
    $query = "SELECT * FROM notifications {$where} ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at_formatted'] = timeAgo($row['created_at']);
        $notifications[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
}

/**
 * Get unread count
 */
function getUnreadCount($conn) {
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'count' => intval($result['count'])
    ]);
}

/**
 * Mark notification as read
 */
function markAsRead($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $id = isset($data['id']) ? intval($data['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        return;
    }
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update']);
    }
}

/**
 * Mark all notifications as read
 */
function markAllAsRead($conn) {
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE (user_id = ? OR user_id IS NULL) AND is_read = 0");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update']);
    }
}

/**
 * Create notification (for system use)
 */
function createNotification($conn) {
    // Only super_admin can create notifications
    if ($_SESSION['user_role'] !== 'super_admin') {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $type = isset($data['type']) ? trim($data['type']) : 'info';
    $title = isset($data['title']) ? trim($data['title']) : '';
    $message = isset($data['message']) ? trim($data['message']) : '';
    $userId = isset($data['user_id']) ? intval($data['user_id']) : null;
    $link = isset($data['link']) ? trim($data['link']) : null;
    
    if (empty($title) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Title and message are required']);
        return;
    }
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, link, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issss", $userId, $type, $title, $message, $link);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Notification created',
            'id' => $stmt->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create notification']);
    }
}

/**
 * Delete notification
 */
function deleteNotification($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
        return;
    }
    
    $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Notification deleted']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete']);
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
