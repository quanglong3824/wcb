<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Chrysan - Welcome Board</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #000;
            color: white;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
        }
        
        #tv-display {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        #content-display img,
        #content-display video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .fullscreen-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(212, 175, 55, 0.9);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.2em;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .fullscreen-btn:hover {
            background: rgba(212, 175, 55, 1);
            transform: scale(1.1);
        }
        
        .tv-info {
            position: fixed;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.6);
            padding: 8px 15px;
            border-radius: 8px;
            z-index: 999;
        }
        
        .tv-info h2 {
            margin: 0 0 3px 0;
            font-size: 0.9em;
            color: #d4af37;
            font-weight: 600;
        }
        
        .tv-info p {
            margin: 0;
            opacity: 0.7;
            font-size: 0.75em;
        }
        
        .no-content {
            text-align: center;
            padding: 40px;
        }
        
        .no-content i {
            font-size: 5em;
            color: #666;
            margin-bottom: 20px;
        }
        
        .no-content p {
            font-size: 1.5em;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="tv-info">
        <h2><i class="fas fa-tv"></i> TV Chrysan</h2>
        <p>Phòng Chrysan - Aurora Hotel Plaza</p>
    </div>
    
    <div id="tv-display">
        <div id="content-display">
            <div class="no-content">
                <i class="fas fa-tv"></i>
                <p>Chưa có nội dung hiển thị</p>
            </div>
        </div>
    </div>
    
    <button class="fullscreen-btn" onclick="toggleFullscreen()" title="Toàn màn hình">
        <i class="fas fa-expand"></i>
    </button>
    
    <script>
        const TV_ID = 2; // TV Chrysan
        
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        function loadContent() {
            console.log('Loading content for TV ID:', TV_ID);
            
            fetch('../api/get-tv-content.php?tv_id=' + TV_ID)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    
                    if (data.success && data.content) {
                        console.log('Displaying content:', data.content);
                        displayContent(data.content);
                    } else {
                        console.log('No content or error:', data.message);
                        showNoContent(data.message || 'Chưa có nội dung hiển thị');
                    }
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    showNoContent('Lỗi kết nối API');
                });
        }
        
        function displayContent(content) {
            const display = document.getElementById('content-display');
            
            console.log('Content type:', content.type);
            console.log('Content path:', content.file_path);
            
            if (content.type === 'image') {
                display.innerHTML = `<img src="../${content.file_path}" alt="${content.name}" onerror="console.error('Image load error'); this.src='../assets/img/no-image.png'">`;
            } else if (content.type === 'video') {
                display.innerHTML = `<video src="../${content.file_path}" autoplay loop muted onerror="console.error('Video load error')"></video>`;
            }
        }
        
        function showNoContent(message) {
            const display = document.getElementById('content-display');
            display.innerHTML = `
                <div class="no-content">
                    <i class="fas fa-tv"></i>
                    <p>${message}</p>
                </div>
            `;
        }
        
        loadContent();
        setInterval(loadContent, 30000);
    </script>
    <script src="../assets/js/tv-reload-checker.js"></script>
    <script src="../assets/js/tv-standby-mode.js"></script>
</body>
</html>
