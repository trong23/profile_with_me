<?php
session_start(); // Bắt đầu session để có thể truy cập $_SESSION

// Thiết lập header để trả về JSON
header('Content-Type: application/json');

// Nạp file kết nối database
require_once 'db.php';

// Chỉ cho phép phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

// Lấy dữ liệu JSON từ body của request
$data = json_decode(file_get_contents('php://input'), true);

// --- CSRF TOKEN VALIDATION ---
$submitted_token = $data['csrf_token'] ?? '';

// So sánh token được gửi lên với token trong session một cách an toàn
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submitted_token)) {
    // Nếu token không hợp lệ, từ chối yêu cầu
    // http_response_code(403); // Forbidden
    echo json_encode(['success' => false, 'message' => 'Lỗi bảo mật (CSRF token không hợp lệ). Vui lòng tải lại trang và thử lại.']);
    exit;
}

// --- VALIDATION ---
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$message = $data['message'] ?? '';

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Địa chỉ email không hợp lệ.']);
    exit;
}

// --- LƯU VÀO DATABASE ---
// Sử dụng prepared statements để chống SQL Injection
$stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");

// 'sss' có nghĩa là 3 tham số đều là kiểu string (string, string, string)
$stmt->bind_param("sss", $name, $email, $message);

if (!$stmt->execute()) {
    // Nếu có lỗi khi thực thi, trả về lỗi và không gửi email
    // Trong thực tế, bạn nên ghi lỗi này vào file log thay vì chỉ trả về cho client
    echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu tin nhắn vào database.']);
    $stmt->close();
    $conn->close();
    exit;
}

// Đóng statement sau khi thực thi thành công
$stmt->close();

// --- GỬI EMAIL ---

// *** THAY ĐỔI THÔNG TIN CỦA BẠN Ở ĐÂY ***
$to = 'vuductrong.dev@email.com'; // Email bạn muốn nhận thư
$subject = 'Tin nhắn mới từ Portfolio của Vũ Đức Trọng';

// Làm sạch dữ liệu trước khi đưa vào email để tránh header injection
$name = htmlspecialchars($name);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars($message);

$body = "Bạn có một tin nhắn mới từ form liên hệ trên portfolio:\n\n";
$body .= "Tên: " . $name . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Nội dung:\n" . $message . "\n";

$headers = "From: no-reply@yourdomain.com\r\n"; // Thay yourdomain.com bằng tên miền của bạn
$headers .= "Reply-To: " . $email . "\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Cảm ơn bạn đã liên hệ! Tôi sẽ phản hồi sớm nhất có thể.']);
} else {
    // Tin nhắn đã được lưu vào DB, nhưng email gửi lỗi.
    // Vẫn trả về success cho người dùng, vì dữ liệu quan trọng nhất đã được lưu.
    echo json_encode(['success' => true, 'message' => 'Cảm ơn bạn! Tin nhắn của bạn đã được nhận.']);
}

// Đóng kết nối database
$conn->close();
?>