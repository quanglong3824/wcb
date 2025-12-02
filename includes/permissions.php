<?php
/**
 * Permission System
 * Định nghĩa quyền truy cập cho từng role
 * Hỗ trợ quyền tùy chỉnh cho từng user
 * 
 * Roles:
 * - super_admin: Toàn quyền
 * - content_manager: Quản lý nội dung (TV, WCB, Upload, Schedule)
 * - user: Chỉ xem (readonly) - có thể tùy chỉnh
 */

// Định nghĩa các quyền
define('PERM_VIEW', 'view');           // Xem
define('PERM_CREATE', 'create');       // Tạo mới
define('PERM_EDIT', 'edit');           // Chỉnh sửa
define('PERM_DELETE', 'delete');       // Xóa
define('PERM_FULL', 'full');           // Toàn quyền

// Danh sách tất cả modules
$ALL_MODULES = [
    'dashboard'   => ['name' => 'Dashboard', 'icon' => 'fas fa-home'],
    'tv_monitor'  => ['name' => 'Giám sát TV', 'icon' => 'fas fa-desktop'],
    'tv_manage'   => ['name' => 'Quản lý TV', 'icon' => 'fas fa-tv'],
    'wcb_manage'  => ['name' => 'Quản lý WCB', 'icon' => 'fas fa-image'],
    'upload'      => ['name' => 'Upload', 'icon' => 'fas fa-cloud-upload-alt'],
    'schedule'    => ['name' => 'Lịch chiếu', 'icon' => 'fas fa-calendar-alt'],
    'settings'    => ['name' => 'Cài đặt', 'icon' => 'fas fa-cog'],
    'users'       => ['name' => 'Quản lý Users', 'icon' => 'fas fa-users'],
    'logs'        => ['name' => 'Activity Logs', 'icon' => 'fas fa-history'],
    'backup'      => ['name' => 'Backup', 'icon' => 'fas fa-database'],
];

// Ma trận phân quyền mặc định theo role
$DEFAULT_PERMISSIONS = [
    'super_admin' => [
        'dashboard'     => [PERM_VIEW, PERM_FULL],
        'tv_monitor'    => [PERM_VIEW, PERM_FULL],
        'tv_manage'     => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'wcb_manage'    => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'upload'        => [PERM_VIEW, PERM_CREATE, PERM_DELETE],
        'schedule'      => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'settings'      => [PERM_VIEW, PERM_EDIT],
        'users'         => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'logs'          => [PERM_VIEW],
        'backup'        => [PERM_VIEW, PERM_CREATE, PERM_DELETE],
        'profile'       => [PERM_VIEW, PERM_EDIT],
    ],
    'content_manager' => [
        'dashboard'     => [PERM_VIEW],
        'tv_monitor'    => [PERM_VIEW],
        'tv_manage'     => [PERM_VIEW, PERM_CREATE, PERM_EDIT],
        'wcb_manage'    => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'upload'        => [PERM_VIEW, PERM_CREATE, PERM_DELETE],
        'schedule'      => [PERM_VIEW, PERM_CREATE, PERM_EDIT, PERM_DELETE],
        'settings'      => [PERM_VIEW],
        'users'         => [PERM_VIEW],
        'logs'          => [PERM_VIEW],
        'backup'        => [PERM_VIEW],
        'profile'       => [PERM_VIEW, PERM_EDIT],
    ],
    'user' => [
        'dashboard'     => [PERM_VIEW],
        'tv_monitor'    => [PERM_VIEW],
        'tv_manage'     => [PERM_VIEW],
        'wcb_manage'    => [PERM_VIEW],
        'upload'        => [PERM_VIEW],
        'schedule'      => [PERM_VIEW],
        'settings'      => [PERM_VIEW],
        'users'         => [PERM_VIEW],
        'logs'          => [PERM_VIEW],
        'backup'        => [PERM_VIEW],
        'profile'       => [PERM_VIEW, PERM_EDIT],
    ],
];

