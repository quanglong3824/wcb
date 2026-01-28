<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slideshow Player</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .slideshow-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            animation: kenBurns 15s ease-in-out infinite alternate;
        }

        /* Ken Burns Effect */
        @keyframes kenBurns {
            0% {
                transform: scale(1) translate(0, 0);
            }

            100% {
                transform: scale(1.1) translate(-2%, -2%);
            }
        }

        /* Loading State */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top-color: #a78bfa;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Error State */
        .error {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ef4444;
            font-size: 1.2rem;
            text-align: center;
            max-width: 600px;
            padding: 2rem;
        }

        /* Hidden audio element */
        #audioPlayer {
            display: none;
        }
    </style>
</head>

<body>
    <div class="slideshow-container" id="slideshowContainer">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Đang tải slideshow...</p>
        </div>
    </div>

    <audio id="audioPlayer"></audio>

    <script>
        // Get slideshow ID from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const slideshowId = urlParams.get('id');

        if (!slideshowId) {
            showError('Không tìm thấy ID slideshow. Vui lòng kiểm tra URL.');
        } else {
            loadSlideshow(slideshowId);
        }

        /**
         * Load slideshow data
         */
        async function loadSlideshow(id) {
            try {
                const response = await fetch(`api/slideshows.php?action=get&id=${id}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Lỗi khi tải slideshow');
                }

                const slideshow = data.slideshow;

                if (!slideshow.images || slideshow.images.length === 0) {
                    throw new Error('Slideshow không có ảnh nào');
                }

                initSlideshow(slideshow);

            } catch (error) {
                console.error('Error loading slideshow:', error);
                showError(error.message);
            }
        }

        /**
         * Initialize and start slideshow
         */
        function initSlideshow(slideshow) {
            const container = document.getElementById('slideshowContainer');
            const loading = document.getElementById('loading');

            // Clear loading
            loading.style.display = 'none';

            // Create slides
            const images = slideshow.images.sort((a, b) => a.display_order - b.display_order);
            images.forEach((img, index) => {
                const slide = document.createElement('div');
                slide.className = 'slide';
                if (index === 0) slide.classList.add('active');

                const imgElement = document.createElement('img');
                imgElement.src = img.file_path;
                imgElement.alt = img.name;

                slide.appendChild(imgElement);
                container.appendChild(slide);
            });

            // Setup audio if available
            if (slideshow.audio_id && slideshow.audio_path) {
                setupAudio(slideshow.audio_path, slideshow.total_duration, slideshow.fade_out_duration);
            }

            // Start slideshow
            startSlideshow(images.length, slideshow.transition_duration);
        }

        /**
         * Setup and play audio
         */
        function setupAudio(audioPath, totalDuration, fadeOutDuration) {
            const audio = document.getElementById('audioPlayer');
            audio.src = audioPath;
            audio.loop = false;

            // Play audio
            audio.play().catch(error => {
                console.warn('Could not autoplay audio:', error);
            });

            // Schedule fade out
            if (fadeOutDuration > 0) {
                const fadeOutStart = (totalDuration - fadeOutDuration) * 1000;
                setTimeout(() => {
                    fadeOutAudio(audio, fadeOutDuration * 1000);
                }, fadeOutStart);
            }
        }

        /**
         * Fade out audio
         */
        function fadeOutAudio(audio, duration) {
            const step = 0.05;
            const interval = duration / (1 / step);
            let volume = audio.volume;

            const fadeOut = setInterval(() => {
                volume -= step;
                if (volume <= 0) {
                    volume = 0;
                    clearInterval(fadeOut);
                    audio.pause();
                }
                audio.volume = volume;
            }, interval);
        }

        /**
         * Start slideshow rotation
         */
        function startSlideshow(imageCount, transitionDuration) {
            if (imageCount <= 1) return; // No need to rotate if only 1 image

            let currentIndex = 0;
            const slides = document.querySelectorAll('.slide');

            setInterval(() => {
                slides[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % imageCount;
                slides[currentIndex].classList.add('active');
            }, transitionDuration * 1000);
        }

        /**
         * Show error message
         */
        function showError(message) {
            const container = document.getElementById('slideshowContainer');
            container.innerHTML = `
                <div class="error">
                    <p>❌ Lỗi</p>
                    <p>${message}</p>
                </div>
            `;
        }

        // Handle fullscreen
        function enterFullscreen() {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        }

        // Auto enter fullscreen on click
        document.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                enterFullscreen();
            }
        });
    </script>
</body>

</html>