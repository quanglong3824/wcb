<?php
/**
 * Forgot Password - Reset Password
 * Giới hạn: 3 lần/2 tuần, 6 lần/1 tháng
 */
session_start();
require_once '../config/php/config.php';

$message = '';
$messageType = '';
$showModal = false;
$newPassword = '';
$canReset = true;
$resetInfo = [];

// Check if system is installed
$lockFile = dirname(__DIR__) . '/.installed';
if (!file_exists($lockFile)) {
    header('Location: ../install.php');
    exit;
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $username = trim($_POST['username']);
    
    if (empty($username)) {
        $message = 'Vui lòng nhập tên đăng nhập!';
        $messageType = 'error';
    } else {
        $conn = getDBConnection();
        
        if (!$conn) {
            $message = 'Không thể kết nối database!';
            $messageType = 'error';
        } else {
            // Check if user exists
            $stmt = $conn->prepare("SELECT id, username, full_name, email FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $message = 'Tên đăng nhập không tồn tại!';
                $messageType = 'error';
            } else {
                $user = $result->fetch_assoc();
                
                // Check reset limits
                $userId = $user['id'];
                $twoWeeksAgo = date('Y-m-d H:i:s', strtotime('-2 weeks'));
                $oneMonthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
                
                // Count resets in last 2 weeks
                $stmt2 = $conn->prepare("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = ? AND action = 'password_reset' AND created_at >= ?");
                $stmt2->bind_param("is", $userId, $twoWeeksAgo);
                $stmt2->execute();
                $twoWeeksCount = $stmt2->get_result()->fetch_assoc()['count'];
                
                // Count resets in last month
                $stmt3 = $conn->prepare("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = ? AND action = 'password_reset' AND created_at >= ?");
                $stmt3->bind_param("is", $userId, $oneMonthAgo);
                $stmt3->execute();
                $oneMonthCount = $stmt3->get_result()->fetch_assoc()['count'];
                
                $resetInfo = [
                    'two_weeks' => $twoWeeksCount,
                    'one_month' => $oneMonthCount
                ];
                
                // Check limits
                if ($twoWeeksCount >= 3) {
                    $message = "Bạn đã đặt lại mật khẩu {$twoWeeksCount} lần trong 2 tuần qua. Vui lòng liên hệ quản trị viên!";
                    $messageType = 'error';
                    $canReset = false;
                } elseif ($oneMonthCount >= 6) {
                    $message = "Bạn đã đặt lại mật khẩu {$oneMonthCount} lần trong 1 tháng qua. Vui lòng liên hệ quản trị viên!";
                    $messageType = 'error';
                    $canReset = false;
                } else {
                    // Generate random password
                    $newPassword = generateRandomPassword();
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    
                    // Update password
                    $stmt4 = $conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt4->bind_param("si", $hashedPassword, $userId);
                    
                    if ($stmt4->execute()) {
                        // Log activity
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $description = "Password reset for user: {$username}";
                        $stmt5 = $conn->prepare("INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, 'password_reset', ?, ?)");
                        $stmt5->bind_param("iss", $userId, $description, $ip);
                        $stmt5->execute();
                        
                        $message = 'Đặt lại mật khẩu thành công!';
                        $messageType = 'success';
                        $showModal = true;
                    } else {
                        $message = 'Lỗi khi đặt lại mật khẩu!';
                        $messageType = 'error';
                    }
                }
            }
            
            $conn->close();
        }
    }
}

/**
 * Generate random password
 * Format: 3 chữ cái viết hoa + 3 số + 2 ký tự đặc biệt
 */
