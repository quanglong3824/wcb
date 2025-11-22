<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

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
            
            $success = assignBoardToTV($board_id, $tv_id);
            echo json_encode(['success' => $success], JSON_UNESCAPED_UNICODE);
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
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action'], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
?>
