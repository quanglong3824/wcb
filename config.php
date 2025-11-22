<?php
// Cấu hình database - Multi TV System
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'auroraho_longdev');
define('DB_PASS', '@longdev3824');
define('DB_NAME', 'auroraho_wcb');
// Base path cho subfolder deployment
define('BASE_PATH', '/wcb');

// Helper function để generate base URL
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . BASE_PATH;
}

// Helper function để generate asset URLs
function getAssetUrl($path) {
    // Remove leading slash if exists
    $path = ltrim($path, '/');
    return BASE_PATH . '/' . $path;
}

// Kết nối database
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            if (strpos($_SERVER['REQUEST_URI'] ?? '', 'api') !== false) {
                throw new Exception("Database connection failed");
            }
            die("Kết nối database thất bại: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
    }
    
    return $conn;
}

// Lấy danh sách departments
function getDepartments() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM departments ORDER BY id");
    $departments = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    }
    return $departments;
}

// Lấy danh sách TV theo department (chỉ active)
function getTVsByDepartment($department_id = null) {
    $conn = getDBConnection();
    $where = $department_id ? "WHERE tv.department_id = $department_id AND tv.status = 'active'" : "WHERE tv.status = 'active'";
    $result = $conn->query("SELECT tv.*, d.name as department_name, d.code as department_code 
                           FROM tv_screens tv 
                           JOIN departments d ON tv.department_id = d.id 
                           $where 
                           ORDER BY tv.department_id, tv.id");
    $tvs = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Add board count for each TV
            $row['board_count'] = getTVBoardCount($row['id']);
            $row['can_assign'] = canAssignToTV($row['id']);
            $tvs[] = $row;
        }
    }
    return $tvs;
}

// Lấy số lượng WCB đang active của một TV
function getTVBoardCount($tv_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) as count 
                           FROM board_assignments 
                           WHERE tv_id = ? AND status = 'active'");
    $stmt->bind_param("i", $tv_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int)$row['count'];
}

// Kiểm tra TV có thể nhận thêm WCB không (max 3)
function canAssignToTV($tv_id) {
    $count = getTVBoardCount($tv_id);
    return $count < 3;
}

// Lấy boards được assign cho TV
function getBoardsForTV($tv_id) {
    $conn = getDBConnection();
    $result = $conn->query("SELECT wb.*, ba.status as assignment_status 
                           FROM welcome_boards wb
                           JOIN board_assignments ba ON wb.id = ba.board_id
                           WHERE ba.tv_id = $tv_id AND ba.status = 'active'
                           ORDER BY wb.event_date DESC");
    $boards = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Add file modification time for cache-busting
            $filepath = __DIR__ . '/' . $row['filepath'];
            $row['mtime'] = file_exists($filepath) ? filemtime($filepath) : 0;
            $row['full_url'] = getAssetUrl($row['filepath']);
            $boards[] = $row;
        }
    }
    return $boards;
}

// Lấy tất cả boards
function getAllBoards() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM welcome_boards ORDER BY event_date DESC");
    $boards = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $boards[] = $row;
        }
    }
    return $boards;
}

// Lấy assignments cho một board
function getBoardAssignments($board_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT ba.*, tv.name as tv_name, tv.code as tv_code, d.name as department_name
                           FROM board_assignments ba
                           JOIN tv_screens tv ON ba.tv_id = tv.id
                           JOIN departments d ON tv.department_id = d.id
                           WHERE ba.board_id = ?");
    $stmt->bind_param("s", $board_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignments = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
        }
    }
    return $assignments;
}

// Lấy tất cả TV trong một department
function getDepartmentTVs($department_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM tv_screens WHERE department_id = ? AND status = 'active'");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tvs = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tvs[] = $row;
        }
    }
    return $tvs;
}

// Assign board cho tất cả TV trong department
function assignBoardToDepartment($board_id, $department_id) {
    $conn = getDBConnection();
    $tvs = getDepartmentTVs($department_id);
    $success_count = 0;
    
    foreach ($tvs as $tv) {
        $stmt = $conn->prepare("INSERT INTO board_assignments (board_id, tv_id, status) 
                               VALUES (?, ?, 'active')
                               ON DUPLICATE KEY UPDATE status = 'active', updated_at = CURRENT_TIMESTAMP");
        $stmt->bind_param("si", $board_id, $tv['id']);
        if ($stmt->execute()) {
            $success_count++;
        }
    }
    
    return ['success' => $success_count > 0, 'assigned_count' => $success_count, 'total_tvs' => count($tvs)];
}

// Assign board cho một TV cụ thể (với validation max 3 WCB)
function assignBoardToTV($board_id, $tv_id) {
    // Kiểm tra TV có thể nhận thêm WCB không
    if (!canAssignToTV($tv_id)) {
        return ['success' => false, 'message' => 'TV đã đủ 3 WCB, không thể assign thêm'];
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO board_assignments (board_id, tv_id, status) 
                           VALUES (?, ?, 'active')
                           ON DUPLICATE KEY UPDATE status = 'active', updated_at = CURRENT_TIMESTAMP");
    $stmt->bind_param("si", $board_id, $tv_id);
    $success = $stmt->execute();
    
    return ['success' => $success, 'message' => $success ? 'Assign thành công' : 'Lỗi khi assign'];
}

// Unassign board từ tất cả TV trong department
function unassignBoardFromDepartment($board_id, $department_id) {
    $conn = getDBConnection();
    $tvs = getDepartmentTVs($department_id);
    $success_count = 0;
    
    foreach ($tvs as $tv) {
        $stmt = $conn->prepare("UPDATE board_assignments SET status = 'inactive' 
                               WHERE board_id = ? AND tv_id = ?");
        $stmt->bind_param("si", $board_id, $tv['id']);
        if ($stmt->execute()) {
            $success_count++;
        }
    }
    
    return ['success' => $success_count > 0, 'unassigned_count' => $success_count];
}

// Unassign board từ một TV cụ thể
function unassignBoardFromTV($board_id, $tv_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE board_assignments SET status = 'inactive' 
                           WHERE board_id = ? AND tv_id = ?");
    $stmt->bind_param("si", $board_id, $tv_id);
    return $stmt->execute();
}
?>
