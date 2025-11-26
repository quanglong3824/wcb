# Hướng dẫn cấu hình Database

## 📋 Tổng quan

Hệ thống WCB hỗ trợ kết nối song song giữa **localhost** (development) và **remote host** (production). Hệ thống tự động phát hiện môi trường và sử dụng cấu hình phù hợp.

## 🔧 Cài đặt Database

### 1. Tạo Database

#### Trên Localhost:
```sql
CREATE DATABASE auroraho_wcb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Trên cPanel (Host):
1. Đăng nhập cPanel
2. Vào **MySQL Databases**
3. Tạo database mới: `auroraho_wcb`
4. Tạo user mới và gán quyền ALL PRIVILEGES

### 2. Import Database

```bash
# Trên localhost
mysql -u root -p auroraho_wcb < database.sql

# Hoặc sử dụng phpMyAdmin
# Import file database.sql qua giao diện web
```

### 3. Cấu hình kết nối

File cấu hình: `config/php/database.php`

```php
// Cấu hình LOCALHOST
define('DB_LOCAL_HOST', 'localhost');
define('DB_LOCAL_USER', 'root');
define('DB_LOCAL_PASS', '');
define('DB_LOCAL_NAME', 'auroraho_wcb');

// Cấu hình REMOTE HOST
define('DB_REMOTE_HOST', 'localhost');
define('DB_REMOTE_USER', 'auroraho_wcbuser');
define('DB_REMOTE_PASS', 'your_password');
define('DB_REMOTE_NAME', 'auroraho_wcb');
```

## 🔍 Kiểm tra kết nối

Truy cập: `http://localhost/your-project/check-database.php`

Trang này sẽ hiển thị:
- ✅ Trạng thái kết nối
- 🌍 Môi trường hiện tại (LOCAL/REMOTE)
- 📊 Thông tin database
- 📋 Danh sách bảng
- 🔌 Trạng thái extensions
- 💻 Thông tin server

## 🎯 Cách hoạt động

### Tự động phát hiện môi trường

```php
// Hệ thống tự động phát hiện dựa trên HTTP_HOST
$isLocalhost = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1', '::1']);

if ($isLocalhost) {
    // Sử dụng cấu hình LOCAL
    define('DB_ENVIRONMENT', 'LOCAL');
} else {
    // Sử dụng cấu hình REMOTE
    define('DB_ENVIRONMENT', 'REMOTE');
}
```

### Kết nối Database

Hệ thống hỗ trợ 2 phương thức:

#### 1. MySQLi (Recommended)
```php
$conn = getDBConnection();
$result = $conn->query("SELECT * FROM users");
```

#### 2. PDO
```php
$pdo = getPDOConnection();
$stmt = $pdo->query("SELECT * FROM users");
```

## 📚 Helper Functions

### dbQuery() - Truy vấn SELECT
```php
// Không có parameters
$users = dbQuery("SELECT * FROM users");

// Với parameters (prepared statement)
$users = dbQuery("SELECT * FROM users WHERE role = ?", ['admin']);
```

### dbExecute() - INSERT/UPDATE/DELETE
```php
// INSERT
$userId = dbExecute(
    "INSERT INTO users (username, password) VALUES (?, ?)",
    ['admin', 'hashed_password']
);

// UPDATE
$result = dbExecute(
    "UPDATE users SET status = ? WHERE id = ?",
    ['active', 1]
);

// DELETE
$result = dbExecute("DELETE FROM users WHERE id = ?", [5]);
```

### dbEscape() - Escape string
```php
$safe = dbEscape($_POST['username']);
```

## 🗂️ Cấu trúc Database

### Bảng chính:
- `users` - Tài khoản người dùng
- `tvs` - Danh sách 7 màn hình TV
- `media` - Thư viện nội dung
- `schedules` - Lịch chiếu
- `tv_media_assignments` - Gán nội dung cho TV
- `activity_logs` - Lịch sử hoạt động
- `system_settings` - Cấu hình hệ thống
- `tv_heartbeats` - Theo dõi trạng thái TV

### Views:
- `view_tv_status` - Trạng thái TV với nội dung
- `view_active_schedules` - Lịch chiếu đang hoạt động
- `view_media_stats` - Thống kê media

