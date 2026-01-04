<?php
/**
 * Activity Logs API
 * Get and export activity logs
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/permissions.php';

header('Content-Type: application/json');

// Kiểm tra quyền xem logs
if (!hasPermission('logs', PERM_VIEW)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền xem Activity Logs']);
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
 * Get logs with pagination and grouping
 */
function getLogs($conn)
{

    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;

    // We fetch more items to allow for grouping while maintaining decent page size
    // Fetching 3x limit is a heuristic
    $fetchLimit = $page * $limit * 3;
    // Actually, simple offset pagination breaks with grouping because row N in DB != row N in View.
    // Correct way: Fetch ALL relevant logs up to Page * Limit, group them, then slice.
    // But that's heavy.
    // Simple way: Standard pagination, group what's on the page. Use client-side "Load More" to fill gaps.
    // Let's stick to standard pagination but group the result.

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
    // Sort by created_at DESC
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

    // Fetch a bit more to handle potential heavy grouping on this page?
    // Let's just fetch standard page for now.
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $rawLogs = [];
    while ($row = $result->fetch_assoc()) {
        $rawLogs[] = $row;
    }

    // Grouping Logic
    $groupedLogs = [];
    if (!empty($rawLogs)) {
        $currentGroup = $rawLogs[0];
        $currentGroup['count'] = 1;
        $currentGroup['ids'] = [$rawLogs[0]['id']];

        for ($i = 1; $i < count($rawLogs); $i++) {
            $log = $rawLogs[$i];

            // Check if similar to previous
            // Logic: Same User, Same Action, Same Description
            // Note: We ignore timestamp difference for now, just consecutive rows
            if (
                $log['user_id'] == $currentGroup['user_id'] &&
                $log['action'] == $currentGroup['action'] &&
                $log['description'] == $currentGroup['description']
            ) {

                $currentGroup['count']++;
                $currentGroup['ids'][] = $log['id'];
                // We keep the created_at of the FIRST item in the group (which is the newest) as the display time
                // Add oldest time so we can show range if needed
                $currentGroup['first_created_at'] = $log['created_at'];

            } else {
                // Different -> Push current group and start new
                $groupedLogs[] = $currentGroup;

                $currentGroup = $log;
                $currentGroup['count'] = 1;
                $currentGroup['ids'] = [$log['id']];
            }
        }
        // Push last group
        $groupedLogs[] = $currentGroup;
    }

    echo json_encode([
        'success' => true,
        'logs' => $groupedLogs,
        'total' => $total, // Start implementing total grouped count is hard, keep raw total
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalPages
    ]);
}

/**
 * Export logs to CSV
 */
function exportToCSV($conn)
{
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
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

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
