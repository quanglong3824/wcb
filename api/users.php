<?php
/**
 * Users API
 * CRUD operations for users management
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';
require_once '../includes/permissions.php';

header('Content-Type: application/json');

// Kiểm tra quyền dựa trên method
$method = $_SERVER['REQUEST_METHOD'];

// GET - cần quyền view
if ($method === 'GET' && !hasPermission('users', PERM_VIEW)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền xem danh sách người dùng']);
    exit;
}

// POST - cần quyền create
if ($method === 'POST' && !isset($_GET['action']) && !hasPermission('users', PERM_CREATE)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền tạo người dùng']);
    exit;
}

// POST reset_password - cần quyền edit
if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reset_password' && !hasPermission('users', PERM_EDIT)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền đặt lại mật khẩu']);
    exit;
}

// PUT - cần quyền edit
if ($method === 'PUT' && !hasPermission('users', PERM_EDIT)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền chỉnh sửa người dùng']);
    exit;
}

// DELETE - cần quyền delete
if ($method === 'DELETE' && !hasPermission('users', PERM_DELETE)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Không có quyền xóa người dùng']);
    exit;
}

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'get_permissions') {
            getUserPermissions($conn);
        } else {
            getUsers($conn);
        }
        break;
    case 'POST':
        if (isset($_GET['action']) && $_GET['action'] === 'reset_password') {
            resetPassword($conn);
        } else {
            createUser($conn);
        }
        break;
    case 'PUT':
        updateUser($conn);
        break;
    case 'DELETE':
        deleteUser($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

$conn->close();

/**
 * Get users list with pagination and filters
 */
function getUsers($conn) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 10;
    $offset = ($page - 1) * $limit;
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role = isset($_GET['role']) ? trim($_GET['role']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $where[] = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'sss';
    }
    
    if (!empty($role)) {
        $where[] = "role = ?";
        $params[] = $role;
        $types .= 's';
    }
    
    if (!empty($status)) {
        $where[] = "status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM users {$whereClause}";
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
    
    // Get users
    $query = "SELECT id, username, full_name, email, role, status, created_at, last_login 
              FROM users 
              {$whereClause} 
              ORDER BY id DESC 
              LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    // Get stats
    $stats = [
        'total' => $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'],
        'active' => $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'active'")->fetch_assoc()['c'],
        'inactive' => $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'inactive'")->fetch_assoc()['c'],
        'admins' => $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'super_admin'")->fetch_assoc()['c']
    ];
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'total_pages' => $totalPages,
        'stats' => $stats
    ]);
}

/**
 * Create new user
 */
function createUser($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Validate required fields
    $required = ['username', 'full_name', 'password', 'role', 'status'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Trường {$field} là bắt buộc"]);
            return;
        }
    }
    
    $username = trim($data['username']);
    $fullName = trim($data['full_name']);
    $email = isset($data['email']) ? trim($data['email']) : null;
    $password = $data['password'];
    $role = $data['role'];
    $status = $data['status'];
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Username chỉ được chứa chữ cái, số và dấu gạch dưới']);
        return;
    }
    
    // Check if username exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username đã tồn tại']);
        return;
    }
    
    // Validate password length
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $username, $fullName, $email, $hashedPassword, $role, $status);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        
        // Save custom permissions if provided
        if (isset($data['custom_permissions']) && $data['custom_permissions'] !== null && $role !== 'super_admin') {
            saveUserPermissions($conn, $userId, $data['custom_permissions']);
        }
        
        // Log activity
        logActivity($conn, 'create_user', 'user', $userId, "Tạo người dùng: {$username}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Tạo người dùng thành công',
            'user_id' => $userId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo người dùng: ' . $conn->error]);
    }
}

/**
 * Update user
 */
