# 🚀 Welcome Board System - Development Roadmap

## 📊 Tổng quan dự án

**Hệ thống Welcome Board (WCB)** là một ứng dụng web quản lý nội dung hiển thị trên 7 màn hình TV tại các vị trí khác nhau trong khách sạn Aurora Hotel.

### Công nghệ sử dụng:
- **Backend**: PHP 7.4+ (Pure PHP, không framework)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS)
- **Database**: MySQL 8.0 / MariaDB 10.5+
- **Server**: Apache/Nginx + PHP-FPM
- **Libraries**: Font Awesome 6.4, jQuery (optional)

---

## 📋 Phân loại tính năng theo cấp độ

### 🟢 LEVEL 1: FRESHER/INTERN (0-6 tháng kinh nghiệm)

#### Mục tiêu: Làm quen với codebase, HTML/CSS, PHP cơ bản

#### Tính năng đã hoàn thành:
- ✅ Cấu trúc thư mục dự án
- ✅ Database schema (8 bảng chính)
- ✅ File cấu hình (config.php, database.php)
- ✅ Layout cơ bản (header, footer, sidebar)
- ✅ Trang dashboard với thống kê tĩnh
- ✅ Giao diện các trang chính (HTML/CSS)

#### Tính năng cần phát triển:

**1.1. Hoàn thiện trang Login** ⭐ Priority: HIGH
```
File: auth/login.php
Tasks:
- [ ] Tạo form đăng nhập với HTML/CSS
- [ ] Validate input (username, password không để trống)
- [ ] Xử lý submit form
- [ ] Hiển thị thông báo lỗi
- [ ] Redirect sau khi đăng nhập thành công
Thời gian ước tính: 2-3 ngày
```

**1.2. Trang Logout** ⭐ Priority: HIGH
```
File: auth/logout.php
Tasks:
- [ ] Xóa session
- [ ] Redirect về trang login
- [ ] Hiển thị thông báo đăng xuất thành công
Thời gian ước tính: 1 ngày
```

**1.3. Trang Profile cá nhân** ⭐ Priority: MEDIUM
```
File: profile.php
Tasks:
- [ ] Hiển thị thông tin user đang đăng nhập
- [ ] Form đổi mật khẩu
- [ ] Form cập nhật thông tin (email, họ tên)
- [ ] Upload avatar
Thời gian ước tính: 3-4 ngày
```

**1.4. Trang 404 Not Found** ⭐ Priority: LOW
```
File: 404.php
Tasks:
- [ ] Thiết kế trang 404 đẹp mắt
- [ ] Link quay về trang chủ
- [ ] Hiển thị menu điều hướng
Thời gian ước tính: 1 ngày
```

**1.5. Footer với thông tin bản quyền** ⭐ Priority: LOW
```
File: includes/footer.php
Tasks:
- [ ] Thêm thông tin bản quyền
- [ ] Link đến trang hỗ trợ
- [ ] Hiển thị phiên bản hệ thống
Thời gian ước tính: 0.5 ngày
```

**Kỹ năng học được:**
- HTML form handling
- PHP session management
- Basic validation
- CSS styling
- File structure organization

---

### 🟡 LEVEL 2: JUNIOR (6-18 tháng kinh nghiệm)

#### Mục tiêu: Làm việc với database, CRUD operations, API endpoints

#### Tính năng cần phát triển:

**2.1. API Authentication** ⭐ Priority: HIGH
```
File: api/auth.php
Tasks:
- [ ] POST /api/auth/login - Xác thực đăng nhập
- [ ] POST /api/auth/logout - Đăng xuất
- [ ] GET /api/auth/check - Kiểm tra session
- [ ] Validate credentials với database
- [ ] Hash password với password_hash()
- [ ] Return JSON response
Thời gian ước tính: 3-4 ngày
```

