<?php
require_once 'admin_auth.php'; // Yêu cầu xác thực trước khi hiển thị trang
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* CSS riêng cho trang Dashboard */
        h1 {
            color: #f5f5dc;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .dashboard-card {
            /* Sử dụng lại hiệu ứng glassmorphism */
            background-color: var(--overlay-color);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container navbar-flex">
            <a href="admin_dashboard.php" class="logo">ADMIN PANEL</a>
            <nav>
                <a href="admin_projects.php">Quản lý Dự án</a>
                <a href="admin_messages.php">Tin nhắn</a>
                <a href="admin_logout.php" class="btn btn-secondary">Đăng xuất</a>
            </nav>
        </div>
    </header>
    <main class="container section-padding">
        <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
        <p style="color: #f5f5dc;">Chọn một mục bên dưới để bắt đầu quản lý nội dung trang web của bạn.</p>

        <div class="dashboard-grid">
            <a href="admin_projects.php" class="dashboard-card">
                <h2>Quản lý Dự án</h2>
                <p>Thêm, sửa hoặc xóa các dự án được hiển thị trên portfolio của bạn.</p>
            </a>

            <a href="admin_messages.php" class="dashboard-card">
                <h2>Tin nhắn Liên hệ</h2>
                <p>Xem và quản lý các tin nhắn được gửi từ form liên hệ.</p>
            </a>
        </div>
    </main>
</body>
</html>