function updateUser($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if user exists
    $checkStmt = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $existingUser = $checkStmt->get_result()->fetch_assoc();
    
    if (!$existingUser) {
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        return;
    }
    
    // Build update query
    $updates = [];
    $params = [];
    $types = '';
    
    if (isset($data['full_name'])) {
        $updates[] = "full_name = ?";
        $params[] = trim($data['full_name']);
        $types .= 's';
    }
    
    if (isset($data['email'])) {
        $updates[] = "email = ?";
        $params[] = trim($data['email']) ?: null;
        $types .= 's';
    }
    
    if (isset($data['role'])) {
        $updates[] = "role = ?";
        $params[] = $data['role'];
        $types .= 's';
    }
    
    if (isset($data['status'])) {
        // Không cho phép tự khóa chính mình
        if ($id == $_SESSION['user_id'] && $data['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Không thể khóa tài khoản của chính mình']);
            return;
        }
        $updates[] = "status = ?";
        $params[] = $data['status'];
        $types .= 's';
    }
    
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự']);
            return;
        }
        $updates[] = "password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        $types .= 's';
    }
    
    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'Không có dữ liệu để cập nhật']);
        return;
    }
    
    $updates[] = "updated_at = NOW()";
    
    $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $params[] = $id;
    $types .= 'i';
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Save custom permissions if provided
        $userRole = isset($data['role']) ? $data['role'] : null;
        if (isset($data['custom_permissions'])) {
            if ($data['custom_permissions'] === null || $userRole === 'super_admin') {
                // Clear custom permissions
                saveUserPermissions($conn, $id, []);
            } else {
                saveUserPermissions($conn, $id, $data['custom_permissions']);
            }
        }
        
        // Log activity
        logActivity($conn, 'update_user', 'user', $id, "Cập nhật người dùng: {$existingUser['username']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $conn->error]);
    }
}

/**
 * Delete user
 */
function deleteUser($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    // Không cho phép xóa chính mình
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản của chính mình']);
        return;
    }
    
    // Check if user exists
    $checkStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $user = $checkStmt->get_result()->fetch_assoc();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        return;
    }
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Log activity
        logActivity($conn, 'delete_user', 'user', $id, "Xóa người dùng: {$user['username']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa: ' . $conn->error]);
    }
}

/**
 * Reset user password
 */
function resetPassword($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    // Không cho phép reset mật khẩu của chính mình qua API này
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng sử dụng chức năng đổi mật khẩu trong Profile']);
        return;
    }
    
    // Check if user exists
    $checkStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    $user = $checkStmt->get_result()->fetch_assoc();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        return;
    }
    
    // Generate new password
    $newPassword = generateRandomPassword(10);
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $id);
    
    if ($stmt->execute()) {
        // Log activity
        logActivity($conn, 'reset_password', 'user', $id, "Đặt lại mật khẩu cho: {$user['username']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Đặt lại mật khẩu thành công',
            'new_password' => $newPassword
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi đặt lại mật khẩu: ' . $conn->error]);
    }
}

/**
 * Generate random password
 */
function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
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

/**
 * Get user permissions
 */
function getUserPermissions($conn) {
    $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        return;
    }
    
    $stmt = $conn->prepare("SELECT module, can_view, can_create, can_edit, can_delete FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $perms = [];
        if ($row['can_view']) $perms[] = 'view';
        if ($row['can_create']) $perms[] = 'create';
        if ($row['can_edit']) $perms[] = 'edit';
        if ($row['can_delete']) $perms[] = 'delete';
        $permissions[$row['module']] = $perms;
    }
    
    echo json_encode([
        'success' => true,
        'permissions' => $permissions
    ]);
}

/**
 * Save user permissions
 */
function saveUserPermissions($conn, $userId, $permissions) {
    if (empty($permissions)) {
        // Delete all custom permissions
        $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return true;
    }
    
    // Delete old permissions
    $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Insert new permissions
    $stmt = $conn->prepare("INSERT INTO user_permissions (user_id, module, can_view, can_create, can_edit, can_delete) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($permissions as $module => $perms) {
        $canView = in_array('view', $perms) ? 1 : 0;
        $canCreate = in_array('create', $perms) ? 1 : 0;
        $canEdit = in_array('edit', $perms) ? 1 : 0;
        $canDelete = in_array('delete', $perms) ? 1 : 0;
        
        $stmt->bind_param("isiiii", $userId, $module, $canView, $canCreate, $canEdit, $canDelete);
        $stmt->execute();
    }
    
    return true;
}
