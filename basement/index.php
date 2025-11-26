<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TV Basement - Welcome Board</title>
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
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px 25px;
            border-radius: 10px;
            z-index: 999;
        }
        
        .tv-info h2 {
            margin: 0 0 5px 0;
            font-size: 1.5em;
            color: #d4af37;
        }
        
        .tv-info p {
            margin: 0;
            opacity: 0.8;
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
        <h2><i class="fas fa-tv"></i> TV Basement</h2>
        <p>Tầng hầm - Quang Long Hotel</p>
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
        // Toggle fullscreen
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        // Note: Auto fullscreen is not allowed by browsers
        // User must click the fullscreen button manually
        
        // Load content from API
        function loadContent() {
            console.log('Loading content for TV ID: 1');
            
            fetch('../api/get-tv-content.php?tv_id=1')
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
        
        // Display content
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
        
        // Show no content message
        function showNoContent(message) {
            const display = document.getElementById('content-display');
            display.innerHTML = `
                <div class="no-content">
                    <i class="fas fa-tv"></i>
                    <p>${message}</p>
                </div>
            `;
        }
        
        // Load content on page load
        loadContent();
        
        // Refresh content every 30 seconds
        setInterval(loadContent, 30000);
    </script>
</body>
</html>
