<?php
/**
 * File này chịu trách nhiệm kết nối đến cơ sở dữ liệu.
 * Nó sẽ được include ở đầu các file PHP cần truy vấn database.
 */

// --- THÔNG TIN KẾT NỐI DATABASE ---
// Thay đổi các giá trị này cho phù hợp với môi trường của bạn.
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // User mặc định của XAMPP
define('DB_PASS', '');     // Password mặc định của XAMPP là rỗng
define('DB_NAME', 'my_profile_db'); // Tên database bạn đã tạo ở bước trước

// --- TẠO KẾT NỐI SỬ DỤNG MySQLi (Object-Oriented style) ---
// MySQLi (MySQL Improved) là một lựa chọn tốt, hiện đại và an toàn.
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// --- KIỂM TRA KẾT NỐI ---
// Luôn kiểm tra kết nối sau khi khởi tạo.
if ($conn->connect_error) {
    // Dừng thực thi và hiển thị lỗi nếu không kết nối được.
    // Trong môi trường production, bạn nên ghi lỗi ra file log thay vì hiển thị trực tiếp.
    die("Kết nối Database thất bại: " . $conn->connect_error);
}

// --- THIẾT LẬP BẢNG MÃ UTF-8 ---
// Rất quan trọng để hiển thị và lưu trữ tiếng Việt chính xác.
$conn->set_charset("utf8mb4");
?>