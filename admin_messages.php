<?php
require_once 'admin_auth.php'; // Bảo vệ trang, yêu cầu đăng nhập
require_once 'db.php';         // Kết nối database

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$success_message = '';

// --- XỬ LÝ HÀNH ĐỘNG ĐÁNH DẤU ĐÃ ĐỌC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'mark_read' && $id > 0) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Tin nhắn đã được đánh dấu là đã đọc.";
    }
    header('Location: admin_messages.php');
    exit;
}

// --- XỬ LÝ HÀNH ĐỘNG XÓA ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete' && $id > 0) {
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Tin nhắn đã được xóa thành công.";
    }
    header('Location: admin_messages.php');
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
    <title>Quản lý Tin nhắn</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* CSS cho trang admin */
        .admin-container { max-width: 1200px; margin: 2rem auto; padding: 2rem; }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 2rem; table-layout: fixed; }
        .admin-table th, .admin-table td { padding: 12px; border: 1px solid var(--border-color, #444); text-align: left; word-wrap: break-word; }
        .admin-table th { background-color: var(--card-background); }
        .admin-table td {
            color: #f5f5dc; /* Màu chữ trắng ngà cho các ô dữ liệu */
        }
        .action-links a { margin-right: 10px; transition: color 0.3s ease; }
        .action-links a:hover {
            color: #f5f5dc; /* Đổi màu link hành động khi hover */
        }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .unread-row { font-weight: bold; background-color: rgba(26, 188, 156, 0.1); }
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
        <h1 style="color: #f5f5dc;">Quản lý Tin nhắn</h1>

        <?php if ($success_message): ?><div class="message success"><?php echo $success_message; ?></div><?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Người gửi</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 35%;">Nội dung</th>
                    <th style="width: 15%;">Thời gian</th>
                    <th style="width: 20%;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ưu tiên tin chưa đọc (is_read = 0) lên đầu, sau đó là tin mới nhất
                $result = $conn->query("SELECT id, name, email, message, is_read, received_at FROM messages ORDER BY is_read ASC, received_at DESC");
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $row_class = $row['is_read'] == 0 ? 'unread-row' : '';
                ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                        <td>
                            <?php 
                                $date = new DateTime($row['received_at']);
                                echo $date->format('d-m-Y H:i');
                            ?>
                        </td>
                        <td class="action-links">
                            <?php if ($row['is_read'] == 0): ?>
                                <a href="admin_messages.php?action=mark_read&id=<?php echo $row['id']; ?>">Đánh dấu đã đọc</a>
                            <?php endif; ?>
                            <a href="admin_messages.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa tin nhắn này không?');">Xóa</a>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Hộp thư của bạn trống.</td>
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
