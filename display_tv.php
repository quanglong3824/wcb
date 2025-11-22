<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?php echo TV_NAME; ?> - Welcome Board Display</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #000;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .display-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .welcome-board {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: none;
        }
        
        .welcome-board.active {
            display: block;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .no-board {
            color: #ffffff;
            text-align: center;
            font-size: 1.5rem;
            padding: 40px;
        }
        
        .tv-info {
            position: fixed;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 100;
        }
        
        body:hover .tv-info {
            opacity: 1;
        }
        
        .board-indicator {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        body:hover .board-indicator {
            opacity: 1;
        }
        
        .indicator-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .indicator-dot.active {
            background: #fff;
            width: 30px;
            border-radius: 6px;
        }
        
        body.hide-cursor {
            cursor: none;
        }
        
        body.hide-cursor .tv-info,
        body.hide-cursor .board-indicator {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="tv-info">
        <?php echo TV_NAME; ?> (<?php echo TV_CODE; ?>)
    </div>
    
    <div class="display-container" id="displayContainer">
        <?php
        require_once __DIR__ . '/config.php';
        
        // Lấy TV ID từ code
        $conn = getDBConnection();
        $tv_code = TV_CODE;
        $tv_result = $conn->query("SELECT id FROM tv_screens WHERE code = '$tv_code' AND status = 'active'");
        
        if ($tv_result && $tv_row = $tv_result->fetch_assoc()) {
            $tv_id = $tv_row['id'];
            
            // Lấy boards được assign cho TV này
            $query = "SELECT wb.* 
                     FROM welcome_boards wb
                     JOIN board_assignments ba ON wb.id = ba.board_id
                     WHERE ba.tv_id = $tv_id AND ba.status = 'active'
                     ORDER BY wb.event_date DESC";
            
            $result = $conn->query($query);
            $active_boards = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $active_boards[] = $row;
                }
            }
        } else {
            $active_boards = [];
        }
        
        if (!empty($active_boards)): ?>
            <?php foreach ($active_boards as $index => $board): ?>
                <img src="<?php echo '/' . $board['filepath']; ?>" 
                     alt="Welcome Board" 
                     class="welcome-board <?php echo $index === 0 ? 'active' : ''; ?>"
                     data-board-id="<?php echo $board['id']; ?>"
                     data-index="<?php echo $index; ?>">
            <?php endforeach; ?>
            
            <?php if (count($active_boards) > 1): ?>
                <div class="board-indicator" id="boardIndicator">
                    <?php foreach ($active_boards as $index => $board): ?>
                        <div class="indicator-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                             onclick="showBoard(<?php echo $index; ?>)"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-board">
                <p>Không có Welcome Board nào cho <?php echo TV_NAME; ?></p>
                <p style="font-size: 1rem; margin-top: 20px; opacity: 0.7;">
                    Vui lòng liên hệ Admin để kích hoạt
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const TV_CODE = '<?php echo TV_CODE; ?>';
        let currentBoardIndex = 0;
        const boards = document.querySelectorAll('.welcome-board');
        const indicators = document.querySelectorAll('.indicator-dot');
        let autoRotateInterval;
        let initialBoardIds = Array.from(boards).map(b => b.dataset.boardId).sort().join(',');
        
        function showBoard(index) {
            if (boards.length === 0) return;
            boards.forEach(board => board.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));
            boards[index].classList.add('active');
            if (indicators[index]) indicators[index].classList.add('active');
            currentBoardIndex = index;
            resetAutoRotate();
        }
        
        function nextBoard() {
            if (boards.length <= 1) return;
            currentBoardIndex = (currentBoardIndex + 1) % boards.length;
            showBoard(currentBoardIndex);
        }
        
        function startAutoRotate() {
            if (boards.length > 1) {
                autoRotateInterval = setInterval(nextBoard, 10000);
            }
        }
        
        function resetAutoRotate() {
            clearInterval(autoRotateInterval);
            startAutoRotate();
        }
        
        startAutoRotate();
        
        // Auto update detection
        function checkForUpdates() {
            const timestamp = Date.now();
            const randomParam = Math.random().toString(36).substring(7);
            
            fetch('/api.php?action=get_tv_boards&tv_code=' + TV_CODE + '&t=' + timestamp + '&r=' + randomParam, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.boards) {
                        const newBoardIds = data.boards.map(b => b.id).sort().join(',');
                        if (initialBoardIds !== newBoardIds) {
                            console.log('🔄 Phát hiện thay đổi! Đang cập nhật...');
                            location.reload();
                        }
                    }
                })
                .catch(err => console.log('Check update error:', err));
        }
        
        // Check mỗi 2 giây
        setInterval(checkForUpdates, 2000);
        
        // Auto refresh mỗi 60 giây (backup)
        setInterval(() => location.reload(), 60000);
        
        // Ẩn cursor sau 3 giây
        let mouseTimer;
        document.addEventListener('mousemove', function() {
            document.body.classList.remove('hide-cursor');
            clearTimeout(mouseTimer);
            mouseTimer = setTimeout(() => {
                document.body.classList.add('hide-cursor');
            }, 3000);
        });
        
        // Tự động fullscreen sau 1 giây
        window.addEventListener('load', function() {
            setTimeout(() => {
                if (!document.fullscreenElement && boards.length > 0) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log('Không thể tự động fullscreen:', err);
                    });
                }
            }, 1000);
        });
    </script>
</body>
</html>
