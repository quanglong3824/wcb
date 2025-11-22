# 🎯 Hệ thống quản lý Welcome Board - 7 TV

Hệ thống quản lý và hiển thị Welcome Board (WCB) cho 7 màn hình TV với giao diện phẳng hiện đại.

## 📋 Tính năng chính

### 1. Upload Welcome Board
- Upload hình ảnh WCB với thông tin sự kiện
- Tự động lưu vào hệ thống
- Hỗ trợ nhiều định dạng ảnh

### 2. Quản lý 7 TV
- **TV Tầng hầm**: Phát tối đa 3 WCB cùng lúc
- **6 TV còn lại**: Mỗi TV phát tối đa 1 WCB
- Hiển thị preview WCB đang phát
- Trạng thái real-time

### 3. Phân bổ WCB
- Chọn WCB cần phát
- Chọn TV muốn phát
- Hệ thống tự động kiểm tra giới hạn
- Không cho phép vượt quá số lượng WCB tối đa

### 4. Điều khiển
- **Mở TV**: Mở trang hiển thị TV trong tab mới
- **Đóng WCB**: Tắt WCB cụ thể trên TV
- **Tắt toàn bộ WCB**: Tắt tất cả WCB trên tất cả TV

## 🏗️ Cấu trúc dự án

```
├── admin.php           # Trang quản lý chính
├── api.php            # API endpoints
├── config.php         # Cấu hình database
├── display_tv.php     # Template hiển thị TV
├── upload.php         # Xử lý upload
├── database_v2.sql    # Database schema
├── basement/tv1/      # TV Tầng hầm
├── fo/tv1/, fo/tv2/   # TV Front Office
├── restaurant/tv1/    # TV Nhà hàng
├── chrysan/tv1/       # TV Chrysan
├── lotus/tv1/         # TV Lotus
└── jasmin/tv1/        # TV Jasmin
```

## 🎨 Giao diện

### Thiết kế phẳng (Flat Design)
- Không sử dụng icon phức tạp
- Màu sắc đơn giản, rõ ràng
- Hiệu ứng chuyển động mượt mà
- Responsive trên mọi thiết bị

### Màu sắc chủ đạo
- **Header**: #2c3e50 (xanh đậm)
- **Primary**: #3498db (xanh dương)
- **Success**: #27ae60 (xanh lá)
- **Danger**: #e74c3c (đỏ)
- **Background**: #f0f0f0 (xám nhạt)

## 🔄 Luồng hoạt động

1. **Upload WCB**
   - Admin upload hình ảnh WCB
   - Nhập thông tin sự kiện (ngày, tiêu đề)
   - Hệ thống lưu vào database

2. **Chọn TV và WCB**
   - Chọn WCB từ danh sách
   - Tick chọn TV muốn phát
   - Hệ thống kiểm tra giới hạn
   - Assign WCB cho TV

3. **Hiển thị trên TV**
   - TV tự động load WCB được assign
   - Nếu có nhiều WCB, tự động xoay vòng 10s/lần
   - Auto refresh mỗi 3 giây để cập nhật

4. **Tắt WCB**
   - Đóng WCB cụ thể: Tắt 1 WCB trên 1 TV
   - Đóng toàn bộ WCB trên TV: Tắt tất cả WCB trên 1 TV
   - Tắt toàn bộ WCB: Tắt tất cả WCB trên tất cả TV

## 📊 Database

### Bảng chính
- `departments`: Các bộ phận (Basement, FO, Restaurant, ...)
- `tv_screens`: 7 TV screens
- `welcome_boards`: Danh sách WCB
- `board_assignments`: Phân bổ WCB cho TV

### Quy tắc
- TV Tầng hầm (BASEMENT_TV1): max 3 WCB
- Các TV khác: max 1 WCB
- Mỗi assignment có status: active/inactive

## 🚀 Cài đặt

1. Import database:
```sql
mysql -u username -p database_name < database_v2.sql
```

2. Cấu hình database trong `config.php`:
```php
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

3. Tạo thư mục uploads:
```bash
mkdir -p uploads backups logs
chmod 755 uploads backups logs
```

4. Truy cập:
- Admin: `http://your-domain/wcb/admin.php`
- TV: `http://your-domain/wcb/basement/tv1/`

## 🔧 API Endpoints

- `GET /api.php?action=get_tvs` - Lấy danh sách TV
- `GET /api.php?action=get_all_boards` - Lấy danh sách WCB
- `GET /api.php?action=get_tv_boards&tv_code=XXX` - Lấy WCB của TV
- `POST /api.php?action=assign_to_tv` - Assign WCB cho TV
- `POST /api.php?action=unassign_from_tv` - Unassign WCB từ TV

## 📱 Responsive

- Desktop: Grid 3-4 cột
- Tablet: Grid 2 cột
- Mobile: Grid 1 cột

## ⚡ Performance

- Auto refresh: 3 giây
- Backup refresh: 60 giây
- Image lazy loading
- Cache busting với timestamp

## 🎯 Tối ưu

- Giao diện phẳng, không icon
- Màu sắc đơn giản
- Animation mượt mà
- Real-time update
- Mobile-friendly

## 📝 Ghi chú

- TV Tầng hầm đặc biệt: có thể phát 3 WCB cùng lúc
- Các TV khác: chỉ phát 1 WCB
- WCB tự động xoay vòng nếu có nhiều hơn 1
- Hệ thống tự động kiểm tra giới hạn khi assign

## 🔐 Bảo mật

- Validate input khi upload
- Kiểm tra file type
- Sanitize database queries
- CORS headers cho API

---

**Version**: 2.0  
**Last Update**: 2024  
**Developer**: LongDev
