# 📺 Hướng dẫn tương thích TV cũ (Samsung, Sony Smart TV)

## Vấn đề

Các TV Samsung, Sony đời cũ sử dụng trình duyệt web cũ (thường là WebKit cũ hoặc Opera) không hỗ trợ đầy đủ JavaScript hiện đại như:
- `fetch()` API
- Arrow functions
- Template literals
- ES6+ syntax

## Giải pháp đã triển khai

### 1. TV Player tương thích (tv-player.js)

File `assets/js/tv-player.js` đã được viết lại với:
- **XMLHttpRequest** thay vì `fetch()`
- **ES5 syntax** (function thay vì arrow functions)
- **String concatenation** thay vì template literals
- **Vendor prefixes** cho CSS transitions/transforms

### 2. Meta Refresh Fallback

Mỗi trang TV có thẻ meta refresh tự động reload sau 10 phút:
```html
<meta http-equiv="refresh" content="600">
```

Đây là cơ chế backup nếu JavaScript không hoạt động.

### 3. CSS tương thích

Template TV sử dụng CSS với vendor prefixes:
```css
-webkit-transition: opacity 0.8s ease;
-moz-transition: opacity 0.8s ease;
-o-transition: opacity 0.8s ease;
transition: opacity 0.8s ease;
```

### 4. Heartbeat System

TV gửi heartbeat mỗi 30 giây để:
- Báo trạng thái online
- Nhận tín hiệu reload từ server

## Cấu hình TV

### Danh sách TV và Folder

| TV ID | Folder | Tên | Vị trí |
|-------|--------|-----|--------|
| 1 | basement | TV Basement | Tầng hầm |
| 2 | chrysan | TV Chrysan | Phòng Chrysan |
| 3 | jasmine | TV Jasmine | Phòng Jasmine |
| 4 | lotus | TV Lotus | Phòng Lotus |
| 5 | restaurant | TV Restaurant | Nhà hàng |
| 6 | fo/tv1 | TV FO 1 | Lễ tân 1 |
| 7 | fo/tv2 | TV FO 2 | Lễ tân 2 |

### URL truy cập

```
http://[server]/wcb/basement/
http://[server]/wcb/chrysan/
http://[server]/wcb/jasmine/
http://[server]/wcb/lotus/
http://[server]/wcb/restaurant/
http://[server]/wcb/fo/tv1/
http://[server]/wcb/fo/tv2/
```

## Cơ chế cập nhật nội dung

### Tự động (Ưu tiên)

1. **Content Refresh**: Mỗi 60 giây, TV kiểm tra nội dung mới từ server
2. **Heartbeat Check**: Mỗi 30 giây, TV gửi heartbeat và nhận tín hiệu reload
3. **Reload Signal Check**: Mỗi 10 giây, TV kiểm tra tín hiệu reload

### Thủ công

1. **Từ Admin Panel**: Nhấn nút "Reload" trên trang Quản lý TV
2. **Meta Refresh**: Trang tự reload sau 10 phút

## Xử lý sự cố

### TV không cập nhật nội dung

1. **Kiểm tra kết nối mạng** của TV
2. **Kiểm tra heartbeat** trong Admin Panel (TV có online không?)
3. **Gửi tín hiệu reload** từ Admin Panel
4. **Refresh thủ công** trên TV (nếu có remote)

### TV hiển thị lỗi

1. **Kiểm tra Console** (nếu TV hỗ trợ)
2. **Kiểm tra API** bằng cách truy cập trực tiếp:
   ```
   http://[server]/wcb/api/get-tv-content.php?tv_id=1&get_all=1
   ```
3. **Kiểm tra file media** có tồn tại không

### TV không gửi heartbeat

1. **Kiểm tra JavaScript** có chạy không
2. **Kiểm tra CORS** - API đã cho phép cross-origin
3. **Kiểm tra firewall** - port 80/443 có mở không

## Tối ưu cho TV cũ

### Khuyến nghị

1. **Sử dụng hình ảnh JPEG** thay vì PNG (nhẹ hơn)
2. **Tối ưu kích thước file** < 2MB mỗi hình
3. **Tránh video nặng** - sử dụng MP4 H.264
4. **Giảm số lượng slide** - tối đa 3 slides

### Cấu hình TV

1. **Tắt chế độ tiết kiệm năng lượng** (tránh TV sleep)
2. **Bật chế độ kiosk** nếu có
3. **Disable screensaver**
4. **Set homepage** về URL của TV

## Giám sát

### Dashboard

Admin Panel hiển thị:
- Trạng thái online/offline của từng TV
- Thời gian heartbeat cuối cùng
- Nội dung đang hiển thị

### Notifications

Hệ thống tự động gửi thông báo khi:
- TV offline > 5 phút
- Lỗi kết nối database
- Lỗi load nội dung

## API Reference

### Get TV Content
```
GET /api/get-tv-content.php?tv_id={id}&get_all=1
```

### Heartbeat
```
GET /api/heartbeat.php?tv_id={id}&folder={folder}
```

### Reload Signal
```
POST /api/reload-tv.php
Body: { "tv_id": 1 }
```

---

**Cập nhật**: 2024-12-01
