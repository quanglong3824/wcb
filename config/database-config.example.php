<?php
/**
 * Database Configuration Example
 * Copy file này thành database.php và điền thông tin của bạn
 */

// =====================================================
// CẤU HÌNH CHO LOCALHOST
// =====================================================
define('DB_LOCAL_HOST', 'localhost');
define('DB_LOCAL_USER', 'root');
define('DB_LOCAL_PASS', '');
define('DB_LOCAL_NAME', 'auroraho_wcb');
define('DB_LOCAL_PORT', 3306);

// =====================================================
// CẤU HÌNH CHO HOST/PRODUCTION
// =====================================================
// Thay đổi các thông tin sau theo cấu hình host của bạn
define('DB_REMOTE_HOST', 'localhost');
define('DB_REMOTE_USER', 'auroraho_wcbuser');
define('DB_REMOTE_PASS', 'your_secure_password_here');
define('DB_REMOTE_NAME', 'auroraho_wcb');
define('DB_REMOTE_PORT', 3306);

// =====================================================
// HƯỚNG DẪN CẤU HÌNH
// =====================================================

/*
1. LOCALHOST (Development):
   - Host: localhost hoặc 127.0.0.1
   - User: root (mặc định XAMPP/WAMP)
   - Pass: để trống (XAMPP) hoặc root (MAMP)
   - Database: auroraho_wcb

2. REMOTE HOST (Production):
   - Lấy thông tin từ cPanel > MySQL Databases
   - Host: thường là localhost hoặc IP server
   - User: username được tạo trong cPanel
   - Pass: password bạn đã đặt
   - Database: tên database đã tạo

3. CẤU TRÚC TÊN DATABASE TRÊN HOST:
   Thường có dạng: cpanel_username_dbname
   Ví dụ: auroraho_wcb

4. KIỂM TRA KẾT NỐI:
   Truy cập: http://your-domain.com/check-database.php
   
5. BẢO MẬT:
   - Không commit file database.php lên Git
   - Sử dụng mật khẩu mạnh cho production
   - Giới hạn quyền truy cập database
*/
