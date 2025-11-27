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
        
        #content-display {
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity;
        }
        
        #content-display img,
        #content-display video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
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
        <h2><i class="fas fa-tv"></i> TV Basement</h2>
        <p>Tầng hầm - Aurora Hotel Plaza</p>
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
        const TV_ID = 1; // TV Basement
        let contentList = [];
        let currentIndex = 0;
        let slideInterval = null;
        
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('Fullscreen error:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        // Load all content assigned to this TV (max 3)
        function loadContentList() {
            console.log('Loading content list for TV ID:', TV_ID);
            
            fetch('../api/get-tv-content.php?tv_id=' + TV_ID + '&get_all=1')
                .then(response => response.json())
                .then(data => {
                    console.log('API Response:', data);
                    
                    if (data.success && data.contents && data.contents.length > 0) {
                        contentList = data.contents.slice(0, 3); // Tối đa 3 WCB
                        console.log('Loaded', contentList.length, 'contents');
                        
                        // Start slideshow if multiple contents
                        if (contentList.length > 1) {
                            startSlideshow();
                        } else {
                            displayContent(contentList[0]);
                        }
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
        
        // Start slideshow with smooth fade effect
        function startSlideshow() {
            // Display first content
            displayContent(contentList[currentIndex]);
            
            // Clear existing interval
            if (slideInterval) {
                clearInterval(slideInterval);
            }
            
            // Change content every 8 seconds
            slideInterval = setInterval(() => {
                nextSlide();
            }, 8000);
        }
        
        // Next slide with smooth transition
        function nextSlide() {
            const display = document.getElementById('content-display');
            
            // Fade out
            display.style.opacity = '0';
            
            // Wait for fade out, then change content
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % contentList.length;
                displayContent(contentList[currentIndex]);
                
                // Fade in
                setTimeout(() => {
                    display.style.opacity = '1';
                }, 50);
            }, 800);
        }
        
        // Display content
        function displayContent(content) {
            const display = document.getElementById('content-display');
            
            console.log('Displaying:', content.name, '- Type:', content.type);
            
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
        loadContentList();
        
        // Refresh content list every 5 minutes
        setInterval(loadContentList, 300000);
    </script>
    <script src="../assets/js/tv-reload-checker.js"></script>
</body>
</html>
