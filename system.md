# Mô tả Hệ thống Bảng chào mừng (Welcome Board - WCB)

## 1. Tổng quan

Hệ thống WCB được xây dựng để tự động hóa việc quản lý và trình chiếu nội dung (bảng chào mừng, thông báo, quảng cáo) trên các màn hình TV thông minh trong khách sạn.

Mục tiêu là thay thế quy trình thủ công (sử dụng USB) bằng một giao diện quản trị tập trung trên nền tảng web, cho phép quản lý từ xa, lên lịch trình và giám sát toàn bộ hệ thống một cách hiệu quả.

---

## 2. Chức năng chi tiết

### 2.1. Quản lý Nội dung (Content Management)
- **Thư viện Đa phương tiện:**
    - Một giao diện duy nhất để tải lên, lưu trữ và quản lý tất cả các tệp nội dung.
    - Hỗ trợ các định dạng phổ biến như hình ảnh (`JPG`, `PNG`) và video (`MP4`).
- **Tổ chức:** Các tệp được lưu trong một thư mục `uploads/` chung và được quản lý thông qua cơ sở dữ liệu.

### 2.2. Quản lý Lịch trình (Scheduling)
- **Lên lịch theo thời gian thực:** Quản trị viên có thể lên lịch trình chiếu một nội dung cụ thể trên một hoặc nhiều TV theo ngày, giờ, và phút bắt đầu/kết thúc.
- **Nội dung mặc định:** Mỗi TV có thể được gán một nội dung (hình ảnh/video) mặc định. Nội dung này sẽ được hiển thị khi không có lịch trình nào khác đang hoạt động.
- **Giao diện lịch trình:** Giao diện trực quan để xem và quản lý các lịch trình đã tạo.

### 2.3. Quản lý và Giám sát Màn hình (TV Management)
- **Giao diện quản lý TV:** Một bảng điều khiển (dashboard) hiển thị danh sách tất cả các TV trong hệ thống.
- **Giám sát trạng thái:**
    - Hiển thị trạng thái **Online** hoặc **Offline** của từng TV theo thời gian thực.
    - TV sẽ tự động gửi tín hiệu "heartbeat" về máy chủ mỗi vài phút để cập nhật trạng thái.
- **Xem trước nội dung:** Cho phép quản trị viên xem trước nội dung đang (hoặc sẽ) được chiếu trên một TV cụ thể.
- **Điều khiển từ xa:** Cung cấp chức năng gửi lệnh "Khởi động lại" (reload) tới một TV cụ thể để làm mới trình duyệt hoặc xử lý sự cố treo màn hình.

### 2.4. Quản lý Người dùng và Phân quyền (User Roles)
- **Super Admin:**
    - Có toàn quyền quản trị hệ thống.
    - Quản lý tài khoản người dùng, cấu hình TV, và tất cả các chức năng khác.
- **Content Manager (Quản lý nội dung):**
    - Được cấp quyền truy cập vào các chức năng liên quan đến nội dung.
    - Có thể tải lên media, tạo và chỉnh sửa lịch trình chiếu.
    - Không có quyền truy cập vào các cài đặt hệ thống hoặc quản lý người dùng.

---

## 3. Kiến trúc hệ thống (Dự kiến)

- **Backend:** PHP thuần, cung cấp các API endpoints (JSON) để frontend và các TV player tương tác.
- **Frontend (Admin Panel):** Giao diện web sử dụng HTML, CSS, và JavaScript (jQuery hoặc tương tự) để cung cấp các công cụ quản trị.
- **Cơ sở dữ liệu:** MySQL / MariaDB để lưu trữ thông tin về:
    - `users`: tài khoản và vai trò người dùng.
    - `media`: thông tin về các tệp trong thư viện.
    - `tvs`: danh sách, trạng thái, và nội dung mặc định của các TV.
    - `schedules`: lịch trình chiếu nội dung trên các TV.
- **Player (trên TV):** Một trang web đơn giản (ví dụ: `player.php`) chạy trên trình duyệt của Smart TV. Trang này sẽ:
    - Lấy ID định danh của TV.
    - Gọi API của backend định kỳ để kiểm tra lịch trình hiện tại.
    - Hiển thị nội dung được chỉ định hoặc nội dung mặc định.
    - Gửi tín hiệu "heartbeat" về máy chủ.

---

## 4. Cấu trúc Thư mục (Dự kiến)

Dựa trên yêu cầu, nội dung sẽ được điều hướng tới các TV đặt tại các vị trí cụ thể. Cấu trúc thư mục player sẽ phản ánh điều này.

```
/
├── admin.php           # Giao diện quản trị chính
├── api.php             # Endpoints cho player và admin panel
├── config.php          # Cấu hình kết nối database và các tham số hệ thống
├── login.php           # Trang đăng nhập
├── player.php          # Giao diện phát nội dung cho TV
├── database.sql        # Cấu trúc cơ sở dữ liệu
├── style.css           # CSS cho trang admin
├── uploads/            # Thư mục chứa tất cả file media (thư viện)
│   ├── image1.jpg
│   └── video1.mp4
└── assets/             # Chứa CSS, JS cho các trang
``` 
- Admin có toàn quyền CRUD 