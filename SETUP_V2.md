# 🚀 Hướng dẫn Setup Hệ thống Multi-TV V2

## 📋 Tổng quan

Hệ thống mới hỗ trợ quản lý Welcome Board cho nhiều TV/bộ phận:
- **FO (Front Office)**: 2 TV
- **Nhà hàng**: 1 TV  
- **Chrysan**: 1 TV
- **Lotus**: 1 TV
- **Jasmin**: 1 TV

## 🔧 Bước 1: Setup Database

```bash
# Import database V2
mysql -u root -p < database_v2.sql
```

Hoặc chạy từng lệnh trong `database_v2.sql` qua phpMyAdmin.

## 📝 Bước 2: Cấu hình Database

Sửa file `config_v2.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');      // ← Sửa
define('DB_PASS', 'your_password');      // ← Sửa
define('DB_NAME', 'wcb_system');
```

## 📂 Bước 3: Cấu trúc thư mục

Hệ thống đã tạo sẵn:

```
/
├── fo/
│   ├── tv1/index.php    → http://domain.com/fo/tv1
│   └── tv2/index.php    → http://domain.com/fo/tv2
├── restaurant/index.php → http://domain.com/restaurant
├── chrysan/index.php    → http://domain.com/chrysan
├── lotus/index.php      → http://domain.com/lotus
├── jasmin/index.php     → http://domain.com/jasmin
├── admin_v2.php         → Trang admin mới
├── api_v2.php           → API cho multi-TV
└── uploads/             → Thư mục chứa ảnh
```

## 🎯 Bước 4: Sử dụng

### Admin:

1. Truy cập: `http://domain.com/admin_v2.php`
2. Upload board mới
3. Chọn TV muốn hiển thị (có thể chọn nhiều TV)
4. Submit

### Mở TV:

**Cách 1: Từ admin**
- Bấm nút "Mở tất cả TV" để mở 6 TV cùng lúc

**Cách 2: Trực tiếp**
- FO TV1: `http://domain.com/fo/tv1`
- FO TV2: `http://domain.com/fo/tv2`
- Restaurant: `http://domain.com/restaurant`
- Chrysan: `http://domain.com/chrysan`
- Lotus: `http://domain.com/lotus`
- Jasmin: `http://domain.com/jasmin`

## 🔄 Tính năng tự động

- ✅ Tự động phát hiện thay đổi mỗi 2 giây
- ✅ Tự động refresh khi có board mới
- ✅ Tự động fullscreen sau 1 giây
- ✅ Ẩn cursor sau 3 giây không di chuyển
- ✅ Tự động chuyển board mỗi 10 giây (nếu có nhiều board)

## 📊 Database Tables

### departments
- Lưu thông tin bộ phận (FO, Restaurant, etc.)

### tv_screens  
- Lưu thông tin từng TV
- Liên kết với department

### welcome_boards
- Lưu thông tin board (ảnh, tiêu đề, ngày)

### board_assignments
- Phân bổ board cho TV (many-to-many)
- Một board có thể hiển thị trên nhiều TV
- Một TV có thể hiển thị nhiều board

## 🎨 Ví dụ sử dụng

### Scenario 1: Sự kiện chung cho tất cả
Upload 1 board → Chọn tất cả 6 TV → Tất cả TV hiển thị cùng board

### Scenario 2: Sự kiện riêng từng phòng
- Upload board Chrysan → Chọn Chrysan TV
- Upload board Lotus → Chọn Lotus TV  
- Upload board Jasmin → Chọn Jasmin TV

### Scenario 3: Sự kiện cho FO
Upload board → Chọn FO TV1 và FO TV2 → Cả 2 TV FO hiển thị

## 🔐 Bảo mật

Để bảo mật admin, thêm authentication vào `admin_v2.php`:

```php
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
```

## 🐛 Troubleshooting

### TV không tự động cập nhật?
1. Kiểm tra file `uploads/.trigger` có tồn tại không
2. Mở Console (F12) xem có lỗi API không
3. Test bằng `test_auto_update.php`

### Board không hiển thị?
1. Kiểm tra board đã được assign cho TV chưa
2. Kiểm tra status = 'active' trong `board_assignments`
3. Kiểm tra đường dẫn ảnh trong database

### Lỗi database?
1. Kiểm tra `config_v2.php` đã đúng chưa
2. Kiểm tra user có quyền truy cập database không
3. Chạy lại `database_v2.sql`

## 📞 Support

Nếu cần hỗ trợ, kiểm tra:
- Console log (F12)
- PHP error log
- MySQL error log
