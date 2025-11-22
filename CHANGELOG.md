# Changelog

Tất cả các thay đổi quan trọng của dự án sẽ được ghi lại ở đây.

## [2.0.0] - 2025-11-21

### 🎉 Tính năng mới
- **MySQL Database**: Chuyển từ JSON sang MySQL để tăng hiệu suất và độ tin cậy
- **Chọn nhiều board**: Hỗ trợ kích hoạt tối đa 3 Welcome Board cùng lúc
- **Tự động chuyển board**: Màn hình display tự động chuyển đổi giữa các board (10s)
- **Điều khiển từ xa**: Kích hoạt board từ điện thoại, màn hình TV tự động cập nhật
- **API Endpoints**: Thêm API để lấy dữ liệu và thống kê
- **Backup/Restore**: Hệ thống backup tự động và khôi phục dữ liệu
- **Health Check**: Endpoint kiểm tra trạng thái hệ thống
- **Cron Jobs**: Tự động backup hàng ngày
- **Activity Logs**: Tracking hoạt động người dùng (tùy chọn)

### 🔧 Cải tiến
- Tối ưu hiệu suất query database với indexes
- Prepared statements để chống SQL injection
- Bảo mật file config với .htaccess
- Responsive design tốt hơn cho mobile
- Animation mượt mà khi chuyển board
- Swipe support cho mobile/tablet
- Auto-refresh thông minh (60s)

### 📦 File mới
- `config.php` - Cấu hình database
- `install.php` - Wizard cài đặt
- `api.php` - API endpoints
- `backup.php` - Backup database
- `restore.php` - Khôi phục dữ liệu
- `health_check.php` - Kiểm tra hệ thống
- `cron_backup.php` - Cron job backup
- `database.sql` - Schema database
- `.htaccess` - Bảo mật Apache
- `.gitignore` - Git ignore rules
- `DEPLOY.md` - Hướng dẫn triển khai
- `CHANGELOG.md` - Lịch sử thay đổi

### 🐛 Sửa lỗi
- Fix lỗi upload file lớn
- Fix lỗi hiển thị ảnh trên Safari
- Fix lỗi fullscreen trên iOS
- Fix lỗi timezone

### 🔒 Bảo mật
- Bảo vệ file config.php
- Bảo vệ file data.json
- SQL injection prevention
- XSS protection
- File upload validation

### 📚 Documentation
- README.md cập nhật đầy đủ
- DEPLOY.md hướng dẫn triển khai chi tiết
- Inline comments trong code
- API documentation

---

## [1.0.0] - 2025-11-20

### 🎉 Phiên bản đầu tiên
- Upload Welcome Board
- Kích hoạt/tắt hiển thị
- Màn hình chiếu fullscreen
- Quản lý admin cơ bản
- Lưu trữ dữ liệu JSON
- Responsive design
- Phím tắt điều khiển

---

## Kế hoạch tương lai

### [2.1.0] - Dự kiến
- [ ] Multi-language support (EN/VI)
- [ ] QR code để truy cập nhanh
- [ ] Preview board trước khi kích hoạt
- [ ] Lịch sử hoạt động chi tiết
- [ ] Export báo cáo PDF
- [ ] Email notification khi upload
- [ ] Dark mode
- [ ] PWA support

### [3.0.0] - Dự kiến
- [ ] User authentication & roles
- [ ] Multi-tenant support
- [ ] Cloud storage integration (S3)
- [ ] Real-time sync với WebSocket
- [ ] Mobile app (React Native)
- [ ] Analytics dashboard
- [ ] AI auto-crop ảnh
- [ ] Video support

---

**Ghi chú:**
- [Major.Minor.Patch] theo Semantic Versioning
- Major: Breaking changes
- Minor: New features (backward compatible)
- Patch: Bug fixes
