document.addEventListener("DOMContentLoaded", () => {
    // ====================================
    // 1. Dark/Light Mode Toggle
    // ====================================
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    
    // Kiểm tra Local Storage hoặc Prefer OS
    const currentTheme = localStorage.getItem('theme');
    
    if (currentTheme === 'dark' || (!currentTheme && prefersDarkScheme.matches)) {
        document.body.classList.add('dark-mode');
        themeToggle.textContent = '☀️';
    } else {
        themeToggle.textContent = '💡';
    }

    themeToggle.addEventListener('click', () => {
        const isDarkMode = document.body.classList.toggle('dark-mode');
        localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        if (isDarkMode) {
            themeToggle.textContent = '☀️';
        } else {
            themeToggle.textContent = '💡';
        }
    });
    
    // ====================================
    // 2. Responsive Hamburger Menu
    // ====================================
    const hamburger = document.getElementById('hamburger-menu');
    const navLinks = document.getElementById('nav-links');
    
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        // Thêm/Xóa class 'open' để tạo hiệu ứng chuyển đổi cho icon hamburger
        hamburger.classList.toggle('open'); 
    });

    // Đóng menu khi click vào một link (trên mobile)
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                hamburger.classList.remove('open');
            }
        });
    });

    // ====================================
    // 3. Typing Effect (Hiệu ứng Gõ chữ)
    // ====================================
    const typingElement = document.getElementById('typing-text');
    const roles = ['Web Developer', 'Full-stack Engineer', 'PHP Specialist', 'UX/UI Enthusiast'];
    let roleIndex = 0;
    let charIndex = 0;
    let isDeleting = false;

    function type() {
        const currentRole = roles[roleIndex];
        const displayText = isDeleting 
            ? currentRole.substring(0, charIndex - 1)
            : currentRole.substring(0, charIndex + 1);

        typingElement.textContent = displayText;

        const typingSpeed = 150;
        const deletingSpeed = 80;
        let delay = typingSpeed;

        if (isDeleting) {
            delay = deletingSpeed;
            charIndex--;
        } else {
            charIndex++;
        }

        if (!isDeleting && charIndex === currentRole.length + 1) {
            delay = 2000; // Dừng 2s sau khi gõ xong
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            roleIndex = (roleIndex + 1) % roles.length;
            delay = 500; // Dừng 0.5s sau khi xóa xong
        }

        setTimeout(type, delay);
    }
    type();

    // ====================================
    // 4. Project Filtering (Lọc Dự án)
    // ====================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Xóa trạng thái active cũ
            filterBtns.forEach(b => b.classList.remove('active'));
            // Set trạng thái active mới
            btn.classList.add('active');

            const filterType = btn.getAttribute('data-filter');

            projectCards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                
                // Hiệu ứng Fade Out/In khi lọc
                if (filterType === 'all' || cardType === filterType) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 50); // Delay nhỏ để hiệu ứng fade-in mượt hơn
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 500); // Ẩn hẳn sau khi fade out
                }
            });
        });
    });
    
    // ====================================
    // 5. Skills Progress Bar (Intersection Observer)
    // ====================================
    const skillCards = document.querySelectorAll('.skill-card');
    
    const observerOptions = {
        root: null, // viewport
        rootMargin: '0px',
        threshold: 0.5 // Kích hoạt khi 50% element visible
    };

    const skillObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const card = entry.target;
                const level = card.getAttribute('data-skill-level');
                const progressBar = card.querySelector('.progress-bar');
                
                progressBar.style.width = `${level}%`; // Đổ đầy thanh tiến trình
                
                // Ngừng quan sát sau khi đã đổ đầy (chỉ chạy 1 lần)
                observer.unobserve(card);
            }
        });
    }, observerOptions);

    skillCards.forEach(card => {
        skillObserver.observe(card);
    });
    
    // ====================================
    // 6. Contact Form Submission
    // ====================================
    const contactForm = document.getElementById('contact-form');
    const formButton = contactForm.querySelector('button[type="submit"]');

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Ngăn form gửi theo cách truyền thống

        const formData = new FormData(contactForm);
        const data = Object.fromEntries(formData.entries());

        // Vô hiệu hóa nút gửi và hiển thị trạng thái đang gửi
        const originalButtonText = formButton.textContent;
        formButton.disabled = true;
        formButton.textContent = 'Đang gửi...';

        try {
            const response = await fetch('contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            // Hiển thị thông báo kết quả cho người dùng
            alert(result.message);

            if (result.success) {
                // Xóa các trường trong form sau khi gửi thành công
                contactForm.reset();
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Đã có lỗi kết nối đến máy chủ. Vui lòng thử lại.');
        } finally {
            // Khôi phục lại nút gửi
            formButton.disabled = false;
            formButton.textContent = originalButtonText;
        }
    });

    // ====================================
    // 7. Scroll Reveal Effect (Hiệu ứng xuất hiện khi cuộn)
    // ====================================
    const revealElements = document.querySelectorAll('.reveal-on-scroll');

    const revealObserverOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15 // Kích hoạt khi 15% element visible
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Ngừng quan sát sau khi đã hiển thị để không lặp lại hiệu ứng
                observer.unobserve(entry.target);
            }
        });
    }, revealObserverOptions);

    revealElements.forEach(el => {
        revealObserver.observe(el);
    });

    // ====================================
    // 8. Secret Admin Redirect
    // ====================================
    const logo = document.querySelector('#main-header .logo');
    let clickCount = 0;
    let clickTimer = null;

    logo.addEventListener('click', (e) => {
        // Ngăn link #hero hoạt động để không cuộn trang lên trên khi click
        e.preventDefault(); 
        
        clickCount++;

        // Nếu đây là lần click đầu tiên, bắt đầu bộ đếm thời gian để reset
        if (clickCount === 1) {
            clickTimer = setTimeout(() => {
                clickCount = 0; // Reset sau 2 giây nếu không có click tiếp theo
            }, 2000); // 2 giây
        }

        // Nếu click đủ 3 lần trong khoảng thời gian cho phép
        if (clickCount === 3) {
            clearTimeout(clickTimer); // Hủy bộ đếm thời gian
            window.location.href = 'admin_login.php'; // Chuyển hướng đến trang đăng nhập admin
        }
    });
});
