# Tính năng Slideshow - Aurora Hotel Plaza WCB System

## Tổng quan

Tính năng slideshow cho phép nhân viên tạo và quản lý slideshow với nhạc nền để trình chiếu trên các màn hình TV trong khách sạn.

## Tính năng chính

### 1. Tạo slideshow

- ✅ Chọn nhiều ảnh từ thư viện media
- ✅ Sắp xếp lại thứ tự ảnh bằng kéo thả (drag & drop)
- ✅ Upload và ghép nhạc nền (MP3, WAV, OGG, M4A)
- ✅ Tự động set thời gian chuyển ảnh (có thể tùy chỉnh)
- ✅ Tính tổng thời gian slideshow
- ✅ Fade out nhạc tự động khi kết thúc slideshow

### 2. Quản lý slideshow

- Xem danh sách tất cả slideshow
- Chỉnh sửa slideshow đã tạo
- Xóa slideshow
- Gán slideshow cho TV cụ thể

### 3. Player slideshow

- Trình chiếu toàn màn hình trên TV
- Hiệu ứng Ken Burns cho ảnh
- Chuyển ảnh mượt mà (fade transition)
- Phát nhạc nền tự động
- Fade out nhạc theo thời gian slideshow

## Hướng dẫn sử dụng

### Bước 1: Chạy migration

Trước tiên, cần chạy migration để tạo các bảng cần thiết:

```bash
# Đăng nhập MySQL
mysql -u your_username -p your_database

# Chạy migration
source /path/to/wcb/migrations/add_slideshow_support.sql
```

Hoặc import trực tiếp qua phpMyAdmin.

### Bước 2: Truy cập trang quản lý

1. Đăng nhập vào hệ thống WCB
2. Truy cập: `http://your-domain/slideshow.php`
3. Click "Tạo Slideshow Mới"

### Bước 3: Tạo slideshow

1. **Nhập thông tin cơ bản:**
   - Tên slideshow \*
   - Mô tả (tùy chọn)
   - Thời gian hiển thị mỗi ảnh (giây) \*
   - Thời gian fade out nhạc (giây)

2. **Upload nhạc nền:**
   - Kéo thả file nhạc vào vùng upload
   - Hoặc click để chọn file
   - Hỗ trợ: MP3, WAV, OGG, M4A (tối đa 50MB)
   - File nhạc sẽ được lưu vào `uploads/` với prefix `audio_`

3. **Chọn ảnh:**
   - Click "Chọn từ thư viện"
   - Click vào các ảnh muốn thêm
   - Click "Xác nhận" khi hoàn tất
   - Kéo thả để sắp xếp lại thứ tự ảnh

4. **Kiểm tra thông tin:**
   - Số lượng ảnh
   - Tổng thời gian slideshow (tự động tính)
   - Thời lượng nhạc

5. **Lưu slideshow**

### Bước 4: Gán slideshow cho TV

1. Trong danh sách slideshow, click icon TV
2. Chọn TV muốn gán
3. Slideshow sẽ tự động phát trên TV đã chọn

### Bước 5: Xem slideshow trên TV

URL trực tiếp: `http://your-domain/slideshow-player.php?id=SLIDESHOW_ID`

## Cấu trúc Database

### Bảng `slideshows`

```sql
- id: ID slideshow
- name: Tên slideshow
- description: Mô tả
- transition_duration: Thời gian hiển thị mỗi ảnh (giây)
- total_duration: Tổng thời gian (tự động tính)
- audio_id: ID file nhạc (foreign key -> media)
- fade_out_duration: Thời gian fade out (giây)
- status: Trạng thái (active/inactive)
- created_by: Người tạo
- created_at: Ngày tạo
- updated_at: Ngày cập nhật
```

### Bảng `slideshow_images`