// Cache cho custom permissions
$_customPermissionsCache = null;

/**
 * Lấy quyền tùy chỉnh của user từ database
 * @param int $userId
 * @return array
 */
function getCustomPermissions($userId) {
    global $_customPermissionsCache;
    
    if ($_customPermissionsCache !== null && isset($_customPermissionsCache[$userId])) {
        return $_customPermissionsCache[$userId];
    }
    
    $conn = getDBConnection();
    if (!$conn) return [];
    
    $stmt = $conn->prepare("SELECT module, can_view, can_create, can_edit, can_delete FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $perms = [];
        if ($row['can_view']) $perms[] = PERM_VIEW;
        if ($row['can_create']) $perms[] = PERM_CREATE;
        if ($row['can_edit']) $perms[] = PERM_EDIT;
        if ($row['can_delete']) $perms[] = PERM_DELETE;
        $permissions[$row['module']] = $perms;
    }
    
    $_customPermissionsCache[$userId] = $permissions;
    return $permissions;
}

/**
 * Lưu quyền tùy chỉnh cho user
 * @param int $userId
 * @param array $permissions
 * @return bool
 */
function saveCustomPermissions($userId, $permissions) {
    global $_customPermissionsCache;
    
    $conn = getDBConnection();
    if (!$conn) return false;
    
    // Xóa quyền cũ
    $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    
    // Thêm quyền mới
    $stmt = $conn->prepare("INSERT INTO user_permissions (user_id, module, can_view, can_create, can_edit, can_delete) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($permissions as $module => $perms) {
        $canView = in_array(PERM_VIEW, $perms) ? 1 : 0;
        $canCreate = in_array(PERM_CREATE, $perms) ? 1 : 0;
        $canEdit = in_array(PERM_EDIT, $perms) ? 1 : 0;
        $canDelete = in_array(PERM_DELETE, $perms) ? 1 : 0;
        
        $stmt->bind_param("isiiii", $userId, $module, $canView, $canCreate, $canEdit, $canDelete);
        $stmt->execute();
    }
    
    // Clear cache
    $_customPermissionsCache = null;
    
    return true;
}

// Biến để lưu PERMISSIONS (sẽ được merge với custom permissions)
$PERMISSIONS = $DEFAULT_PERMISSIONS;

/**
 * Kiểm tra quyền của user hiện tại
 * Ưu tiên: Custom permissions > Default role permissions
 * @param string $module Module cần kiểm tra (dashboard, tv_manage, etc.)
 * @param string $permission Quyền cần kiểm tra (view, create, edit, delete)
 * @return bool
 */
function hasPermission($module, $permission = PERM_VIEW) {
    global $DEFAULT_PERMISSIONS;
    
    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    
    // Super admin luôn có toàn quyền
    if ($role === 'super_admin') {
        if (!isset($DEFAULT_PERMISSIONS[$role][$module])) {
            return false;
        }
        return in_array($permission, $DEFAULT_PERMISSIONS[$role][$module]) || 
               in_array(PERM_FULL, $DEFAULT_PERMISSIONS[$role][$module]);
    }
    
    // Kiểm tra custom permissions trước
    $customPerms = getCustomPermissions($userId);
    if (!empty($customPerms) && isset($customPerms[$module])) {
        return in_array($permission, $customPerms[$module]);
    }
    
    // Fallback về default permissions của role
    if (!isset($DEFAULT_PERMISSIONS[$role])) {
        return false;
    }
    
    if (!isset($DEFAULT_PERMISSIONS[$role][$module])) {
        return false;
    }
    
    return in_array($permission, $DEFAULT_PERMISSIONS[$role][$module]) || 
           in_array(PERM_FULL, $DEFAULT_PERMISSIONS[$role][$module]);
}

/**
 * Kiểm tra xem module có readonly không
 * @param string $module
 * @return bool
 */
