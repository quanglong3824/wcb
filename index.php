<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Board Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Hệ thống Welcome Board</h1>
            <nav>
                <a href="#business" class="nav-btn">Phòng Kinh Doanh</a>
                <a href="admin.php" class="nav-btn">Admin</a>
                <a href="display.php" target="_blank" class="nav-btn display-btn">Chiếu màn hình</a>
                <a href="test_auto_update.php" target="_blank" class="nav-btn" style="background: #9b59b6;">Test Auto Update</a>
            </nav>
        </header>

        <!-- Phần Upload cho Phòng Kinh Doanh -->
        <section id="business" class="panel">
            <h2>Phòng Kinh Doanh - Upload Welcome Board</h2>
            
            <?php if (isset($_GET['upload_success'])): ?>
                <div class="alert success">Upload thành công! Welcome Board đã sẵn sàng.</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['upload_error'])): ?>
                <div class="alert error"><?php echo htmlspecialchars($_GET['upload_error']); ?></div>
            <?php endif; ?>

            <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label for="event_date">Ngày hội thảo</label>
                    <input type="date" id="event_date" name="event_date" required>
                </div>
                
                <div class="form-group">
                    <label for="event_title">Tiêu đề sự kiện</label>
                    <input type="text" id="event_title" name="event_title" placeholder="Nhập tên sự kiện..." required>
                </div>
                
                <div class="form-group">
                    <label for="welcome_image">Hình ảnh Welcome Board</label>
                    <input type="file" id="welcome_image" name="welcome_image" accept="image/*" required>
                    <small class="note">Yêu cầu: Ảnh nằm ngang, tối đa 2K (1920x1080 khuyến nghị)</small>
                </div>
                
                <button type="submit" class="btn-primary">Upload Welcome Board</button>
            </form>
        </section>

        <!-- Phần Admin -->
        <section id="admin" class="panel">
            <h2>Admin - Quản lý Welcome Board</h2>
            
            <?php if (isset($_GET['admin_success'])): ?>
                <div class="alert success">Đã cập nhật trạng thái hiển thị!</div>
            <?php endif; ?>
            
            <div class="admin-controls">
                <h3>Danh sách Welcome Board</h3>
                <?php include 'admin_list.php'; ?>
            </div>
            
            <div class="current-status">
                <h3>Trạng thái hiện tại</h3>
                <?php include 'current_status.php'; ?>
            </div>
        </section>
    </div>

    <script src="script.js"></script>
</body>
</html>