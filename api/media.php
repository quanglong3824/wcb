<?php
/**
 * Media API
 * CRUD operations with search, filter, pagination
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

switch ($method) {
    case 'GET':
        getMedia($conn);
        break;
    case 'PUT':
        updateMedia($conn);
        break;
    case 'DELETE':
        deleteMedia($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();

/**
 * Get media with search, filter, pagination
 */
function getMedia($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $type = isset($_GET['type']) ? trim($_GET['type']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : 'active';
    $sortBy = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'created_at';
    $sortOrder = isset($_GET['sort_order']) && strtoupper($_GET['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
    
    // Validate sort column
    $allowedSortColumns = ['id', 'name', 'type', 'file_size', 'created_at'];
    if (!in_array($sortBy, $allowedSortColumns)) {
        $sortBy = 'created_at';
    }
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $where[] = "(m.name LIKE ? OR m.description LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    if (!empty($type)) {
        $where[] = "m.type = ?";
        $params[] = $type;
        $types .= 's';
    }
    
    if (!empty($status)) {
        $where[] = "m.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM media m {$whereClause}";
    if (!empty($params)) {
        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param($types, ...$params);
        $countStmt->execute();
        $totalResult = $countStmt->get_result()->fetch_assoc();
    } else {
        $totalResult = $conn->query($countQuery)->fetch_assoc();
    }
    $total = $totalResult['total'];
    $totalPages = ceil($total / $limit);
    
    // Get media
    $query = "SELECT 
                m.*,
                u.full_name as uploaded_by_name,
                (SELECT COUNT(*) FROM tv_media_assignments WHERE media_id = m.id) as assignment_count
              FROM media m
              LEFT JOIN users u ON m.uploaded_by = u.id
              {$whereClause}
              ORDER BY {$sortBy} {$sortOrder}
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $media = [];
    while ($row = $result->fetch_assoc()) {
        // Format file size
        $row['file_size_formatted'] = formatFileSize($row['file_size']);
        $row['created_at_formatted'] = date('d/m/Y H:i', strtotime($row['created_at']));
        
        // Get assigned TVs
        $assignQuery = "SELECT t.id, t.name, t.location 
                        FROM tv_media_assignments tma 
                        JOIN tvs t ON tma.tv_id = t.id 
                        WHERE tma.media_id = ?";
        $assignStmt = $conn->prepare($assignQuery);
        $assignStmt->bind_param("i", $row['id']);
        $assignStmt->execute();
        $assignResult = $assignStmt->get_result();
        
        $assignedTvs = [];
        while ($tv = $assignResult->fetch_assoc()) {
            $assignedTvs[] = $tv;
        }
        $row['assigned_tvs'] = $assignedTvs;
        
        $media[] = $row;
    }
    
    // Get stats
    $stats = [
        'total' => $conn->query("SELECT COUNT(*) as c FROM media WHERE status = 'active'")->fetch_assoc()['c'],
        'images' => $conn->query("SELECT COUNT(*) as c FROM media WHERE type = 'image' AND status = 'active'")->fetch_assoc()['c'],
        'videos' => $conn->query("SELECT COUNT(*) as c FROM media WHERE type = 'video' AND status = 'active'")->fetch_assoc()['c'],
        'total_size' => $conn->query("SELECT SUM(file_size) as s FROM media WHERE status = 'active'")->fetch_assoc()['s'] ?? 0
    ];
    $stats['total_size_formatted'] = formatFileSize($stats['total_size']);
    
    echo json_encode([
        'success' => true,
        'media' => $media,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalPages,
        'stats' => $stats
    ]);
}

/**
 * Update media
 */
function updateMedia($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if media exists
    $checkStmt = $conn->prepare("SELECT id, name FROM media WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $existing = $checkStmt->get_result()->fetch_assoc();
    
    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'Media không tồn tại']);
        return;
    }
    
    // Build update query
    $updates = [];
    $params = [];
    $types = '';
    
    if (isset($data['name'])) {
        $updates[] = "name = ?";
        $params[] = trim($data['name']);
        $types .= 's';
    }
    
    if (isset($data['description'])) {
        $updates[] = "description = ?";
        $params[] = trim($data['description']);
        $types .= 's';
    }
    
    if (isset($data['status'])) {
        $updates[] = "status = ?";
        $params[] = $data['status'];
        $types .= 's';
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'Không có dữ liệu để cập nhật']);
        return;
    }
    
    $updates[] = "updated_at = NOW()";
    
    $query = "UPDATE media SET " . implode(', ', $updates) . " WHERE id = ?";
    $params[] = $id;
    $types .= 'i';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        logActivity($conn, 'update_media', 'media', $id, "Cập nhật media: {$existing['name']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $conn->error]);
    }
}

/**
 * Delete media (soft delete)
 */
function deleteMedia($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $permanent = isset($_GET['permanent']) && $_GET['permanent'] === '1';
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    // Check if media exists
    $checkStmt = $conn->prepare("SELECT id, name, file_path FROM media WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $media = $checkStmt->get_result()->fetch_assoc();
    
    if (!$media) {
        echo json_encode(['success' => false, 'message' => 'Media không tồn tại']);
        return;
    }
    
    if ($permanent) {
        // Delete file from disk
        $filePath = dirname(__DIR__) . '/' . $media['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM media WHERE id = ?");
        $stmt->bind_param("i", $id);
    } else {
        // Soft delete
        $stmt = $conn->prepare("UPDATE media SET status = 'deleted', updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
    }
    
    if ($stmt->execute()) {
        logActivity($conn, 'delete_media', 'media', $id, "Xóa media: {$media['name']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Xóa thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . $conn->error]);
    }
}

/**
 * Format file size
 */
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

/**
 * Log activity
 */
function logActivity($conn, $action, $entityType, $entityId, $description) {
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $ip = $_SERVER['REMOTE_ADDR'];
        $stmt->bind_param("ississ", $_SESSION['user_id'], $action, $entityType, $entityId, $description, $ip);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}
