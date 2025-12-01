<?php
/**
 * Activity Logs API
 * Get and export activity logs
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

// Chỉ super_admin mới được truy cập
if ($_SESSION['user_role'] !== 'super_admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
    exit;
}

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Get users list for filter
if (isset($_GET['get_users'])) {
    $result = $conn->query("SELECT id, username, full_name FROM users ORDER BY full_name ASC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'users' => $users]);
    $conn->close();
    exit;
}

// Export to CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    exportToCSV($conn);
    exit;
}

// Get logs with pagination and filters
getLogs($conn);

$conn->close();

/**
 * Get logs with pagination
 */
function getLogs($conn) {
    header('Content-Type: application/json');
    
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $action = isset($_GET['action']) ? trim($_GET['action']) : '';
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $where[] = "al.description LIKE ?";
        $params[] = "%{$search}%";
        $types .= 's';
    }
    
    if (!empty($action)) {
        $where[] = "al.action = ?";
        $params[] = $action;
        $types .= 's';
    }
    
    if ($userId > 0) {
        $where[] = "al.user_id = ?";
        $params[] = $userId;
        $types .= 'i';
    }
    
    if (!empty($dateFrom)) {
        $where[] = "DATE(al.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $where[] = "DATE(al.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM activity_logs al {$whereClause}";
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
    
    // Get logs
    $query = "SELECT 
                al.id,
                al.user_id,
                al.action,
                al.entity_type,
                al.entity_id,
                al.description,
                al.ip_address,
                al.created_at,
                u.username,
                u.full_name as user_name
              FROM activity_logs al
              LEFT JOIN users u ON al.user_id = u.id
              {$whereClause}
              ORDER BY al.created_at DESC
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'logs' => $logs,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalPages
    ]);
}

/**
 * Export logs to CSV
 */
function exportToCSV($conn) {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $action = isset($_GET['action']) ? trim($_GET['action']) : '';
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $where[] = "al.description LIKE ?";
        $params[] = "%{$search}%";
        $types .= 's';
    }
    
    if (!empty($action)) {
        $where[] = "al.action = ?";
        $params[] = $action;
        $types .= 's';
    }
    
    if ($userId > 0) {
        $where[] = "al.user_id = ?";
        $params[] = $userId;
        $types .= 'i';
    }
    
    if (!empty($dateFrom)) {
        $where[] = "DATE(al.created_at) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $where[] = "DATE(al.created_at) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get logs (limit 10000 for export)
    $query = "SELECT 
                al.id,
                al.created_at,
                u.username,
                u.full_name as user_name,
                al.action,
                al.entity_type,
                al.entity_id,
                al.description,
                al.ip_address
              FROM activity_logs al
              LEFT JOIN users u ON al.user_id = u.id
              {$whereClause}
              ORDER BY al.created_at DESC
              LIMIT 10000";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($query);
    }
    
    // Set headers for CSV download
    $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Write header row
    fputcsv($output, ['ID', 'Thời gian', 'Username', 'Họ tên', 'Hành động', 'Đối tượng', 'ID đối tượng', 'Mô tả', 'IP']);
    
    // Write data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['created_at'],
            $row['username'],
            $row['user_name'],
            $row['action'],
            $row['entity_type'],
            $row['entity_id'],
            $row['description'],
            $row['ip_address']
        ]);
    }
    
    fclose($output);
    $conn->close();
}
