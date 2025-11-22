# 🚀 Quick Start Guide

Hướng dẫn nhanh để chạy WCB System trong 5 phút.

## 📦 Bước 1: Upload file (2 phút)

Upload toàn bộ file lên server vào thư mục `/public_html/wcb/`

```bash
# Hoặc dùng FTP/SFTP
# Hoặc dùng cPanel File Manager
```

## 🔧 Bước 2: Cài đặt (2 phút)

### Option A: Sử dụng Installer (Khuyến nghị)

1. Truy cập: `https://aurorahotelplaza.com/wcb/test.php`
2. Kiểm tra các yêu cầu hệ thống
3. Click "Chạy cài đặt"
4. Nhập thông tin database:
   - Host: `localhost`
   - Username: `your_db_user`
   - Password: `your_db_pass`
   - Database: `wcb`
5. Click "Cài đặt ngay"

### Option B: Cài đặt thủ công

```bash
# 1. Tạo database
mysql -u root -p
CREATE DATABASE wcb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# 2. Import schema
mysql -u root -p wcb < database.sql

# 3. Tạo file config.php
cp config.php.example config.php
nano config.php
# Sửa thông tin database

# 4. Tạo thư mục
mkdir uploads backups logs
chmod 755 uploads backups logs
```

## ✅ Bước 3: Kiểm tra (1 phút)

Truy cập: `https://aurorahotelplaza.com/wcb/test.php`

Đảm bảo tất cả test đều PASS ✓

## 🎉 Bước 4: Sử dụng

### Upload Welcome Board
1. Vào: `https://aurorahotelplaza.com/wcb/`
2. Phần "Phòng Kinh Doanh"
3. Chọn ngày, nhập tiêu đề, upload ảnh
4. Click "Upload Welcome Board"

### Kích hoạt hiển thị
1. Vào phần "Admin"
2. Chọn board cần hiển thị
3. Click "Kích hoạt hiển thị"
4. Có thể kích hoạt tối đa 3 board cùng lúc

### Chiếu lên màn hình

**Cách 1: Trên cùng 1 máy**
- Click "Chiếu màn hình" → Nhấn F11

**Cách 2: Điều khiển từ xa (Khuyến nghị)**

Trên TV:
```
1. Mở Chrome/Firefox
2. Vào: https://aurorahotelplaza.com/wcb/display.php
3. Nhấn F11 (fullscreen)
4. Màn hình đen chờ kích hoạt
```

Trên điện thoại:
```
1. Vào: https://aurorahotelplaza.com/wcb/
2. Phần Admin → Chọn board → Kích hoạt
3. TV tự động cập nhật sau 60s (hoặc nhấn R)
```

## 🔑 Phím tắt

Trên màn hình chiếu:
- `F11` - Fullscreen
- `←` `→` - Chuyển board
- `I` - Hiện/ẩn thông tin
- `R` - Refresh
- `Esc` - Thoát fullscreen

## 📱 Sử dụng trên Mobile

- Swipe trái/phải để chuyển board
- Tap 2 lần để fullscreen
- Pinch to zoom

## 🔄 Backup tự động

### Cấu hình Cron Job (Khuyến nghị)

```bash
# Vào cPanel → Cron Jobs
# Thêm dòng sau (backup mỗi ngày 2h sáng):

0 2 * * * /usr/bin/php /home/username/public_html/wcb/cron_backup.php >> /home/username/public_html/wcb/logs/backup.log 2>&1
```

### Backup thủ công

```bash
php backup.php
```

Hoặc truy cập: `https://aurorahotelplaza.com/wcb/backup.php`

## 🆘 Troubleshooting

### Lỗi: "Kết nối database thất bại"
```bash
# Kiểm tra config.php
nano config.php

# Test kết nối
php -r "require 'config.php'; getDBConnection(); echo 'OK';"
```

### Lỗi: "Không thể upload file"
```bash
# Kiểm tra quyền
ls -la uploads/
chmod 755 uploads/

# Kiểm tra PHP limits
php -i | grep upload_max_filesize
```

### Lỗi: "Màn hình không cập nhật"
- Nhấn F5 hoặc R để refresh
- Kiểm tra kết nối internet
- Xóa cache trình duyệt (Ctrl+Shift+Delete)

## 📞 Cần giúp đỡ?

1. Xem log: `tail -f logs/backup.log`
2. Health check: `https://aurorahotelplaza.com/wcb/health_check.php`
3. Test system: `https://aurorahotelplaza.com/wcb/test.php`
4. Đọc docs: `README.md` và `DEPLOY.md`

## 🎯 Checklist hoàn thành

- [ ] Upload file lên server
- [ ] Chạy installer hoặc import database
- [ ] Test hệ thống (test.php)
- [ ] Upload thử 1 board
- [ ] Kích hoạt và xem trên display.php
- [ ] Cấu hình cron backup
- [ ] Xóa file install.php (bảo mật)
- [ ] Đọc README.md để biết thêm tính năng

## 🚀 Bước tiếp theo

- Đọc `README.md` để hiểu đầy đủ tính năng
- Đọc `DEPLOY.md` để triển khai production
- Xem `CHANGELOG.md` để biết các cập nhật
- Cấu hình backup tự động
- Thiết lập monitoring

---

**Chúc mừng! Bạn đã sẵn sàng sử dụng WCB System! 🎉**
