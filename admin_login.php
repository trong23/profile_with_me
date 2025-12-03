<?php
session_start(); // Bắt đầu session để lưu trạng thái đăng nhập

// Nếu đã đăng nhập rồi, chuyển hướng thẳng tới dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

// --- CẤU HÌNH ADMIN ---
// Trong một ứng dụng thực tế, bạn sẽ lấy thông tin này từ database.
// Ở đây, chúng ta định nghĩa sẵn để đơn giản hóa.
define('ADMIN_USERNAME', 'admin');
// Quan trọng: Đây là mật khẩu đã được mã hóa. Mật khẩu gốc là 'admin123'
// Mật khẩu gốc là 'admin123'
define('ADMIN_PASSWORD', 'admin123');
$error_message = '';

// --- XỬ LÝ ĐĂNG NHẬP KHI FORM ĐƯỢC GỬI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kiểm tra username và xác thực mật khẩu
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Đăng nhập thành công, lưu trạng thái vào session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        // Chuyển hướng đến trang dashboard
        header('Location: admin_dashboard.php');
        exit;
    } else {
        // Đăng nhập thất bại
        $error_message = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-container { max-width: 400px; width: 100%; padding: 2rem; border-radius: 8px; }
        .login-container h1 { text-align: center; color: var(--primary-color); }
        .error { background-color: #ffdddd; border-left: 6px solid #f44336; padding: 10px; margin-bottom: 15px; }
        /* Đảm bảo chữ luôn dễ đọc trên nền tối */
        .login-container p,
        .login-container label {
            color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="login-container bg-alt">
        <h1>Admin Login</h1>
        <p style="text-align: center; margin-bottom: 20px;">Đăng nhập để quản lý portfolio.</p>

        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php" class="contact-form" style="padding: 0; background: none; backdrop-filter: none;">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Đăng nhập</button>
            <a href="index.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; text-align: center; margin-right: 0;">Quay lại trang chủ</a>
        </form>
        <p style="text-align:center; font-size: 0.8rem; margin-top: 1rem;">(Username: admin, Password: thêm đuôi 123)</p>
    </div>
</body>
</html>