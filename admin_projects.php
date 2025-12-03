<?php
require_once 'admin_auth.php'; // Bảo vệ trang, yêu cầu đăng nhập
require_once 'db.php';         // Kết nối database

$action = $_GET['action'] ?? 'list'; // Hành động mặc định là 'list'
$id = (int)($_GET['id'] ?? 0);
$error_message = '';
$success_message = '';

// --- XỬ LÝ HÀNH ĐỘNG XÓA (GET request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete' && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Dự án đã được xóa thành công!";
        // Xóa file cache để trang chủ tải lại dữ liệu mới
        $cache_file = 'cache/projects.json';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }
    header('Location: admin_projects.php');
    exit;
}

// Lấy thông báo từ session nếu có
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Dự án</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* CSS cho trang admin */
        .admin-container { max-width: 1200px; margin: 2rem auto; padding: 2rem; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
        .admin-table th, .admin-table td { padding: 12px; border: 1px solid var(--border-color, #444); text-align: left; }
        .admin-table th { background-color: var(--card-background); }
        .admin-table td {
            color: #f5f5dc; /* Màu chữ trắng ngà cho các ô dữ liệu */
        }
        .admin-table img { max-width: 100px; height: auto; }
        .action-links a { margin-right: 10px; transition: color 0.3s ease; }
        .action-links a:hover {
            color: #f5f5dc; /* Đổi màu link hành động khi hover */
        }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container navbar-flex">
            <a href="admin_dashboard.php" class="logo">ADMIN PANEL</a>
            <nav>
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_logout.php" class="btn btn-secondary">Đăng xuất</a>
            </nav>
        </div>
    </header>

    <main class="admin-container">
        <h1 style="color: #f5f5dc;">Quản lý Dự án</h1>

        <?php if ($success_message): ?><div class="message success"><?php echo $success_message; ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="message error"><?php echo $error_message; ?></div><?php endif; ?>

        <a href="admin_project_edit.php" class="btn btn-primary">Thêm Dự án Mới</a>

        <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Phân loại</th>
                        <th>Ảnh</th>
                        <th>Thứ tự</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT id, title, category, image_url, display_order FROM projects ORDER BY display_order ASC, id DESC");
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td>
                                <?php if (!empty($row['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Ảnh dự án">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['display_order']; ?></td>
                            <td class="action-links">
                                <a href="admin_project_edit.php?id=<?php echo $row['id']; ?>">Sửa</a>
                                <a href="admin_projects.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa dự án này không?');">Xóa</a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Chưa có dự án nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
    </main>
</body>
</html>
<?php
$conn->close();
?>