# 🚀 Hướng dẫn triển khai lên aurorahotelplaza.com/wcb

## Bước 1: Chuẩn bị

### 1.1. Tạo database MySQL
Đăng nhập vào cPanel hoặc phpMyAdmin và tạo database mới:

```sql
CREATE DATABASE wcb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Tạo user và cấp quyền:
```sql
CREATE USER 'wcb_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON wcb.* TO 'wcb_user'@'localhost';
FLUSH PRIVILEGES;
```

### 1.2. Upload file
Upload toàn bộ file lên server vào thư mục `/public_html/wcb/`:

```
/public_html/wcb/
├── index.php
├── install.php
├── upload.php
├── admin_actions.php
├── admin_list.php
├── current_status.php
├── display.php
├── api.php
├── backup.php
├── restore.php
├── style.css
├── script.js
├── .htaccess
└── README.md
```

### 1.3. Tạo thư mục và phân quyền
```bash
mkdir uploads
chmod 755 uploads

mkdir backups
chmod 755 backups
```

## Bước 2: Cài đặt

### 2.1. Chạy installer
Truy cập: `https://aurorahotelplaza.com/wcb/install.php`

Nhập thông tin:
- **Database Host**: `localhost`
- **Database Username**: `wcb_user`
- **Database Password**: `your_strong_password`
- **Database Name**: `wcb`
/** The name of the database for WordPress */
define( 'DB_NAME', 'auroraho_web_2025' ); //auroraho_web_2025
define( 'DB_USER', 'auroraho_longdev' ); //auroraho_longdev
define( 'DB_PASSWORD', '@longdev3824' ); //@longdev3824
define( 'DB_HOST', 'localhost:3306' ); //localhost:3306
Click **"Cài đặt ngay"**

### 2.2. Kiểm tra
Hệ thống sẽ tự động:
- ✅ Tạo bảng `welcome_boards`
- ✅ Tạo file `config.php`
- ✅ Migrate dữ liệu từ `data.json` (nếu có)

## Bước 3: Sử dụng

### 3.1. Truy cập hệ thống
- **Trang chính**: `https://aurorahotelplaza.com/wcb/`
- **Màn hình chiếu**: `https://aurorahotelplaza.com/wcb/display.php`

### 3.2. Workflow điều khiển từ xa

**Trên TV/Màn hình chiếu:**
1. Mở Chrome/Firefox
2. Truy cập: `https://aurorahotelplaza.com/wcb/display.php`
3. Nhấn F11 để fullscreen
4. Màn hình sẽ hiển thị màu đen (chờ kích hoạt)

**Trên điện thoại/máy tính:**
1. Mở trình duyệt
2. Truy cập: `https://aurorahotelplaza.com/wcb/`
3. Upload Welcome Board (phần Phòng Kinh Doanh)
4. Vào phần Admin
5. Chọn board và click "Kích hoạt hiển thị"
6. Màn hình TV sẽ tự động cập nhật sau 60 giây (hoặc nhấn R để refresh ngay)

## Bước 4: Backup tự động (Khuyến nghị)

### 4.1. Tạo Cron Job
Vào cPanel → Cron Jobs, thêm:

```bash
# Backup mỗi ngày lúc 2h sáng
0 2 * * * /usr/bin/php /home/username/public_html/wcb/backup.php
```

### 4.2. Backup thủ công
```bash
php backup.php
```

Hoặc truy cập: `https://aurorahotelplaza.com/wcb/backup.php`

## Bước 5: Bảo mật

### 5.1. Xóa file install.php sau khi cài đặt
```bash
rm install.php
```

### 5.2. Bảo vệ thư mục admin (tùy chọn)
Tạo file `.htpasswd` để bảo vệ trang admin:

```bash
htpasswd -c .htpasswd admin
```

Thêm vào `.htaccess`:
```apache
<Location "/wcb/">
    AuthType Basic
    AuthName "WCB Admin Area"
    AuthUserFile /path/to/.htpasswd
    Require valid-user
</Location>
```

### 5.3. SSL/HTTPS
Đảm bảo website đã cài SSL certificate (Let's Encrypt miễn phí)

## Bước 6: Kiểm tra

### 6.1. Checklist
- [ ] Upload được ảnh
- [ ] Kích hoạt/tắt board hoạt động
- [ ] Màn hình display hiển thị đúng
- [ ] Tự động chuyển board (nếu có nhiều board)
- [ ] Backup hoạt động
- [ ] API endpoints hoạt động

### 6.2. Test API
```bash
curl https://aurorahotelplaza.com/wcb/api.php?action=get_stats
curl https://aurorahotelplaza.com/wcb/api.php?action=get_active_boards
```

## Troubleshooting

### Lỗi: "Kết nối database thất bại"
- Kiểm tra thông tin trong `config.php`
- Đảm bảo MySQL service đang chạy
- Kiểm tra user có quyền truy cập database

### Lỗi: "Không thể upload file"
- Kiểm tra quyền thư mục `uploads/` (chmod 755)
- Kiểm tra `upload_max_filesize` trong php.ini
- Kiểm tra dung lượng disk còn trống

### Lỗi: "Màn hình không cập nhật"
- Nhấn F5 hoặc R để refresh
- Kiểm tra kết nối internet
- Xóa cache trình duyệt

### Lỗi: "File config.php không tồn tại"
- Chạy lại `install.php`
- Kiểm tra quyền ghi file trong thư mục

## Nâng cấp

### Từ JSON sang MySQL
Nếu đang dùng phiên bản JSON cũ:

1. Backup file `data.json`
2. Upload file mới
3. Chạy `install.php`
4. Hệ thống tự động migrate dữ liệu

### Update code
```bash
# Backup trước khi update
php backup.php

# Upload file mới (giữ nguyên config.php và uploads/)
# Không cần chạy lại install.php
```

## Liên hệ hỗ trợ

Nếu gặp vấn đề, liên hệ:
- Email: support@aurorahotelplaza.com
- Hotline: 1900 xxxx

---

**Lưu ý quan trọng:**
- Luôn backup trước khi thay đổi
- Không chia sẻ thông tin database
- Thường xuyên cập nhật mật khẩu
- Kiểm tra log định kỳ