### Stored Procedures:
- `sp_update_tv_status()` - Cập nhật trạng thái TV
- `sp_get_tv_content()` - Lấy nội dung cho TV
- `sp_update_schedule_status()` - Cập nhật trạng thái lịch

### Events (Tự động):
- `evt_update_tv_status` - Chạy mỗi phút
- `evt_update_schedule_status` - Chạy mỗi phút
- `evt_cleanup_old_logs` - Chạy hàng ngày 2h sáng
- `evt_cleanup_old_heartbeats` - Chạy hàng ngày 3h sáng

## 🔐 Bảo mật

### 1. Không commit file cấu hình
File `config/php/database.php` đã được thêm vào `.gitignore`

### 2. Sử dụng Prepared Statements
```php
// ✅ ĐÚNG - An toàn
$users = dbQuery("SELECT * FROM users WHERE id = ?", [$id]);

// ❌ SAI - Dễ bị SQL Injection
$users = dbQuery("SELECT * FROM users WHERE id = $id");
```

### 3. Mật khẩu mạnh
- Sử dụng mật khẩu phức tạp cho production
- Không sử dụng mật khẩu mặc định

### 4. Giới hạn quyền
- User database chỉ cần quyền: SELECT, INSERT, UPDATE, DELETE
- Không cần quyền DROP, CREATE (trừ khi cần thiết)

## 🚀 Deploy lên Host

### Bước 1: Upload files
```bash
# Upload tất cả files trừ:
- config/php/database.php (tạo mới trên host)
- uploads/* (upload riêng nếu cần)
```

### Bước 2: Tạo database trên cPanel
1. MySQL Databases → Create New Database
2. MySQL Users → Create New User
3. Add User To Database → ALL PRIVILEGES

### Bước 3: Cấu hình database.php
```php
define('DB_REMOTE_HOST', 'localhost');
define('DB_REMOTE_USER', 'cpanel_user_dbuser');
define('DB_REMOTE_PASS', 'secure_password');
define('DB_REMOTE_NAME', 'cpanel_user_auroraho_wcb');
```

### Bước 4: Import database
- Sử dụng phpMyAdmin trên cPanel
- Import file `database.sql`

### Bước 5: Kiểm tra
- Truy cập: `https://yourdomain.com/check-database.php`
- Xác nhận kết nối thành công

## 🆘 Xử lý lỗi

### Lỗi: "Access denied for user"
```
Nguyên nhân: Sai username/password
Giải pháp: Kiểm tra lại thông tin trong database.php
```

### Lỗi: "Unknown database"
```
Nguyên nhân: Database chưa được tạo
Giải pháp: Tạo database trong cPanel hoặc phpMyAdmin
```

### Lỗi: "Can't connect to MySQL server"
```
Nguyên nhân: MySQL service không chạy hoặc sai host
Giải pháp: 
- Kiểm tra MySQL service
- Thử đổi host thành '127.0.0.1'
```

### Lỗi: "Table doesn't exist"
```
Nguyên nhân: Chưa import database.sql
Giải pháp: Import file database.sql
```

## 📞 Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. File `check-database.php` để xem chi tiết lỗi
2. PHP error log
3. MySQL error log

## 📝 Tài khoản mặc định

Sau khi import database:

**Super Admin:**
- Username: `admin`
- Password: `admin123`

**Content Manager:**
- Username: `manager`
- Password: `admin123`

⚠️ **Quan trọng:** Đổi mật khẩu ngay sau khi đăng nhập lần đầu!

## 🔄 Backup Database

### Tự động (Recommended)
```bash
# Tạo cron job chạy hàng ngày
0 2 * * * mysqldump -u root -p auroraho_wcb > /backup/wcb_$(date +\%Y\%m\%d).sql
```

### Thủ công
```bash
# Export
mysqldump -u root -p auroraho_wcb > backup.sql

# Import
mysql -u root -p auroraho_wcb < backup.sql
```

## 📊 Monitoring

Hệ thống tự động:
- Cập nhật trạng thái TV mỗi phút
- Cập nhật trạng thái lịch chiếu mỗi phút
- Xóa log cũ hơn 90 ngày
- Xóa heartbeat cũ hơn 7 ngày

Kiểm tra events:
```sql
SHOW EVENTS;
SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = 'auroraho_wcb';
```