function generateRandomPassword() {
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $special = '!@#$%';
    
    $password = '';
    
    // 3 uppercase letters
    for ($i = 0; $i < 3; $i++) {
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
    }
    
    // 3 numbers
    for ($i = 0; $i < 3; $i++) {
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
    }
    
    // 2 special characters
    for ($i = 0; $i < 2; $i++) {
        $password .= $special[random_int(0, strlen($special) - 1)];
    }
    
    // Shuffle the password
    $password = str_shuffle($password);
    
    return $password;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Welcome Board System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>
        .reset-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        
        .reset-info p {
            margin: 5px 0;
        }
        
        .reset-info strong {
            color: #d4af37;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            animation: fadeIn 0.3s;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
            text-align: center;
        }
        
        .modal-header {
            margin-bottom: 20px;
        }
        
        .modal-header i {
            font-size: 4em;
            color: #10b981;
            margin-bottom: 15px;
        }
        
        .modal-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .password-display {
            background: #f8f9fa;
            border: 2px solid #d4af37;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .password-display .password {
            font-size: 2em;
            font-weight: bold;
            color: #d4af37;
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
            margin: 10px 0;
        }
        
        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 0.9em;
        }
        
        .modal-warning i {
            color: #856404;
            font-size: 1em;
        }
        
        .btn-close-modal {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-close-modal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-key"></i> Quên mật khẩu</h1>
            <p>Đặt lại mật khẩu tài khoản</p>
        </div>
        
        <div class="login-body">
            <div class="message-area">
                <?php if ($message): ?>
                    <p class="message message-<?php echo $messageType; ?>">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="reset-info">
                <p><i class="fas fa-info-circle"></i> <strong>Giới hạn đặt lại mật khẩu:</strong></p>
                <p>• Tối đa <strong>3 lần</strong> trong 2 tuần</p>
                <p>• Tối đa <strong>6 lần</strong> trong 1 tháng</p>
                <?php if (!empty($resetInfo)): ?>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid #b3d9ff;">
                    <p>Bạn đã đặt lại: <strong><?php echo $resetInfo['two_weeks']; ?>/3</strong> lần (2 tuần) | <strong><?php echo $resetInfo['one_month']; ?>/6</strong> lần (1 tháng)</p>
                <?php endif; ?>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Nhập tên đăng nhập của bạn"
                               required 
                               autofocus
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                </div>
                
                <button type="submit" name="reset_password" class="btn-login" <?php echo !$canReset ? 'disabled' : ''; ?>>
                    <i class="fas fa-sync-alt"></i>
                    Đặt lại mật khẩu
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" style="color: #d4af37; text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                </a>
            </div>
        </div>
        
        <div class="login-footer">
            <p>
                <i class="fas fa-shield-alt"></i> Mật khẩu mới sẽ được tạo ngẫu nhiên và chỉ hiển thị 1 lần duy nhất
            </p>
        </div>
    </div>
    
    <!-- Modal hiển thị mật khẩu mới -->
    <div id="passwordModal" class="modal <?php echo $showModal ? 'show' : ''; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-check-circle"></i>
                <h2>Mật khẩu mới của bạn</h2>
                <p>Vui lòng ghi nhớ mật khẩu này!</p>
            </div>
            
            <div class="password-display">
                <p style="margin: 0; color: #666;">Mật khẩu mới:</p>
                <div class="password"><?php echo htmlspecialchars($newPassword); ?></div>
            </div>
            
            <div class="modal-warning">
                <p><i class="fas fa-exclamation-triangle"></i> <strong>Cảnh báo quan trọng:</strong></p>
                <p>Mật khẩu này chỉ hiển thị <strong>DUY NHẤT 1 LẦN</strong>. Vui lòng ghi chú lại ngay!</p>
                <p>Sau khi đóng cửa sổ này, bạn sẽ không thể xem lại mật khẩu.</p>
            </div>
            
            <button class="btn-close-modal" onclick="closeModal()">
                <i class="fas fa-check"></i> Tôi đã ghi nhớ, đóng cửa sổ
            </button>
        </div>
    </div>
    
    <script>
        function closeModal() {
            const modal = document.getElementById('passwordModal');
            modal.classList.remove('show');
            
            // Redirect to login after 500ms
            setTimeout(() => {
                window.location.href = 'login.php?password_reset=success';
            }, 500);
        }
        
        // Prevent closing modal by clicking outside
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                alert('Vui lòng ghi nhớ mật khẩu trước khi đóng!');
            }
        });
        
        // Prevent closing by ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('passwordModal').classList.contains('show')) {
                e.preventDefault();
                alert('Vui lòng ghi nhớ mật khẩu trước khi đóng!');
            }
        });
    </script>
</body>
</html>
