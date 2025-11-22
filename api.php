<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// CORS headers for cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Cache-Control, Pragma');

$action = $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'get_tv_boards':
            // Lấy boards cho một TV cụ thể
            $tv_code = $_GET['tv_code'] ?? '';
            
            if (empty($tv_code)) {
                echo json_encode(['success' => false, 'message' => 'Missing TV code'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            // Lấy TV ID
            $stmt = $conn->prepare("SELECT id FROM tv_screens WHERE code = ? AND status = 'active'");
            $stmt->bind_param("s", $tv_code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($tv_row = $result->fetch_assoc()) {
                $tv_id = $tv_row['id'];
                $boards = getBoardsForTV($tv_id);
                
                echo json_encode([
                    'success' => true,
                    'tv_code' => $tv_code,
                    'tv_id' => $tv_id,
                    'count' => count($boards),
                    'boards' => $boards,
                    'timestamp' => time()
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'TV not found or inactive'
                ], JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'get_departments':
            $departments = getDepartments();
            echo json_encode([
                'success' => true,
                'departments' => $departments
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_tvs':
            $department_id = $_GET['department_id'] ?? null;
            $tvs = getTVsByDepartment($department_id);
            echo json_encode([
                'success' => true,
                'tvs' => $tvs
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_all_boards':
            $boards = getAllBoards();
            echo json_encode([
                'success' => true,
                'boards' => $boards
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_board_assignments':
            $board_id = $_GET['board_id'] ?? '';
            if (empty($board_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing board ID'], JSON_UNESCAPED_UNICODE);
                break;
            }
            $assignments = getBoardAssignments($board_id);
            echo json_encode([
                'success' => true,
                'assignments' => $assignments
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_tv_board_count':
            // Lấy số WCB của một TV
            $tv_id = $_GET['tv_id'] ?? '';
            if (empty($tv_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing TV ID'], JSON_UNESCAPED_UNICODE);
                break;
            }
            $count = getTVBoardCount($tv_id);
            $can_assign = canAssignToTV($tv_id);
            echo json_encode([
                'success' => true,
                'tv_id' => $tv_id,
                'board_count' => $count,
                'can_assign' => $can_assign,
                'max_boards' => 3
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'validate_assignment':
            // Kiểm tra có thể assign board cho TV không
            $tv_id = $_GET['tv_id'] ?? '';
            if (empty($tv_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing TV ID'], JSON_UNESCAPED_UNICODE);
                break;
            }
            $can_assign = canAssignToTV($tv_id);
            $count = getTVBoardCount($tv_id);
            echo json_encode([
                'success' => true,
                'can_assign' => $can_assign,
                'current_count' => $count,
                'message' => $can_assign ? 'TV có thể nhận thêm WCB' : 'TV đã đủ 3 WCB'
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'assign_to_department':
            // Assign board cho tất cả TV trong department
            $board_id = $_POST['board_id'] ?? '';
            $department_id = $_POST['department_id'] ?? '';
            
            if (empty($board_id) || empty($department_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $result = assignBoardToDepartment($board_id, $department_id);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'assign_to_tv':
            // Assign board cho một TV cụ thể
            $board_id = $_POST['board_id'] ?? '';
            $tv_id = $_POST['tv_id'] ?? '';
            
            if (empty($board_id) || empty($tv_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            // Kiểm tra TV code để xác định max WCB
            $stmt = $conn->prepare("SELECT code FROM tv_screens WHERE id = ?");
            $stmt->bind_param("i", $tv_id);
            $stmt->execute();
            $tv_result = $stmt->get_result();
            $tv = $tv_result->fetch_assoc();
            
            $current_count = getTVBoardCount($tv_id);
            $max_wcb = ($tv && $tv['code'] === 'BASEMENT_TV1') ? 3 : 1;
            
            if ($current_count >= $max_wcb) {
                echo json_encode([
                    'success' => false, 
                    'message' => "TV đã đủ $max_wcb WCB, không thể assign thêm"
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $result = assignBoardToTV($board_id, $tv_id);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'batch_assign':
            // Assign nhiều boards cùng lúc
            $board_ids = $_POST['board_ids'] ?? [];
            $tv_ids = $_POST['tv_ids'] ?? [];
            
            if (empty($board_ids) || empty($tv_ids)) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            // Parse JSON nếu cần
            if (is_string($board_ids)) $board_ids = json_decode($board_ids, true);
            if (is_string($tv_ids)) $tv_ids = json_decode($tv_ids, true);
            
            $results = [];
            $success_count = 0;
            $error_count = 0;
            
            foreach ($board_ids as $board_id) {
                foreach ($tv_ids as $tv_id) {
                    $result = assignBoardToTV($board_id, $tv_id);
                    if ($result['success']) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                    $results[] = [
                        'board_id' => $board_id,
                        'tv_id' => $tv_id,
                        'success' => $result['success'],
                        'message' => $result['message']
                    ];
                }
            }
            
            echo json_encode([
                'success' => $success_count > 0,
                'total_assignments' => count($results),
                'success_count' => $success_count,
                'error_count' => $error_count,
                'results' => $results
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'unassign_from_department':
            // Unassign board từ tất cả TV trong department
            $board_id = $_POST['board_id'] ?? '';
            $department_id = $_POST['department_id'] ?? '';
            
            if (empty($board_id) || empty($department_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $result = unassignBoardFromDepartment($board_id, $department_id);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'unassign_from_tv':
            // Unassign board từ một TV cụ thể
            $board_id = $_POST['board_id'] ?? '';
            $tv_id = $_POST['tv_id'] ?? '';
            
            if (empty($board_id) || empty($tv_id)) {
                echo json_encode(['success' => false, 'message' => 'Missing parameters'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $success = unassignBoardFromTV($board_id, $tv_id);
            echo json_encode(['success' => $success], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'get_all_active_assignments':
            $assignments = getAllActiveAssignments();
            echo json_encode([
                'success' => true,
                'assignments' => $assignments
            ], JSON_UNESCAPED_UNICODE);
            break;

            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
?>