**2.2. CRUD Users** ⭐ Priority: HIGH
```
Files: api/users.php, admin/pages/user-management.php
Tasks:
- [ ] GET /api/users - Lấy danh sách users
- [ ] GET /api/users/{id} - Lấy thông tin 1 user
- [ ] POST /api/users - Tạo user mới
- [ ] PUT /api/users/{id} - Cập nhật user
- [ ] DELETE /api/users/{id} - Xóa user
- [ ] Phân quyền (chỉ admin mới được CRUD)
- [ ] Giao diện quản lý users
Thời gian ước tính: 5-7 ngày
```

**2.3. CRUD TVs** ⭐ Priority: HIGH
```
Files: api/tvs.php (đã có get-tvs.php)
Tasks:
- [ ] Chuyển data từ hardcode sang database
- [ ] POST /api/tvs - Thêm TV mới
- [ ] PUT /api/tvs/{id} - Cập nhật TV
- [ ] DELETE /api/tvs/{id} - Xóa TV
- [ ] Validate IP address format
- [ ] Kiểm tra folder tồn tại
- [ ] Tích hợp với giao diện tv.php
Thời gian ước tính: 4-5 ngày
```

**2.4. CRUD Media/WCB** ⭐ Priority: HIGH
```
Files: api/media.php, api/upload.php (đã có)
Tasks:
- [ ] GET /api/media - Lấy danh sách media
- [ ] GET /api/media/{id} - Chi tiết media
- [ ] POST /api/media/upload - Upload file
- [ ] PUT /api/media/{id} - Cập nhật thông tin
- [ ] DELETE /api/media/{id} - Xóa media
- [ ] Validate file type, size
- [ ] Generate thumbnail cho video
- [ ] Tích hợp với manage-wcb.php
Thời gian ước tính: 6-8 ngày
```

**2.5. CRUD Schedules** ⭐ Priority: HIGH
```
Files: api/schedules.php (đã có get-schedules.php)
Tasks:
- [ ] Chuyển data từ hardcode sang database
- [ ] POST /api/schedules - Tạo lịch chiếu
- [ ] PUT /api/schedules/{id} - Cập nhật lịch
- [ ] DELETE /api/schedules/{id} - Xóa lịch
- [ ] Validate thời gian (start < end)
- [ ] Kiểm tra conflict lịch chiếu
- [ ] Tích hợp với schedule.php
Thời gian ước tính: 5-7 ngày
```

**2.6. Dashboard với dữ liệu thực** ⭐ Priority: MEDIUM
```
File: index.php, api/dashboard.php
Tasks:
- [ ] Lấy thống kê từ database
- [ ] Tổng số TV, TV online/offline
- [ ] Tổng số media
- [ ] Lịch chiếu hôm nay
- [ ] Biểu đồ hoạt động (optional)
- [ ] Recent activities
Thời gian ước tính: 3-4 ngày
```

**2.7. Search & Filter** ⭐ Priority: MEDIUM
```
Tasks:
- [ ] Search media by name
- [ ] Filter media by type (image/video)
- [ ] Filter schedules by TV, date, status
- [ ] Filter TVs by location, status
- [ ] Pagination cho danh sách
Thời gian ước tính: 4-5 ngày
```

**Kỹ năng học được:**
- MySQL queries (SELECT, INSERT, UPDATE, DELETE)
- Prepared statements (SQL injection prevention)
- RESTful API design
- JSON handling
- File upload handling
- Input validation & sanitization
- Error handling

---

### 🟠 LEVEL 3: MIDDLE (1.5-3 năm kinh nghiệm)

#### Mục tiêu: Logic phức tạp, real-time features, optimization

#### Tính năng cần phát triển:

**3.1. TV Player System** ⭐ Priority: HIGH
```
Files: */index.php (basement, chrysan, jasmine, etc.)
Tasks:
- [ ] Tự động phát hiện TV ID từ folder
- [ ] Gọi API lấy nội dung cần hiển thị
- [ ] Sử dụng stored procedure sp_get_tv_content()
- [ ] Hiển thị media (image/video) fullscreen
- [ ] Auto-refresh theo schedule
- [ ] Fallback về default content
- [ ] Xử lý lỗi khi mất kết nối
- [ ] Transition effects giữa các nội dung
Thời gian ước tính: 7-10 ngày
```

