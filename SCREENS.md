# Danh sách 7 màn hình TV

## Cấu trúc màn hình

### 1. TV Basement
- **ID**: 1
- **Tên**: TV Basement
- **Vị trí**: Tầng hầm
- **Folder**: `basement/`
- **Display URL**: `basement/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Welcome Banner 01

### 2. TV Chrysan
- **ID**: 2
- **Tên**: TV Chrysan
- **Vị trí**: Phòng Chrysan
- **Folder**: `chrysan/`
- **Display URL**: `chrysan/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Hotel Promo Video

### 3. TV Jasmine
- **ID**: 3
- **Tên**: TV Jasmine
- **Vị trí**: Phòng Jasmine
- **Folder**: `jasmine/`
- **Display URL**: `jasmine/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Welcome Banner 02

### 4. TV Lotus
- **ID**: 4
- **Tên**: TV Lotus
- **Vị trí**: Phòng Lotus
- **Folder**: `lotus/`
- **Display URL**: `lotus/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Lotus Display

### 5. TV Restaurant
- **ID**: 5
- **Tên**: TV Restaurant
- **Vị trí**: Nhà hàng
- **Folder**: `restaurant/`
- **Display URL**: `restaurant/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Menu Display

### 6. TV FO 1
- **ID**: 6
- **Tên**: TV FO 1 (Front Office 1)
- **Vị trí**: Lễ tân 1
- **Folder**: `fo/tv1/`
- **Display URL**: `fo/tv1/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Welcome Video

### 7. TV FO 2
- **ID**: 7
- **Tên**: TV FO 2 (Front Office 2)
- **Vị trí**: Lễ tân 2
- **Folder**: `fo/tv2/`
- **Display URL**: `fo/tv2/index.php`
- **Trạng thái**: Online
- **Nội dung mặc định**: Welcome Banner

## Cách sử dụng

### Truy cập màn hình hiển thị
Mỗi màn hình có URL riêng để hiển thị nội dung:
- Basement: `http://localhost/quanglong3824/wcb/basement/`
- Chrysan: `http://localhost/quanglong3824/wcb/chrysan/`
- Jasmine: `http://localhost/quanglong3824/wcb/jasmine/`
- Lotus: `http://localhost/quanglong3824/wcb/lotus/`
- Restaurant: `http://localhost/quanglong3824/wcb/restaurant/`
- FO 1: `http://localhost/quanglong3824/wcb/fo/tv1/`
- FO 2: `http://localhost/quanglong3824/wcb/fo/tv2/`

### Quản lý từ Admin
- **Giám sát**: `view.php` - Xem tất cả 7 màn hình cùng lúc
- **Quản lý TV**: `tv.php` - CRUD các TV
- **Gán nội dung**: `manage-wcb.php` - Gán WCB cho từng TV
- **Lên lịch**: `schedule.php` - Lên lịch chiếu cho từng TV

## Notes
- Mỗi folder TV có file `index.php` riêng để hiển thị nội dung
- Các TV có thể được điều khiển độc lập
- Hỗ trợ hiển thị cả hình ảnh và video
- Auto-refresh để cập nhật nội dung theo lịch
