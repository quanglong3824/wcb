</div> <!-- .app-container -->

<script>
    // Confirm logout
    function confirmLogout() {
        if (confirm('Bạn có chắc muốn đăng xuất?')) {
            window.location.href = '<?php echo $basePath ?? "./"; ?>auth/logout.php';
        }
    }

    // Mobile sidebar toggle functionality
    (function () {
        const hamburger = document.getElementById('hamburgerMenu');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (!hamburger || !sidebar) return;

        // Toggle sidebar
        function toggleSidebar() {
            hamburger.classList.toggle('active');
            sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        // Close sidebar
        function closeSidebar() {
            hamburger.classList.remove('active');
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Event listeners
        hamburger.addEventListener('click', toggleSidebar);

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        // Close on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Close sidebar when clicking nav links (mobile)
        sidebar.querySelectorAll('.nav-item').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    })();
</script>
</body>

</html>