```sql
- id: ID
- slideshow_id: ID slideshow
- media_id: ID ảnh (foreign key -> media)
- display_order: Thứ tự hiển thị
- custom_duration: Thời gian hiển thị riêng (tùy chọn)
- created_at: Ngày tạo
```

### Cập nhật bảng `media`

```sql
- type: Thêm giá trị 'audio' và 'slideshow'
```

## API Endpoints

### Slideshow API (`api/slideshows.php`)

#### 1. Tạo slideshow

```
POST /api/slideshows.php?action=create
Body:
- name: Tên slideshow
- description: Mô tả
- transition_duration: Thời gian chuyển ảnh
- audio_id: ID nhạc nền
- fade_out_duration: Thời gian fade out
- image_ids: JSON array các ID ảnh
```

#### 2. Cập nhật slideshow

```
POST /api/slideshows.php?action=update
Body: Tương tự create + slideshow_id
```

#### 3. Xóa slideshow

```
POST /api/slideshows.php?action=delete
Body:
- slideshow_id: ID slideshow
```

#### 4. Lấy thông tin slideshow

```
GET /api/slideshows.php?action=get&id=SLIDESHOW_ID
```

#### 5. Danh sách slideshow

```
GET /api/slideshows.php?action=list&status=all|active|inactive
```

### Upload Audio API (`api/upload-audio.php`)

```
POST /api/upload-audio.php
Body (multipart/form-data):
- audio: File nhạc
- name: Tên file (tùy chọn)
- description: Mô tả (tùy chọn)
```

## Quy ước đặt tên file

### File nhạc

```
uploads/audio_[timestamp]_[unique_id].[extension]
Ví dụ: uploads/audio_1706443200_abc123.mp3
```

### File ảnh

```
uploads/[unique_id]_[timestamp].[extension]
(Sử dụng hệ thống upload hiện tại)
```

## Lưu ý kỹ thuật

### 1. Tính toán thời gian

- **Tổng thời gian = Số ảnh × Thời gian mỗi ảnh**
- **Fade out bắt đầu = Tổng thời gian - Thời gian fade out**
- Nếu nhạc ngắn hơn slideshow: nhạc kết thúc trước
- Nếu nhạc dài hơn slideshow: tự động fade out

### 2. Hiệu ứng

- **Fade transition**: 1 giây
- **Ken Burns effect**: 15 giây (scale 1.0 -> 1.1)
- **Fade out**: Giảm dần volume trong N giây

### 3. Browser support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Fullscreen API
- HTML5 Audio API
- Drag & Drop API

### 4. Performance

- Preload ảnh để tránh giật lag
- Sử dụng CSS transitions thay vì JavaScript animation
- Audio fade out mượt mà với interval timing

## Troubleshooting

### Nhạc không phát

- Kiểm tra format audio được hỗ trợ
- Kiểm tra quyền truy cập file
- Thử click vào màn hình để trigger autoplay

### Ảnh không hiển thị

- Kiểm tra path ảnh trong database
- Kiểm tra quyền đọc file trong `/uploads`

### Slideshow bị giật

- Tối ưu kích thước ảnh (khuyến nghị: 1920x1080)
- Kiểm tra kết nối mạng
- Giảm số ảnh nếu cần

## Changelog

### Version 1.0.0 (2026-01-28)

- ✅ Tạo, sửa, xóa slideshow
- ✅ Upload và quản lý nhạc nền
- ✅ Chọn nhiều ảnh và sắp xếp
- ✅ Tự động tính thời gian
- ✅ Fade out nhạc
- ✅ Player với Ken Burns effect
- ✅ Responsive design

## TODO (Tính năng tương lai)

- [ ] Thêm nhiều hiệu ứng chuyển ảnh (slide, zoom, flip, etc.)
- [ ] Cho phép set thời gian riêng cho từng ảnh
- [ ] Hỗ trợ subtitle/text overlay
- [ ] Schedule slideshow theo thời gian
- [ ] Preview slideshow trước khi lưu
- [ ] Export slideshow ra video
