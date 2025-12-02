<?php
session_start();
require_once '../config/php/config.php';

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
        
        // Ghi log (bỏ qua lỗi nếu có)
        try {
            $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, 'login', 'User logged in', ?)");
            $ip = $_SERVER['REMOTE_ADDR'];
            $logStmt->bind_param("is", $user['id'], $ip);
            $logStmt->execute();
        } catch (Exception $e) {
            error_log("Login logging error: " . $e->getMessage());
        }
        
        // Remember me
        if ($remember) {
            // TODO: Implement remember me with secure token
        }
        
        // Redirect to dashboard
        header('Location: ../index.php');
        exit;
    } else {
        // Sai mật khẩu
        header('Location: login.php?error=invalid');
        exit;
    }
} else {
    // Không tìm thấy user
    header('Location: login.php?error=invalid');
    exit;
}

$stmt->close();
$conn->close();
