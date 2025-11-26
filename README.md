# Welcome Board System - Quang Long Hotel

Hệ thống quản lý bảng chào mừng thông minh cho khách sạn Quang Long.

## 📋 Cấu trúc dự án

### Trang chính (Root Level)
- **index.php** - Dashboard chính
- **view.php** - Giám sát TV (hiển thị các màn hình TV đang chiếu)
- **tv.php** - Quản lý TV (CRUD TV)
- **manage-wcb.php** - Quản lý WCB (Welcome Board Content)
- **uploads.php** - Upload file (hình ảnh, video)
- **schedule.php** - Quản lý lịch chiếu
- **settings.php** - Cài đặt hệ thống

### CSS Files (assets/css/)
- **admin.css** - Styles chung cho admin (header, sidebar, footer, dashboard)
- **view.css** - Styles cho trang giám sát TV
- **tv.css** - Styles cho trang quản lý TV
- **manage-wcb.css** - Styles cho trang quản lý WCB
- **uploads.css** - Styles cho trang upload
- **schedule.css** - Styles cho trang lịch chiếu
- **settings.css** - Styles cho trang cài đặt

### JavaScript Files (assets/js/)
- **admin.js** - JS chung (sidebar toggle, tooltips, alerts)
- **view.js** - JS cho giám sát TV (auto-refresh, filters)
- **tv.js** - JS cho quản lý TV (CRUD operations)
- **manage-wcb.js** - JS cho quản lý WCB
- **uploads.js** - JS cho upload (drag & drop, progress)
- **schedule.js** - JS cho lịch chiếu
- **settings.js** - JS cho cài đặt

### API Backend (api/)
- **upload.php** - Xử lý upload file
- **get-tv-status.php** - Lấy trạng thái TV
- **get-tvs.php** - Lấy danh sách TV
- **get-wcb.php** - Lấy danh sách WCB
- **get-files.php** - Lấy danh sách files
- **get-schedules.php** - Lấy danh sách lịch chiếu

### Includes
- **header.php** - Header với top bar
- **sidebar.php** - Sidebar navigation menu
- **footer.php** - Footer

### Config
- **config/php/config.php** - Cấu hình chung, helper functions
- **config/php/database.php** - Cấu hình database

## 🎨 Tính năng

### 1. Dashboard
- Thống kê tổng quan
- Quick actions
- Hiển thị số liệu TV, WCB, lịch chiếu

### 2. Giám sát TV (view.php)
- Hiển thị grid các màn hình TV
- Trạng thái online/offline
- Nội dung đang chiếu
- Filter theo vị trí và trạng thái
- Auto-refresh mỗi 30 giây

### 3. Quản lý TV (tv.php)
- Thêm/sửa/xóa TV
- Hiển thị dạng grid hoặc table
- Thông tin: tên, vị trí, IP, trạng thái
- Điều khiển TV

### 4. Quản lý WCB (manage-wcb.php)
- Quản lý nội dung Welcome Board
- Preview hình ảnh/video
- Gán nội dung cho TV
- Search và filter

### 5. Upload (uploads.php)
- Drag & drop upload
- Upload nhiều file cùng lúc
- Progress bar
- File gallery với preview
- Filter theo loại file

### 6. Lịch chiếu (schedule.php)
- Lên lịch hiển thị nội dung
- Chọn TV, nội dung, thời gian
- Lặp lại (daily, weekly, monthly)
- Quản lý trạng thái lịch

### 7. Cài đặt (settings.php)
- Cài đặt chung (tên khách sạn, múi giờ)
- Cài đặt hiển thị (auto-refresh, transition)
- Cài đặt thông báo (email)
- Quản lý người dùng

## 🎯 Sidebar Menu

1. **Dashboard** - Trang chủ
2. **Giám sát TV** - Theo dõi TV
3. **Quản lý TV** - CRUD TV
4. **Quản lý WCB** - CRUD nội dung
5. **Upload** - Tải file lên
6. **Lịch chiếu** - Quản lý schedule
7. **Cài đặt** - Cấu hình hệ thống

## 🛠️ Công nghệ

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL (cấu hình trong database.php)
- **Icons**: Font Awesome 6.4.0
- **Architecture**: Clean Code, tách biệt HTML/CSS/JS/PHP

## 📦 Cài đặt

1. Copy toàn bộ project vào thư mục web server (htdocs/www)
2. Import database (nếu có)
3. Cấu hình database trong `config/php/database.php`
4. Truy cập: `http://localhost/quanglong3824/wcb/`

## 🔐 Bảo mật

- Session-based authentication
- XSS protection với htmlspecialchars()
- File upload validation
- SQL injection prevention (prepared statements)

## 📱 Responsive

- Mobile-friendly design
- Adaptive layouts
- Touch-friendly controls

## 🎨 Design Pattern

- **MVC-like structure**: Tách biệt logic, view, data
- **Component-based**: Reusable includes (header, sidebar, footer)
- **API-driven**: AJAX calls cho dynamic content
- **Clean Code**: Readable, maintainable, documented

## 📝 Notes

- Tất cả API hiện đang trả về dữ liệu mẫu
- Cần implement database operations
- Cần thêm authentication system
- Cần thêm error handling