**3.2. TV Heartbeat System** ⭐ Priority: HIGH
```
Files: api/heartbeat.php
Tasks:
- [ ] POST /api/heartbeat - TV gửi tín hiệu sống
- [ ] Lưu vào bảng tv_heartbeats
- [ ] Cập nhật last_heartbeat trong bảng tvs
- [ ] Tự động đánh dấu offline nếu quá threshold
- [ ] Sử dụng stored procedure sp_update_tv_status()
- [ ] Tích hợp vào TV player (gửi mỗi 60s)
Thời gian ước tính: 4-5 ngày
```

**3.3. Schedule Engine** ⭐ Priority: HIGH
```
Files: api/schedule-engine.php, cron/update-schedules.php
Tasks:
- [ ] Logic kiểm tra lịch chiếu hiện tại
- [ ] Xử lý repeat (daily, weekly, monthly)
- [ ] Priority handling (lịch có priority cao hơn)
- [ ] Auto update status (pending → active → completed)
- [ ] Conflict detection & resolution
- [ ] Sử dụng stored procedure sp_update_schedule_status()
- [ ] Cron job chạy mỗi phút
Thời gian ước tính: 8-10 ngày
```

**3.4. Real-time Monitoring** ⭐ Priority: MEDIUM
```
Files: view.php, api/monitoring.php
Tasks:
- [ ] WebSocket hoặc Long Polling
- [ ] Hiển thị trạng thái TV real-time
- [ ] Preview nội dung đang chiếu
- [ ] Thông báo khi TV offline
- [ ] Remote control (reload, change content)
- [ ] Activity timeline
Thời gian ước tính: 10-12 ngày
```

**3.5. Activity Logging System** ⭐ Priority: MEDIUM
```
Files: includes/logger.php, api/logs.php
Tasks:
- [ ] Log tất cả actions (login, upload, CRUD)
- [ ] Tự động log qua triggers
- [ ] Giao diện xem logs
- [ ] Filter logs by user, action, date
- [ ] Export logs to CSV
- [ ] Auto cleanup old logs (90 days)
Thời gian ước tính: 5-6 ngày
```

**3.6. Media Library Management** ⭐ Priority: MEDIUM
```
Files: uploads.php, api/media-library.php
Tasks:
- [ ] Drag & drop multiple files
- [ ] Upload progress bar
- [ ] Thumbnail generation
- [ ] Video duration detection
- [ ] File size optimization
- [ ] Bulk operations (delete, assign)
- [ ] Storage quota management
Thời gian ước tính: 8-10 ngày
```

**3.7. Settings Management** ⭐ Priority: MEDIUM
```
Files: settings.php, api/settings.php
Tasks:
- [ ] Lưu/lấy settings từ bảng system_settings
- [ ] Validate settings values
- [ ] Cache settings (để tránh query nhiều)
- [ ] Apply settings real-time
- [ ] Backup/restore settings
- [ ] Import/export configuration
Thời gian ước tính: 5-6 ngày
```

**3.8. Notification System** ⭐ Priority: LOW
```
Files: includes/notification.php, api/notifications.php
Tasks:
- [ ] Email notifications (TV offline, schedule failed)
- [ ] In-app notifications
- [ ] Notification preferences
- [ ] Email templates
- [ ] SMTP configuration
- [ ] Notification history
Thời gian ước tính: 6-8 ngày
```

**Kỹ năng học được:**
- Complex business logic
- Real-time communication
- Cron jobs & scheduled tasks
- Performance optimization
- Caching strategies
- Error handling & logging
- File processing & optimization

---

### 🔴 LEVEL 4: SENIOR (3+ năm kinh nghiệm)

#### Mục tiêu: Architecture, security, scalability, advanced features

#### Tính năng cần phát triển:

**4.1. Security Hardening** ⭐ Priority: CRITICAL
```
Tasks:
- [ ] Implement CSRF protection
- [ ] XSS prevention (output escaping)
- [ ] SQL injection prevention (prepared statements)
- [ ] Rate limiting cho API
- [ ] Session hijacking prevention
- [ ] Secure file upload (validate MIME type)
- [ ] Input sanitization layer
- [ ] Security headers (CSP, X-Frame-Options)
- [ ] Password policy enforcement
- [ ] Two-factor authentication (2FA)
- [ ] API authentication (JWT tokens)
- [ ] Audit trail cho sensitive operations
Thời gian ước tính: 15-20 ngày
```

**4.2. Performance Optimization** ⭐ Priority: HIGH
```
Tasks:
- [ ] Database query optimization
- [ ] Add proper indexes
- [ ] Implement caching (Redis/Memcached)
- [ ] Lazy loading cho images
- [ ] CDN integration
- [ ] Minify CSS/JS
- [ ] Image optimization pipeline
- [ ] Database connection pooling
- [ ] Query result caching
- [ ] Implement pagination efficiently
- [ ] Load testing & benchmarking
Thời gian ước tính: 12-15 ngày
```

**4.3. Advanced Scheduling** ⭐ Priority: HIGH
```
Tasks:
- [ ] Playlist support (multiple media in sequence)
- [ ] Conditional scheduling (weather-based, event-based)
- [ ] Template schedules (copy to multiple TVs)
- [ ] Schedule preview/simulation
- [ ] Conflict resolution strategies
- [ ] Emergency override (urgent announcements)
- [ ] Schedule versioning & rollback
- [ ] Bulk schedule operations
Thời gian ước tính: 10-12 ngày
```

**4.4. Multi-tenant Support** ⭐ Priority: MEDIUM
```
Tasks:
- [ ] Organization/tenant management
- [ ] Isolated data per tenant
- [ ] Tenant-specific branding
- [ ] Resource quotas per tenant
- [ ] Billing & subscription management
- [ ] Tenant admin dashboard
- [ ] Cross-tenant reporting (super admin)
Thời gian ước tính: 15-20 ngày
```

**4.5. Advanced Analytics** ⭐ Priority: MEDIUM
```
Files: analytics.php, api/analytics.php
Tasks:
- [ ] Content performance tracking
- [ ] TV uptime statistics
- [ ] Schedule compliance reports
- [ ] User activity analytics
- [ ] Custom reports builder
- [ ] Data visualization (charts, graphs)
- [ ] Export reports (PDF, Excel)
- [ ] Scheduled report delivery
Thời gian ước tính: 12-15 ngày
```

**4.6. API Documentation & SDK** ⭐ Priority: MEDIUM
```
Tasks:
- [ ] OpenAPI/Swagger documentation
- [ ] API versioning (v1, v2)
- [ ] Rate limiting & throttling
- [ ] API key management
- [ ] Webhook support
- [ ] JavaScript SDK
- [ ] PHP SDK
- [ ] API usage analytics
Thời gian ước tính: 10-12 ngày
```

**4.7. Backup & Disaster Recovery** ⭐ Priority: HIGH
```
Tasks:
- [ ] Automated database backups
- [ ] File backup (uploads folder)
- [ ] Backup scheduling
- [ ] Restore functionality
- [ ] Backup verification
- [ ] Off-site backup storage
- [ ] Point-in-time recovery
- [ ] Disaster recovery plan
Thời gian ước tính: 8-10 ngày
```

**4.8. Content Management System** ⭐ Priority: MEDIUM
```
Tasks:
- [ ] Template system cho content
- [ ] Dynamic content (weather, news, RSS)
- [ ] Content approval workflow
- [ ] Version control cho content
- [ ] Content expiration
- [ ] A/B testing cho content
- [ ] Content recommendation engine
Thời gian ước tính: 15-18 ngày
```

**4.9. Mobile App Integration** ⭐ Priority: LOW
```
Tasks:
- [ ] RESTful API cho mobile
- [ ] Push notifications
- [ ] Mobile-optimized dashboard
- [ ] QR code cho TV control
- [ ] Mobile upload support
- [ ] Offline mode
Thời gian ước tính: 20-25 ngày
```

