<?php
session_start(); // Bắt đầu hoặc tiếp tục một session
include 'header.php';

// --- CSRF TOKEN GENERATION ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Nạp file kết nối database để có thể sử dụng biến $conn
require_once 'db.php';
?>

    <section id="hero" class="section-padding">
        <div class="container hero-grid">
            <div class="hero-text">
                <p>Xin chào, tôi là</p>
                <h1>VŨ ĐỨC TRỌNG</h1>
                <h2>Tôi là một <span id="typing-text"></span></h2>
                <p class="tagline">Tạo ra các giải pháp web toàn diện, từ giao diện hiện đại đến logic backend vững chắc.</p>
                <div class="cta-group">
                    <a href="#projects" class="btn btn-primary">Xem Dự án</a>
                    <a href="#contact" class="btn btn-secondary">Liên hệ</a>
                </div>
            </div>
            <div class="hero-image">
                <!-- Thay thế placeholder bằng ảnh thật -->
                <!-- Đường dẫn đã được cập nhật theo cấu trúc assets/img/ -->
                <img src="assets/img/profile-me.jpg" alt="Ảnh đại diện Vũ Đức Trọng" class="profile-picture">
            </div>
        </div>
    </section>

    <section id="about" class="section-padding bg-alt">
        <div class="container">
            <h2 class="reveal-on-scroll">Về Tôi</h2>
            <p class="reveal-on-scroll">Tôi là một nhà phát triển Full-stack với kinh nghiệm sử dụng HTML, CSS, JavaScript cho Front-end và PHP cho Back-end. Tôi tin rằng sự kết hợp giữa thiết kế giao diện tinh tế và code backend hiệu suất cao là chìa khóa cho các ứng dụng web thành công. Tôi luôn tìm tòi các giải pháp mới để cải thiện trải nghiệm người dùng.</p>
        </div>
    </section>

    <section id="skills" class="section-padding">
        <div class="container">
            <h2 class="reveal-on-scroll">Kỹ năng Chuyên môn</h2>
            <div class="skills-grid">
                <!-- Hàng trên: 2 kỹ năng chính -->
                <div class="skill-card reveal-on-scroll" data-skill-level="90">
                    <h3>Front-end (HTML/CSS/JS)</h3>
                    <p>Sử dụng thành thạo Flexbox, Grid, Responsive Design và Vanilla JavaScript/ES6.</p>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: 0;"></div></div>
                </div>
                <div class="skill-card reveal-on-scroll" data-skill-level="85">
                    <h3>PHP & MySQL</h3>
                    <p>Xây dựng API, xử lý Database CRUD và Logic Business bằng PHP thuần/Framework.</p>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: 0;"></div></div>
                </div>
                <!-- Hàng dưới: 3 kỹ năng phụ -->
                <div class="skill-card reveal-on-scroll" data-skill-level="80">
                    <h3>Java (Core & Spring Boot)</h3>
                    <p>Thành thạo lập trình hướng đối tượng Java Core (OOP), xử lý I/O, Multithreading. Có kinh nghiệm xây dựng RESTful APIs, Microservices sử dụng Spring Boot, kết nối và thao tác với các hệ quản trị CSDL.</p>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: 0;"></div></div>
                </div>
                <div class="skill-card reveal-on-scroll" data-skill-level="75">
                    <h3>React & Node.js</h3>
                    <p>Có kinh nghiệm phát triển giao diện người dùng đơn trang <strong>SPA</strong> bằng <strong>React.js</strong> (Hooks, Router) và quản lý trạng thái. Đồng thời, thành thạo xây dựng <strong>RESTful APIs</strong> hiệu suất cao sử dụng môi trường <strong>Node.js/Express.js</strong> và tích hợp cơ sở dữ liệu.</p>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: 0;"></div></div>
                </div>
                <div class="skill-card reveal-on-scroll" data-skill-level="70">
                    <h3>C# (WinForms/Desktop App)</h3>
                    <p>Phát triển các ứng dụng Desktop giao diện người dùng bằng C# WinForms/WPF cho các hệ thống quản lý. Nắm vững mô hình 3 lớp (3-Layer Architecture) và tương tác với các cơ sở dữ liệu như SQL Server.</p>
                    <div class="progress-bar-container"><div class="progress-bar" style="width: 0;"></div></div>
                </div>
            </div>
        </div>
    </section>

    <section id="projects" class="section-padding bg-alt">
        <div class="container">
            <h2 class="reveal-on-scroll">Các Dự án Đã thực hiện</h2>
            <div class="project-filters reveal-on-scroll">
                <button class="filter-btn active" data-filter="all">Tất cả</button>
                <button class="filter-btn" data-filter="fe">Front-end</button>
                <button class="filter-btn" data-filter="php">PHP/Backend</button>
                <button class="filter-btn" data-filter="fullstack">Fullstack</button>
            </div>

            <div class="projects-grid">
                <?php
                // --- CACHING LOGIC ---
                $cache_file = 'cache/projects.json';
                $cache_time = 3600; // 1 giờ (tính bằng giây)
                $projects_data = [];

                // Kiểm tra xem thư mục cache có tồn tại không, nếu không thì tạo
                if (!is_dir('cache')) {
                    mkdir('cache', 0755, true);
                }

                // Kiểm tra file cache có tồn tại và còn hợp lệ không
                if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
                    // Lấy dữ liệu từ cache
                    $projects_data = json_decode(file_get_contents($cache_file), true);
                } else {
                    // --- LẤY DỮ LIỆU TỪ DATABASE ---
                    // Sử dụng Prepared Statements để tăng cường bảo mật và hiệu suất
                    $sql = "SELECT title, description, category, image_url, live_demo_url, github_url FROM projects ORDER BY display_order ASC, created_at DESC";
                    $result = $conn->query($sql);

                    // Thêm kiểm tra lỗi truy vấn
                    if ($result === false) {
                        // Ghi lại lỗi hoặc hiển thị một thông báo thân thiện
                        // Trong môi trường production, không nên hiển thị lỗi chi tiết cho người dùng
                        error_log("Lỗi truy vấn database: " . $conn->error);
                        echo "<p>Đã có lỗi xảy ra khi tải dự án. Vui lòng thử lại sau.</p>";
                    } elseif ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $projects_data[] = $row;
                        }
                        // Lưu dữ liệu vào file cache để sử dụng cho lần sau
                        // Thêm kiểm tra ghi file thất bại
                        if (file_put_contents($cache_file, json_encode($projects_data, JSON_PRETTY_PRINT)) === false) {
                            error_log("Không thể ghi vào file cache: " . $cache_file);
                        }
                    }
                }

                // --- HIỂN THỊ DỮ LIỆU (TỪ CACHE HOẶC DB) ---
                if (!empty($projects_data)) {
                    foreach ($projects_data as $project) {
                        // Sử dụng htmlspecialchars để tránh lỗi XSS khi hiển thị dữ liệu từ DB
                        $title = htmlspecialchars($project['title']);
                        $description = htmlspecialchars($project['description']);
                        $category = htmlspecialchars($project['category']);
                        $imageUrl = htmlspecialchars($project['image_url'] ?? 'assets/img/default.jpg'); // Ảnh mặc định nếu null
                        $liveUrl = htmlspecialchars($project['live_demo_url'] ?? '#');
                        $githubUrl = htmlspecialchars($project['github_url'] ?? '#');
                ?>
                        <article class="project-card" data-type="<?php echo $category; ?>">
                            <div class="project-image" style="background-image: url('<?php echo $imageUrl; ?>'); background-size: cover; background-position: center;">
                                <!-- Ảnh sẽ được hiển thị làm nền cho div này -->
                            </div>
                            <div class="project-info">
                                <h3><?php echo $title; ?></h3>
                                <p><?php echo $description; ?></p>
                                <div class="project-links">
                                    <a href="<?php echo $liveUrl; ?>" target="_blank" rel="noopener noreferrer" class="link-live">Live Demo</a>
                                    <a href="<?php echo $githubUrl; ?>" target="_blank" rel="noopener noreferrer" class="link-github">GitHub Code</a>
                                </div>
                            </div>
                        </article>
                <?php } // Kết thúc vòng lặp foreach
                } else {
                    // Hiển thị thông báo nếu không có dự án nào trong database
                    echo "<p>Hiện tại chưa có dự án nào để hiển thị.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <section id="contact" class="section-padding">
        <div class="container">
            <h2 class="reveal-on-scroll">Liên hệ với tôi</h2>
            <div class="contact-flex reveal-on-scroll">
                <form id="contact-form" class="contact-form">
                    <p>Tôi luôn sẵn sàng cho các cơ hội công việc hoặc dự án mới!</p>
                    <!-- CSRF Token Field -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="text" name="name" placeholder="Tên của bạn" required>
                    <input type="email" name="email" placeholder="Email của bạn" required>
                    <textarea name="message" placeholder="Tin nhắn của bạn" rows="5" required></textarea>
                    <button type="submit" class="btn btn-primary">Gửi Tin nhắn</button>
                </form>
                <div class="social-links">
                    <h3>Kết nối</h3>
                    <a href="https://linkedin.com/in/vuductrong" target="_blank" rel="noopener noreferrer">LinkedIn Profile</a>
                    <a href="https://github.com/vuductrong" target="_blank" rel="noopener noreferrer">GitHub Repository</a>
                </div>
            </div>
        </div>
    </section>

<?php
include 'footer.php';
// Đóng kết nối database sau khi đã sử dụng xong
$conn->close();
?>