function isReadOnly($module) {
    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
    
    // Super admin không bao giờ readonly
    if ($role === 'super_admin') {
        return false;
    }
    
    // Kiểm tra xem có quyền edit/create/delete không
    return !hasPermission($module, PERM_CREATE) && 
           !hasPermission($module, PERM_EDIT) && 
           !hasPermission($module, PERM_DELETE);
}

/**
 * Lấy danh sách quyền của user hiện tại cho một module
 * @param string $module
 * @return array
 */
function getPermissions($module) {
    global $PERMISSIONS;
    
    $role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
    
    if (!isset($PERMISSIONS[$role][$module])) {
        return [];
    }
    
    return $PERMISSIONS[$role][$module];
}

/**
 * Lấy role hiện tại
 * @return string
 */
function getCurrentRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
}

/**
 * Lấy tên hiển thị của role
 * @param string $role
 * @return string
 */
function getRoleName($role) {
    $names = [
        'super_admin' => 'Super Admin',
        'content_manager' => 'Content Manager',
        'user' => 'User'
    ];
    return isset($names[$role]) ? $names[$role] : $role;
}

/**
 * Tạo HTML cho badge readonly
 * @return string
 */
function getReadOnlyBadge() {
    return '<span class="readonly-badge"><i class="fas fa-lock"></i> Chỉ xem</span>';
}

/**
 * Tạo attribute disabled nếu không có quyền
 * @param string $module
 * @param string $permission
 * @return string
 */
function disabledIfNoPermission($module, $permission = PERM_EDIT) {
    return hasPermission($module, $permission) ? '' : 'disabled';
}

/**
 * Tạo class readonly nếu không có quyền
 * @param string $module
 * @return string
 */
function readonlyClass($module) {
    return isReadOnly($module) ? 'readonly-mode' : '';
}

/**
 * Lấy icons hiển thị quyền cho module
 * @param string $module
 * @return array ['icons' => HTML, 'title' => tooltip text]
 */
function getModulePermissionIcons($module) {
    $canView = hasPermission($module, PERM_VIEW);
    $canCreate = hasPermission($module, PERM_CREATE);
    $canEdit = hasPermission($module, PERM_EDIT);
    $canDelete = hasPermission($module, PERM_DELETE);
    
    $icons = '';
    $titles = [];
    
    if ($canView) {
        $icons .= '<i class="fas fa-eye perm-icon perm-view" title="Xem"></i>';
        $titles[] = 'Xem';
    }
    if ($canCreate) {
        $icons .= '<i class="fas fa-plus perm-icon perm-create" title="Tạo"></i>';
        $titles[] = 'Tạo';
    }
    if ($canEdit) {
        $icons .= '<i class="fas fa-edit perm-icon perm-edit" title="Sửa"></i>';
        $titles[] = 'Sửa';
    }
    if ($canDelete) {
        $icons .= '<i class="fas fa-trash perm-icon perm-delete" title="Xóa"></i>';
        $titles[] = 'Xóa';
    }
    
    // Nếu chỉ có view, hiển thị ổ khóa
    if ($canView && !$canCreate && !$canEdit && !$canDelete) {
        $icons = '<i class="fas fa-lock perm-icon perm-readonly" title="Chỉ xem"></i>';
        $titles = ['Chỉ xem'];
    }
    
    return [
        'icons' => $icons,
        'title' => 'Quyền: ' . implode(', ', $titles)
    ];
}

/**
 * Kiểm tra và hiển thị trang 403 nếu readonly
 * Gọi hàm này sau khi include header và sidebar
 * @param string $module
 * @param string $cssFile (optional)
 * @return bool - true nếu đã hiển thị 403 và cần exit
 */
function checkReadonlyAccess($module, $cssFile = null) {
    global $currentModule;
    $currentModule = $module;
    
    if (isReadOnly($module)) {
        echo '<main class="main-content">';
        if ($cssFile) {
            echo '<link rel="stylesheet" href="' . $cssFile . '">';
        }
        include __DIR__ . '/no-permission.php';
        echo '</main>';
        include __DIR__ . '/footer.php';
        return true;
    }
    return false;
}
