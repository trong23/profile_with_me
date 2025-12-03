<?php
require_once 'admin_auth.php'; // Bảo vệ trang
require_once 'db.php';         // Kết nối DB

$id = $_GET['id'] ?? 0;
$error_message = '';

// --- XỬ LÝ LƯU DỮ LIỆU (INSERT/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $live_demo_url = $_POST['live_demo_url'] ?? '';
    $github_url = $_POST['github_url'] ?? '';
    $display_order = $_POST['display_order'] ?? 0;

    if (empty($title) || empty($category)) {
        $error_message = "Tiêu đề và Phân loại là bắt buộc.";
    } else {
        if ($project_id) { // --- UPDATE ---
            $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, category=?, image_url=?, live_demo_url=?, github_url=?, display_order=? WHERE id=?");
            $stmt->bind_param("ssssssii", $title, $description, $category, $image_url, $live_demo_url, $github_url, $display_order, $project_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Dự án đã được cập nhật thành công!";
            }
        } else { // --- INSERT ---
            $stmt = $conn->prepare("INSERT INTO projects (title, description, category, image_url, live_demo_url, github_url, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $title, $description, $category, $image_url, $live_demo_url, $github_url, $display_order);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Dự án đã được thêm thành công!";
            }
        }
        // Xóa file cache để trang chủ tải lại dữ liệu mới
        $cache_file = 'cache/projects.json';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
        header('Location: admin_projects.php'); // Chuyển hướng về trang danh sách
        exit;
    }
}

// --- LẤY DỮ LIỆU ĐỂ HIỂN THỊ FORM ---
$project = [
    'id' => 0, 'title' => '', 'description' => '', 'category' => '', 'image_url' => '',
    'live_demo_url' => '', 'github_url' => '', 'display_order' => 0
];

if ($id > 0) { // Nếu là edit, lấy thông tin dự án
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        // Không tìm thấy dự án, có thể chuyển hướng hoặc báo lỗi
        header('Location: admin_projects.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Chỉnh sửa' : 'Thêm'; ?> Dự án</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .admin-container { max-width: 900px; margin: 2rem auto; padding: 2rem; }
        .message { padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container navbar-flex">
            <a href="admin_dashboard.php" class="logo">ADMIN PANEL</a>
            <a href="admin_logout.php" class="btn btn-secondary">Đăng xuất</a>
        </div>
    </header>

    <main class="admin-container">
        <h1 style="color: #f5f5dc;"><?php echo $id ? 'Chỉnh sửa Dự án' : 'Thêm Dự án Mới'; ?></h1>

        <?php if ($error_message): ?><div class="message error"><?php echo $error_message; ?></div><?php endif; ?>

        <form method="POST" action="admin_project_edit.php<?php echo $id ? '?id='.$id : ''; ?>" class="contact-form">
            <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
            
            <label for="title">Tiêu đề (*)</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>

            <label for="description">Mô tả</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($project['description']); ?></textarea>

            <label for="category">Phân loại (*)</label>
            <select id="category" name="category" required>
                <option value="fe" <?php if($project['category'] === 'front-end') echo 'selected'; ?>>Front-end</option>
                <option value="php" <?php if($project['category'] === 'php') echo 'selected'; ?>>PHP/Backend</option>
                <option value="fullstack" <?php if($project['category'] === 'fullstack') echo 'selected'; ?>>Fullstack</option>
            </select>

            <label for="image_url">URL Hình ảnh (ví dụ: assets/img/project.jpg)</label>
            <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($project['image_url']); ?>">

            <label for="live_demo_url">URL Live Demo</label>
            <input type="text" id="live_demo_url" name="live_demo_url" value="<?php echo htmlspecialchars($project['live_demo_url']); ?>">

            <label for="github_url">URL GitHub</label>
            <input type="text" id="github_url" name="github_url" value="<?php echo htmlspecialchars($project['github_url']); ?>">

            <label for="display_order">Thứ tự hiển thị (số nhỏ hơn lên trước)</label>
            <input type="number" id="display_order" name="display_order" value="<?php echo htmlspecialchars($project['display_order']); ?>">

            <button type="submit" class="btn btn-primary">Lưu Dự án</button>
            <a href="admin_projects.php" class="btn btn-secondary">Hủy</a>
        </form>
    </main>
</body>
</html>