**4.10. DevOps & CI/CD** ⭐ Priority: MEDIUM
```
Tasks:
- [ ] Docker containerization
- [ ] Docker Compose setup
- [ ] CI/CD pipeline (GitHub Actions)
- [ ] Automated testing (PHPUnit)
- [ ] Code quality tools (PHPStan, PHPCS)
- [ ] Deployment automation
- [ ] Environment management (dev, staging, prod)
- [ ] Monitoring & alerting (Prometheus, Grafana)
Thời gian ước tính: 12-15 ngày
```

**Kỹ năng học được:**
- System architecture design
- Security best practices
- Performance tuning
- Scalability patterns
- DevOps practices
- Advanced database optimization
- API design & documentation
- Testing strategies

---

## 🎯 Ưu tiên phát triển (Recommended Order)

### Phase 1: Core Functionality (2-3 tháng)
1. ✅ Database setup
2. 🔄 Authentication system (Level 1 & 2)
3. 🔄 CRUD operations (Level 2)
4. 🔄 TV Player system (Level 3)
5. 🔄 Schedule engine (Level 3)

### Phase 2: Essential Features (2-3 tháng)
6. 🔄 Heartbeat system (Level 3)
7. 🔄 Media library (Level 3)
8. 🔄 Real-time monitoring (Level 3)
9. 🔄 Activity logging (Level 3)
10. 🔄 Settings management (Level 3)

### Phase 3: Enhancement (1-2 tháng)
11. 🔄 Security hardening (Level 4)
12. 🔄 Performance optimization (Level 4)
13. 🔄 Advanced scheduling (Level 4)
14. 🔄 Analytics (Level 4)

### Phase 4: Advanced Features (2-3 tháng)
15. 🔄 Notification system (Level 3)
16. 🔄 Backup & recovery (Level 4)
17. 🔄 API documentation (Level 4)
18. 🔄 Multi-tenant (Level 4) - Optional

---

## 📚 Tài liệu tham khảo

### Cho Fresher/Intern:
- PHP Basics: https://www.php.net/manual/en/tutorial.php
- HTML/CSS: https://www.w3schools.com/
- MySQL: https://dev.mysql.com/doc/

### Cho Junior:
- PHP PDO: https://www.php.net/manual/en/book.pdo.php
- RESTful API: https://restfulapi.net/
- SQL Optimization: https://use-the-index-luke.com/

### Cho Middle:
- PHP Best Practices: https://phptherightway.com/
- Design Patterns: https://refactoring.guru/design-patterns/php
- Performance: https://www.php.net/manual/en/features.performance.php

### Cho Senior:
- Security: https://owasp.org/www-project-top-ten/
- Architecture: https://martinfowler.com/architecture/
- Scalability: https://github.com/binhnguyennus/awesome-scalability

---

## 🔧 Setup môi trường phát triển

### Requirements:
- PHP 7.4+
- MySQL 8.0+ / MariaDB 10.5+
- Apache/Nginx
- Composer (optional)
- Git

### Installation:
```bash
# Clone repository
git clone <repository-url>

# Import database
mysql -u root -p < database.sql

# Configure database
cp config/database-config.example.php config/php/database.php
# Edit config/php/database.php với thông tin của bạn

# Check connection
http://localhost/your-project/check-database.php

# Start development
http://localhost/your-project/
```

---

## 📞 Support & Contact

- **Project Lead**: [Your Name]
- **Email**: [your-email]
- **Documentation**: README-DATABASE.md, SCREENS.md, system.md

---

## 📝 Notes

- Tất cả thời gian ước tính là cho 1 developer
- Có thể điều chỉnh priority dựa trên nhu cầu thực tế
- Nên làm theo thứ tự từ Level 1 → Level 4
- Code review là bắt buộc trước khi merge
- Viết unit test cho các chức năng quan trọng
- Document code và API endpoints

**Last Updated**: 2024-01-20
**Version**: 1.0.0
