    <!-- Nút Cuộn Lên Đầu Trang -->
    <a href="#" id="scroll-to-top" class="scroll-to-top" title="Lên đầu trang"><i class="fas fa-angle-up"></i></a>
    <div id="menu-overlay" class="menu-overlay"></div><footer class="main-footer">
        <div class="container">
            <p style="color: #f5f5dc;">&copy; 2024 Vũ Đức Trọng. Thiết kế và phát triển bởi chính tôi.</p>
        </div>
    </footer>

    <script src="assets/script.js"></script>
    <script>
    // === FIX: Logo click scroll to top ===
    // Đoạn mã này đảm bảo khi nhấn vào logo, trang sẽ cuộn lên đầu
    // mà không ảnh hưởng đến tính năng nhấn 3 lần để vào trang admin.
    document.addEventListener('DOMContentLoaded', () => {
        const logoLink = document.querySelector('a.logo');
        if (logoLink) {
            // Tìm và loại bỏ các event listener 'click' cũ có thể gây xung đột
            // Bằng cách thay thế phần tử bằng một bản sao của chính nó.
            const newLogoLink = logoLink.cloneNode(true);
            logoLink.parentNode.replaceChild(newLogoLink, logoLink);

            // Thêm lại event listener mới, đã được sửa lỗi
            let clickCount = 0;
            newLogoLink.addEventListener('click', function(event) {
                event.preventDefault(); // Chặn hành vi mặc định để tự quản lý
                clickCount++;
                setTimeout(() => { clickCount = 0; }, 600); // Reset sau 600ms

                if (clickCount === 3) window.location.href = 'admin_login.php';
                else if (clickCount === 1) window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });

    // === FIX: Mobile Menu Closing Behavior ===
    // Đoạn mã này cho phép đóng menu mobile bằng cách nhấn lại vào nút hamburger
    // hoặc nhấn vào vùng nền mờ phía sau.
    document.addEventListener('DOMContentLoaded', () => {
        const hamburgerButton = document.getElementById('hamburger-menu');
        const overlay = document.getElementById('menu-overlay');
        const navLinks = document.querySelectorAll('#nav-links a');

        const closeMenu = () => {
            document.body.classList.remove('menu-active');
        };

        const toggleMenu = () => {
            document.body.classList.toggle('menu-active');
        };

        if (hamburgerButton) {
            hamburgerButton.addEventListener('click', toggleMenu); // Mở/đóng menu
        }
        if (overlay) {
            overlay.addEventListener('click', closeMenu); // Chỉ đóng menu
        }

        // Thêm sự kiện click cho từng link trong menu để tự động đóng lại
        if (navLinks) {
            navLinks.forEach(link => {
                link.addEventListener('click', closeMenu);
            });
        }
    });

    // === FIX: Move Theme Toggle to Mobile Menu ===
    // Di chuyển nút bật/tắt theme vào trong menu mobile để không bị ẩn đi.
    document.addEventListener('DOMContentLoaded', () => {
        const navLinks = document.getElementById('nav-links');
        const themeToggle = document.getElementById('theme-toggle');
        // Chỉ thực hiện khi ở màn hình mobile (khi hamburger menu hiển thị)
        if (window.innerWidth <= 768 && navLinks && themeToggle) {
            navLinks.appendChild(themeToggle);
        }
    });

    // === Scroll to Top Button Logic ===
    // Hiển thị nút khi người dùng cuộn xuống và xử lý sự kiện click.
    document.addEventListener('DOMContentLoaded', () => {
        const scrollToTopButton = document.getElementById('scroll-to-top');

        if (scrollToTopButton) {
            // Hiển thị/ẩn nút dựa trên vị trí cuộn
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) { // Hiện nút sau khi cuộn 300px
                    scrollToTopButton.classList.add('visible');
                } else {
                    scrollToTopButton.classList.remove('visible');
                }
            });

            // Xử lý sự kiện click để cuộn lên đầu trang mượt mà
            scrollToTopButton.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
    </script>
</body>
</html>