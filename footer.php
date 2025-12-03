    <footer>
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
    </script>
</body>
</html>