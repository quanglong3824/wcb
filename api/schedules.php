<?php
/**
 * Schedules API
 * CRUD operations for schedule management
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
        getSchedules($conn);
        break;
    case 'POST':
        createSchedule($conn);
        break;
    case 'PUT':
        updateSchedule($conn);
        break;
    case 'DELETE':
        deleteSchedule($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();

/**
 * Get schedules with filters
 */
function getSchedules($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, intval($_GET['limit']))) : 20;
    $offset = ($page - 1) * $limit;
    
    $tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = '';
    
    if ($tvId > 0) {
        $where[] = "s.tv_id = ?";
        $params[] = $tvId;
        $types .= 'i';
    }
    
    if (!empty($status)) {
        $where[] = "s.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($dateFrom)) {
        $where[] = "s.schedule_date >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }
    
    if (!empty($dateTo)) {
        $where[] = "s.schedule_date <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM schedules s {$whereClause}";
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
    
    // Get schedules
    $query = "SELECT 
                s.*,
                t.name as tv_name,
                t.location as tv_location,
                m.name as media_name,
                m.type as media_type,
                m.file_path as media_path,
                m.thumbnail_path as media_thumbnail,
                u.full_name as created_by_name
              FROM schedules s
              LEFT JOIN tvs t ON s.tv_id = t.id
              LEFT JOIN media m ON s.media_id = m.id
              LEFT JOIN users u ON s.created_by = u.id
              {$whereClause}
              ORDER BY s.schedule_date DESC, s.start_time ASC
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        // Format times
        $row['start_time_formatted'] = date('H:i', strtotime($row['start_time']));
        $row['end_time_formatted'] = date('H:i', strtotime($row['end_time']));
        $row['schedule_date_formatted'] = date('d/m/Y', strtotime($row['schedule_date']));
        
        // Check if currently active
        $now = new DateTime();
        $scheduleDate = new DateTime($row['schedule_date']);
        $startTime = new DateTime($row['schedule_date'] . ' ' . $row['start_time']);
        $endTime = new DateTime($row['schedule_date'] . ' ' . $row['end_time']);
        
        $row['is_today'] = $scheduleDate->format('Y-m-d') === $now->format('Y-m-d');
        $row['is_active_now'] = $row['status'] === 'active' && $now >= $startTime && $now <= $endTime;
        
        $schedules[] = $row;
    }
    
    // Get stats
    $today = date('Y-m-d');
    $stats = [
        'total' => $conn->query("SELECT COUNT(*) as c FROM schedules")->fetch_assoc()['c'],
        'active' => $conn->query("SELECT COUNT(*) as c FROM schedules WHERE status = 'active'")->fetch_assoc()['c'],
        'today' => $conn->query("SELECT COUNT(*) as c FROM schedules WHERE schedule_date = '{$today}'")->fetch_assoc()['c'],
        'pending' => $conn->query("SELECT COUNT(*) as c FROM schedules WHERE status = 'pending'")->fetch_assoc()['c']
    ];
    
    echo json_encode([
        'success' => true,
        'schedules' => $schedules,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalPages,
        'stats' => $stats
    ]);
}

/**
 * Create new schedule
 */
function createSchedule($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Validate required fields
    $required = ['tv_id', 'media_id', 'schedule_date', 'start_time', 'end_time'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            return;
        }
    }
    
    $tvId = intval($data['tv_id']);
    $mediaId = intval($data['media_id']);
    $scheduleDate = $data['schedule_date'];
    $startTime = $data['start_time'];
    $endTime = $data['end_time'];
    $repeatType = isset($data['repeat_type']) ? $data['repeat_type'] : 'none';
    $priority = isset($data['priority']) ? intval($data['priority']) : 1;
    $status = isset($data['status']) ? $data['status'] : 'active';
    
    // Validate time
    if (strtotime($startTime) >= strtotime($endTime)) {
        echo json_encode(['success' => false, 'message' => 'Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc']);
        return;
    }
    
    // Check for conflicts
    $conflictQuery = "SELECT id FROM schedules 
                      WHERE tv_id = ? 
                      AND schedule_date = ? 
                      AND status = 'active'
                      AND (
                          (start_time <= ? AND end_time > ?) OR
                          (start_time < ? AND end_time >= ?) OR
                          (start_time >= ? AND end_time <= ?)
                      )";
    $conflictStmt = $conn->prepare($conflictQuery);
    $conflictStmt->bind_param("isssssss", $tvId, $scheduleDate, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime);
    $conflictStmt->execute();
    
    if ($conflictStmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Lịch chiếu bị trùng với lịch khác']);
        return;
    }
    
    // Insert schedule
    $stmt = $conn->prepare("INSERT INTO schedules (tv_id, media_id, schedule_date, start_time, end_time, repeat_type, priority, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iissssisi", $tvId, $mediaId, $scheduleDate, $startTime, $endTime, $repeatType, $priority, $status, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $scheduleId = $stmt->insert_id;
        
        // Log activity
        logActivity($conn, 'create_schedule', 'schedule', $scheduleId, "Tạo lịch chiếu mới");
        
        echo json_encode([
            'success' => true,
            'message' => 'Tạo lịch chiếu thành công',
            'schedule_id' => $scheduleId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo lịch chiếu: ' . $conn->error]);
    }
}

/**
 * Update schedule
 */
function updateSchedule($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if schedule exists
    $checkStmt = $conn->prepare("SELECT id FROM schedules WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Lịch chiếu không tồn tại']);
        return;
    }
    
    // Build update query
    $updates = [];
    $params = [];
    $types = '';
    
    if (isset($data['tv_id'])) {
        $updates[] = "tv_id = ?";
        $params[] = intval($data['tv_id']);
        $types .= 'i';
    }
    
    if (isset($data['media_id'])) {
        $updates[] = "media_id = ?";
        $params[] = intval($data['media_id']);
        $types .= 'i';
    }
    
    if (isset($data['schedule_date'])) {
        $updates[] = "schedule_date = ?";
        $params[] = $data['schedule_date'];
        $types .= 's';
    }
    
    if (isset($data['start_time'])) {
        $updates[] = "start_time = ?";
        $params[] = $data['start_time'];
        $types .= 's';
    }
    
    if (isset($data['end_time'])) {
        $updates[] = "end_time = ?";
        $params[] = $data['end_time'];
        $types .= 's';
    }
    
    if (isset($data['repeat_type'])) {
        $updates[] = "repeat_type = ?";
        $params[] = $data['repeat_type'];
        $types .= 's';
    }
    
    if (isset($data['priority'])) {
        $updates[] = "priority = ?";
        $params[] = intval($data['priority']);
        $types .= 'i';
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
    
    $query = "UPDATE schedules SET " . implode(', ', $updates) . " WHERE id = ?";
    $params[] = $id;
    $types .= 'i';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        logActivity($conn, 'update_schedule', 'schedule', $id, "Cập nhật lịch chiếu");
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $conn->error]);
    }
}

/**
 * Delete schedule
 */
function deleteSchedule($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    // Check if schedule exists
    $checkStmt = $conn->prepare("SELECT id FROM schedules WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Lịch chiếu không tồn tại']);
        return;
    }
    
    // Delete schedule
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logActivity($conn, 'delete_schedule', 'schedule', $id, "Xóa lịch chiếu");
        
        echo json_encode([
            'success' => true,
            'message' => 'Xóa lịch chiếu thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . $conn->error]);
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
