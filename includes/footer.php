    </div> <!-- .app-container -->
    
    <script>
        // Confirm logout
        function confirmLogout() {
            if (confirm('Bạn có chắc muốn đăng xuất?')) {
                window.location.href = '<?php echo $basePath ?? "./"; ?>auth/logout.php';
            }
        }
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>
