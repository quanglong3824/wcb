<?php
require_once '../config/php/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get TV ID and folder from query string
$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;
$tvFolder = isset($_GET['folder']) ? trim($_GET['folder']) : '';

// Connect to database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Cannot connect to database']);
    exit;
}

// If folder is provided and no TV ID, find TV by folder
if ($tvId <= 0 && !empty($tvFolder)) {
    $stmt = $conn->prepare("SELECT id FROM tvs WHERE folder = ?");
    $stmt->bind_param("s", $tvFolder);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $tvId = $result->fetch_assoc()['id'];
    }
}

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid TV ID or folder']);
    exit;
}

// Get TV info
$tvStmt = $conn->prepare("SELECT id, name, location, folder, IFNULL(is_paused, 0) as is_paused FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tvResult = $tvStmt->get_result();

if ($tvResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV not found']);
    exit;
}

$tv = $tvResult->fetch_assoc();
$content = null;

// Nếu TV đang tạm dừng (chế độ chờ), trả về không có nội dung
if ($tv['is_paused']) {
    echo json_encode([
        'success' => false,
        'message' => 'TV đang ở chế độ chờ',
        'paused' => true,
        'tv' => $tv
    ]);
    $conn->close();
    exit;
}

// Kiểm tra nếu yêu cầu lấy tất cả nội dung (cho TV Basement slideshow)
$getAll = isset($_GET['get_all']) && $_GET['get_all'] == '1';

if ($getAll) {
    // Lấy tất cả media được gán cho TV này (tối đa 3)
    $allContentsQuery = "
        SELECT m.id, m.name, m.type, m.file_path, m.thumbnail_path, 'assigned' as source
        FROM tv_media_assignments tma
        INNER JOIN media m ON tma.media_id = m.id
        WHERE tma.tv_id = ?
        AND m.status = 'active'
        ORDER BY tma.display_order ASC, tma.id ASC
        LIMIT 3
    ";
    
    $allStmt = $conn->prepare($allContentsQuery);
    $allStmt->bind_param("i", $tvId);
    $allStmt->execute();
    $allResult = $allStmt->get_result();
    
    $contents = [];
    while ($row = $allResult->fetch_assoc()) {
        $contents[] = $row;
    }
    
    if (count($contents) > 0) {
        // Cập nhật last_heartbeat cho TV
        $updateStmt = $conn->prepare("UPDATE tvs SET last_heartbeat = NOW(), current_content_id = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $contents[0]['id'], $tvId);
        $updateStmt->execute();
        
        echo json_encode([
            'success' => true,
            'tv' => $tv,
            'contents' => $contents,
            'count' => count($contents)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No content assigned to this TV',
            'tv' => $tv
        ]);
    }
    
    $conn->close();
    exit;
}

// BƯỚC 1: Kiểm tra lịch chiếu đang active (ưu tiên cao nhất)
$scheduleQuery = "
    SELECT m.id, m.name, m.type, m.file_path, m.thumbnail_path, 'schedule' as source
    FROM schedules s
    INNER JOIN media m ON s.media_id = m.id
    WHERE s.tv_id = ?
    AND s.status = 'active'
    AND s.schedule_date = CURDATE()
    AND CURTIME() BETWEEN s.start_time AND s.end_time
    AND m.status = 'active'
    ORDER BY s.priority DESC, s.id DESC
    LIMIT 1
";

$scheduleStmt = $conn->prepare($scheduleQuery);
$scheduleStmt->bind_param("i", $tvId);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();

if ($scheduleResult->num_rows > 0) {
    $content = $scheduleResult->fetch_assoc();
}

// BƯỚC 2: Nếu không có lịch chiếu, lấy nội dung mặc định từ tv_media_assignments
if (!$content) {
    $assignmentQuery = "
        SELECT m.id, m.name, m.type, m.file_path, m.thumbnail_path, 'default' as source
        FROM tv_media_assignments tma
        INNER JOIN media m ON tma.media_id = m.id
        WHERE tma.tv_id = ?
        AND tma.is_default = 1
        AND m.status = 'active'
        LIMIT 1
    ";
    
    $assignmentStmt = $conn->prepare($assignmentQuery);
    $assignmentStmt->bind_param("i", $tvId);
    $assignmentStmt->execute();
    $assignmentResult = $assignmentStmt->get_result();
    
    if ($assignmentResult->num_rows > 0) {
        $content = $assignmentResult->fetch_assoc();
    }
}

// BƯỚC 3: Nếu vẫn không có, lấy bất kỳ media nào được gán cho TV
if (!$content) {
    $anyAssignmentQuery = "
        SELECT m.id, m.name, m.type, m.file_path, m.thumbnail_path, 'assigned' as source
        FROM tv_media_assignments tma
        INNER JOIN media m ON tma.media_id = m.id
        WHERE tma.tv_id = ?
        AND m.status = 'active'
        ORDER BY tma.display_order ASC, tma.id ASC
        LIMIT 1
    ";
    
    $anyStmt = $conn->prepare($anyAssignmentQuery);
    $anyStmt->bind_param("i", $tvId);
    $anyStmt->execute();
    $anyResult = $anyStmt->get_result();
    
    if ($anyResult->num_rows > 0) {
        $content = $anyResult->fetch_assoc();
    }
}

// Trả về kết quả
if ($content) {
    // Cập nhật last_heartbeat cho TV
    $updateStmt = $conn->prepare("UPDATE tvs SET last_heartbeat = NOW(), current_content_id = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $content['id'], $tvId);
    $updateStmt->execute();
    
    echo json_encode([
        'success' => true,
        'tv' => $tv,
        'content' => $content
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No content assigned to this TV',
        'tv' => $tv
    ]);
}

$conn->close();
