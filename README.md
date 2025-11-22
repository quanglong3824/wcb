# Welcome Board Management System

Hệ thống quản lý Welcome Board cho Aurora Hotel Plaza - Hỗ trợ điều khiển từ xa và hiển thị đa màn hình.

## 🚀 Tính năng

### 📋 Phòng Kinh Doanh
- Upload hình ảnh Welcome Board
- Chọn ngày hội thảo
- Nhập tiêu đề sự kiện
- Kiểm tra tự động kích thước ảnh (max 2K, nằm ngang)

### ⚙️ Admin
- **Chọn tối đa 3 Welcome Board cùng lúc** (phục vụ 3 hội nghị trong 1 ngày)
- Kích hoạt/tắt hiển thị từng Welcome Board riêng lẻ
- Xóa Welcome Board không cần thiết
- Xem trạng thái tất cả board đang active
- **Điều khiển từ xa**: Kích hoạt board từ điện thoại/máy tính, màn hình TV tự động cập nhật

### 🖥️ Hiển thị
- Màn hình chiếu full screen
- **Tự động chuyển đổi** giữa các board đang active (mỗi 10 giây)
- Tự động làm mới mỗi 60 giây
- Phím tắt điều khiển (←/→ để chuyển board)
- Hỗ trợ swipe trên mobile/tablet
- Responsive design

## 📁 Cấu trúc file

```
├── index.php              # Trang chính
├── install.php            # Cài đặt database
├── config.php             # Cấu hình database (tự động tạo)
├── upload.php             # Xử lý upload
├── admin_actions.php      # Xử lý hành động admin
├── admin_list.php         # Danh sách board cho admin
├── current_status.php     # Trạng thái hiện tại
├── display.php            # Màn hình chiếu
├── api.php                # API endpoints
├── backup.php             # Backup database
├── restore.php            # Khôi phục dữ liệu
├── style.css              # CSS styling
├── script.js              # JavaScript
├── .htaccess              # Bảo mật Apache
├── data.json              # Backup JSON
├── uploads/               # Thư mục chứa ảnh
├── backups/               # Thư mục backup (tự động tạo)
└── README.md              # Hướng dẫn
```

## 🛠️ Cài đặt

### 🚀 Quick Start (5 phút)
Xem hướng dẫn nhanh: **[QUICKSTART.md](QUICKSTART.md)**

### 📖 Hướng dẫn chi tiết

#### Bước 1: Upload file
1. Upload tất cả file lên server `aurorahotelplaza.com/wcb/`
2. Đảm bảo thư mục `uploads/` có quyền ghi (chmod 755)

#### Bước 2: Kiểm tra hệ thống
1. Truy cập: `aurorahotelplaza.com/wcb/test.php`
2. Kiểm tra tất cả requirements

#### Bước 3: Cài đặt database
1. Truy cập: `aurorahotelplaza.com/wcb/install.php`
2. Nhập thông tin database MySQL
3. Click "Cài đặt ngay"
4. Hệ thống sẽ tự động:
   - Tạo database và bảng
   - Migrate dữ liệu từ JSON (nếu có)
   - Tạo file config.php

#### Bước 4: Sử dụng
- Truy cập `index.php` để bắt đầu

### 📚 Tài liệu
- **[QUICKSTART.md](QUICKSTART.md)** - Hướng dẫn nhanh 5 phút
- **[DEPLOY.md](DEPLOY.md)** - Hướng dẫn triển khai chi tiết
- **[CHANGELOG.md](CHANGELOG.md)** - Lịch sử thay đổi

## 📋 Yêu cầu hệ thống

- PHP 7.0+ với mysqli extension
- MySQL 5.7+ hoặc MariaDB 10.2+
- Web server (Apache/Nginx)
- Hỗ trợ upload file

## 🎯 Flow sử dụng

### Cách 1: Sử dụng trên cùng 1 máy
1. **Sale/Phòng Kinh Doanh**: Upload ảnh Welcome Board với ngày và tiêu đề
2. **Admin**: Chọn board để kích hoạt hiển thị (tối đa 3 board)
3. **Chiếu**: Mở `display.php` full screen để chiếu

### Cách 2: Điều khiển từ xa (Khuyến nghị cho aurorahotelplaza.com/wcb)
1. **Trên TV/Màn hình chiếu:**
   - Mở trình duyệt: `aurorahotelplaza.com/wcb/display.php`
   - Nhấn F11 để fullscreen
   - Màn hình sẽ hiển thị màu đen (chờ kích hoạt)

2. **Trên điện thoại/máy tính:**
   - Mở: `aurorahotelplaza.com/wcb`
   - Vào phần Admin
   - Chọn Welcome Board và click "Kích hoạt hiển thị"
   - Màn hình TV sẽ tự động cập nhật

3. **Quản lý nhiều hội nghị:**
   - Kích hoạt tối đa 3 board cùng lúc
   - Màn hình tự động chuyển đổi giữa các board
   - Tắt/bật từng board riêng lẻ khi cần

## ⚠️ Lưu ý

- Ảnh phải nằm ngang (chiều rộng > chiều cao)
- Kích thước tối đa 2K (2048px chiều rộng)
- File tối đa 10MB
- Chỉ chấp nhận JPG, PNG

## 🔧 Phím tắt (Màn hình chiếu)

- `F11`: Toggle fullscreen
- `←` `→`: Chuyển board trước/sau (khi có nhiều board)
- `I`: Hiện/ẩn thông tin
- `R`: Làm mới
- `Esc`: Thoát fullscreen
- **Swipe**: Vuốt trái/phải trên mobile để chuyển board

## 🔧 Quản lý & Bảo trì

### Backup dữ liệu
```bash
php backup.php
```
Hoặc truy cập trực tiếp: `aurorahotelplaza.com/wcb/backup.php`

### Khôi phục dữ liệu
Truy cập: `aurorahotelplaza.com/wcb/restore.php`

### API Endpoints
- `api.php?action=get_active_boards` - Lấy danh sách board đang active
- `api.php?action=get_stats` - Thống kê hệ thống

### Cấu trúc Database

**Bảng: welcome_boards**
```sql
- id (VARCHAR 50) - Primary Key
- event_date (DATE) - Ngày sự kiện
- event_title (VARCHAR 255) - Tiêu đề
- filename (VARCHAR 255) - Tên file
- filepath (VARCHAR 255) - Đường dẫn file
- upload_time (DATETIME) - Thời gian upload
- status (ENUM) - active/inactive
- width (INT) - Chiều rộng ảnh
- height (INT) - Chiều cao ảnh
- created_at (TIMESTAMP) - Thời gian tạo
- updated_at (TIMESTAMP) - Thời gian cập nhật
```

## 🔒 Bảo mật

- File `config.php` được bảo vệ bởi `.htaccess`
- File `data.json` không thể truy cập trực tiếp
- SQL injection được ngăn chặn bằng prepared statements
- Upload file được validate kỹ lưỡng

## 📞 Hỗ trợ

Liên hệ admin nếu có vấn đề kỹ thuật.