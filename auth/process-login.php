<?php
session_start();
require_once '../config/php/config.php';
require_once '../includes/logger.php';

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Lấy dữ liệu từ form
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$remember = isset($_POST['remember']);

// Validate input
if (empty($username) || empty($password)) {
    header('Location: login.php?error=empty');
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    header('Location: login.php?error=connection');
    exit;
}

// Tìm user trong database
$stmt = $conn->prepare("SELECT id, username, password, full_name, email, role, status FROM users WHERE username = ? AND status = 'active'");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
        $_SESSION['user_role'] = !empty($user['role']) ? $user['role'] : 'content_manager';
        $_SESSION['user_email'] = $user['email'] ?? '';

        // Cập nhật last_login
        $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $user['id']);
        $updateStmt->execute();

        // Ghi log
        logActivity($conn, 'login', null, null, 'User logged in', $user['id']);

        // Remember me
        if ($remember) {
            // TODO: Implement remember me with secure token
        }

        // Redirect to dashboard
        header('Location: ../index.php');
        exit;
    } else {
        // Sai mật khẩu
        logActivity($conn, 'login_failed', 'user', $user['id'], "Failed login attempt for {$username} (Wrong Password)", $user['id']);
        header('Location: login.php?error=invalid');
        exit;
    }
} else {
    // Không tìm thấy user
    // Log failed attempt with user_id=0
    logActivity($conn, 'login_failed', null, null, "Failed login attempt for {$username} (User not found)", 0);
    header('Location: login.php?error=invalid');
    exit;
}

$stmt->close();
$conn->close();