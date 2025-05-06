<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE BOOKSTORE SYSTEM</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_book_Db";

    function countBooks() {
        global $servername, $username, $password, $dbname;
        $count = 0;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            return 0;
        }
        $sql = "SELECT COUNT(file) as total FROM books";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $count = $row['total'];
        }
        $conn->close();
        return $count;
    }

    function countUsers() {
        global $servername, $username, $password, $dbname;
        $count = 0;
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            return 0;
        }
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $count = $row['total'];
        }
        $conn->close();
        return $count;
    }

    function getActiveNotifications() {
        global $servername, $username, $password, $dbname;
        $notifications = array();
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            return $notifications;
        }

        $cleanupSql = "DELETE FROM notifications WHERE 
                     (availability = '1minute' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE)) OR
                     (availability = '1day' AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)) OR
                     (availability = '1week' AND created_at < DATE_SUB(NOW(), INTERVAL 1 WEEK)) OR
                     (availability = '2weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 2 WEEK)) OR
                     (availability = '3weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 3 WEEK)) OR
                     (availability = '1month' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $conn->query($cleanupSql);

        $sql = "SELECT *, TIMESTAMPDIFF(SECOND, NOW(), 
                CASE availability
                    WHEN '1minute' THEN DATE_ADD(created_at, INTERVAL 1 MINUTE)
                    WHEN '1day' THEN DATE_ADD(created_at, INTERVAL 1 DAY)
                    WHEN '1week' THEN DATE_ADD(created_at, INTERVAL 1 WEEK)
                    WHEN '2weeks' THEN DATE_ADD(created_at, INTERVAL 2 WEEK)
                    WHEN '3weeks' THEN DATE_ADD(created_at, INTERVAL 3 WEEK)
                    WHEN '1month' THEN DATE_ADD(created_at, INTERVAL 1 MONTH)
                END) as remaining_seconds 
                FROM notifications 
                HAVING remaining_seconds > 0";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $notifications[] = $row;
            }
        }
        $conn->close();
        return $notifications;
    }

    $totalBooks = countBooks();
    $totalUsers = countUsers();
    $activeNotifications = getActiveNotifications();
    $notificationCount = count($activeNotifications);
    ?>

    <header class="header" id="header">
        <a href="" class="logo">
            <img src="image/logo.jpeg" alt="Online Bookstore Logo" class="logo-img">
            ARU ONLINE BOOKSTORE
        </a>
        <i class='bx bx-menu' id="menu-icon"></i>
        <nav class="navbar">
            <a href="#home" class="active">Home <i class='bx bx-home'></i></a>
            <a href="#stats">ABOUT US <i class='bx bx-info-circle'></i></a>
            <a href="#services">USER HELP <i class='bx bx-help-circle'></i></a>
            <div style="position:relative;display:inline-block;">
                <a href="notifications.php">NOTIFICATION <i class='bx bx-bell'></i>
                    <span id="notification-badge" class="notification-badge" style="<?php echo $notificationCount > 0 ? '' : 'display:none;' ?>"><?php echo $notificationCount; ?></span>
                </a>
            </div>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">LOGIN <i class='bx bx-chevron-down'></i></a>
                <div class="dropdown-menu">
                    <a href="admin dashboard.php">ADMIN</a>
                    <a href="librarian.php">LIBRARIAN</a>
                    <a href="user.php">USER</a>
                </div>
            </div>
            <div class="theme-switcher">
                <a href="#setting" id="settings-toggle"><i class='bx bx-cog'></i></a>
                <div class="theme-options" id="theme-options">
                    <div class="theme-option" data-theme="default">Default</div>
                    <div class="theme-option" data-theme="pink">Pink</div>
                    <div class="theme-option" data-theme="red">Red</div>
                    <div class="theme-option" data-theme="dark">Dark</div>
                    <div class="theme-option" data-theme="light">Light</div>
                </div>
            </div>
            <a href="#footer" title="Go to Footer">
                <i class='bx bx-down-arrow-alt' style="font-size: 30px; color: aqua;"></i>
            </a>
        </nav>
    </header>

    <section class="home" id="home">
        <div class="home-content">
            <h1>Welcome to ARSI University <span>Online Bookstore</span></h1>
        </div>
        <div class="home-img">
            <img src="image/logo1.jpeg" alt="Bookstore Image">
        </div>
    </section>

    <section class="stats" id="stats">
        <h2 class="heading">System <span>Statistics</span></h2>
        <div class="stats-container">
            <div class="stats-box">
                <i class='bx bx-user'></i>
                <h3>Total Users</h3>
                <p class="counter" data-target="<?php echo $totalUsers; ?>">0</p>
            </div>
            <div class="stats-box">
                <i class='bx bx-book'></i>
                <h3>Total Books</h3>
                <p class="counter" data-target="<?php echo $totalBooks; ?>">0</p>
            </div>
        </div>
    </section>

    <section class="services" id="services">
        <h2 class="heading">User<span>Access Rule & Principle</span></h2>
        <div class="services-container">
            <div class="services-box">
                <i class='bx bx-user-check'></i>
                <h3>Who Can use?</h3>
                <p>This online bookstore is developed for ARSI University governmental organization! Only those who have access permission granted by ARSI University system responsibility taker leaders can use this online bookstore system. Examples include students, teachers, librarians, and other authorized personnel.</p>
            </div>
            <div class="services-box">
                <i class='bx bx-user-plus'></i>
                <h3>How can use?</h3>
                <p>To use this online bookstore system, you need to register at the ARSI University library office or system admin office. When you go to register, do not forget your university ID. After registration, you will receive a username and password for login authentication. After that, you will gain access permission.</p>
            </div>
            <div class="services-box">
                <i class='bx bx-time-five'></i>
                <h3>When can use?</h3>
                <p>all permissions are decided by the University leader, system admin, or librarian. As per university rules, access to the online bookstore is granted only while you are enrolled in an active university program. If you are dismissed from the university records, you lose your access permission, or if you graduate.</p>
            </div>
        </div>
    </section>

    <section class="portfolio" id="notification">
        <h2 class="heading">Latest <span> Upload Book</span></h2>
        <div class="portfolio-container">
            <?php
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                echo '<p style="color: #fff; font-size: 1.6rem; text-align: center;">Database connection failed.</p>';
            } else {
                $cleanupSql = "DELETE FROM notifications WHERE 
                             (availability = '1minute' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MINUTE)) OR
                             (availability = '1day' AND created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)) OR
                             (availability = '1week' AND created_at < DATE_SUB(NOW(), INTERVAL 1 WEEK)) OR
                             (availability = '2weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 2 WEEK)) OR
                             (availability = '3weeks' AND created_at < DATE_SUB(NOW(), INTERVAL 3 WEEK)) OR
                             (availability = '1month' AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH))";
                $conn->query($cleanupSql);

                $sql = "SELECT cover FROM books ORDER BY date DESC LIMIT 6";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="portfolio-box">';
                        echo '<img src="/bookstore/book/' . htmlspecialchars($row['cover']) . '" alt="Book Cover">';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="color: #fff; font-size: 1.6rem; text-align: center;">No books found.</p>';
                }
                $conn->close();
            }
            ?>
        </div>
    </section>

    <footer class="footer" id="footer">
        <div class="footer-divider"></div>
        <div class="footer-content">
            <div class="footer-social">
                <h3>Follow Us</h3>
                <a href="https://t.me/ArsiUniversityYuunvarsiitiiArsii" target="_blank" title="Telegram"><i class='bx bxl-telegram'></i></a>
                <a href="https://web.facebook.com/arsiuniversityofficial?_rdc=1&_rdr#" target="_blank" title="Facebook"><i class='bx bxl-facebook'></i></a>
                <a href="https://www.youtube.com/@arsiuniversity" target="_blank" title="YouTube"><i class='bx bxl-youtube'></i></a>
                <a href="https://x.com/UniversityArsi" target="_blank" title="Twitter"><i class='bx bxl-twitter'></i></a>
                <a href="https://arsiun.edu.et/" target="_blank" title="Website"><i class='bx bx-world'></i></a>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p><i class='bx bx-mail-send'></i> <a href="mailto:info@arsiuniversity.edu">pr.arsiu@arsiun.edu.et</a></p>
                <p><i class='bx bx-phone'></i> <a href="tel:+1234567890">+251222380252</a></p>
            </div>
            <div class="footer-iconTop">
                <a href="#home" title="Back to Top">
                    <i class='bx bx-up-arrow-alt'></i>
                </a>
            </div>
        </div>
        <div class="footer-text">
            <p>Copyright ©2025 All Rights Reserved by ARSI University.</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuIcon = document.getElementById('menu-icon');
        const navbar = document.querySelector('.navbar');
        const dropdowns = document.querySelectorAll('.dropdown');
        const themeSwitcher = document.querySelector('.theme-switcher');
        const themeOptions = document.getElementById('theme-options');
        const settingsToggle = document.getElementById('settings-toggle');
        
        menuIcon.addEventListener('click', function() {
            navbar.classList.toggle('active');
            menuIcon.classList.toggle('bx-x');
        });
        
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            });
        });
        
        settingsToggle.addEventListener('click', function(e) {
            e.preventDefault();
            themeOptions.style.display = themeOptions.style.display === 'block' ? 'none' : 'block';
        });
        
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', function() {
                const theme = this.getAttribute('data-theme');
                document.body.className = theme;
                localStorage.setItem('bookstoreTheme', theme);
                themeOptions.style.display = 'none';
            });
        });
        
        const savedTheme = localStorage.getItem('bookstoreTheme');
        if (savedTheme) {
            document.body.className = savedTheme;
        }
        
        document.addEventListener('click', function(e) {
            if (!themeSwitcher.contains(e.target) && themeOptions.style.display === 'block') {
                themeOptions.style.display = 'none';
            }
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        });
        
        const animateCounters = () => {
            const counters = document.querySelectorAll('.counter');
            const speed = 200;
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const increment = target / speed;
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        };
        
        animateCounters();
        
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section');
            const navLinks = document.querySelectorAll('.navbar a');
            const header = document.querySelector('header');
            sections.forEach(sec => {
                const top = window.scrollY;
                const offset = sec.offsetTop - 150;
                const height = sec.offsetHeight;
                const id = sec.getAttribute('id');
                if (top >= offset && top < offset + height) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href').includes(id)) {
                            link.classList.add('active');
                        }
                    });
                }
            });
            header.classList.toggle('sticky', window.scrollY > 100);
        });
        
        ScrollReveal().reveal('.home-content, .heading', {origin: 'top', distance: '80px', duration: 2000, delay: 200});
        ScrollReveal().reveal('.home-img, .services-container, .portfolio-container, .stats-container', {origin: 'bottom', distance: '80px', duration: 2000, delay: 200});
        
        function updateNotificationCount() {
            fetch('?notification_count=1')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.style.display = 'block';
                        badge.textContent = data.count;
                        
                        data.notifications.forEach(notification => {
                            startCountdown(notification.id, notification.remaining_seconds);
                        });
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error updating notification count:', error);
                });
        }

        function startCountdown(notificationId, seconds) {
            const countdownElement = document.getElementById('countdown-' + notificationId);
            if (!countdownElement) return;

            let remaining = seconds;
            
            const interval = setInterval(() => {
                remaining--;
                
                if (remaining <= 0) {
                    clearInterval(interval);
                    countdownElement.textContent = "Expired";
                    updateNotificationCount();
                    return;
                }
                
                const hours = Math.floor(remaining / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const secs = remaining % 60;
                
                let timeString = '';
                if (hours > 0) timeString += hours + 'h ';
                if (minutes > 0) timeString += minutes + 'm ';
                timeString += secs + 's';
                
                countdownElement.textContent = timeString;
            }, 1000);
        }

        updateNotificationCount();
        setInterval(updateNotificationCount, 30000);
    });
    </script>

    <?php
    if (isset($_GET['notification_count'])) {
        $activeNotifications = getActiveNotifications();
        $notificationCount = count($activeNotifications);
        
        $response = array(
            'count' => $notificationCount,
            'notifications' => array()
        );
        
        foreach ($activeNotifications as $notification) {
            $response['notifications'][] = array(
                'id' => $notification['id'],
                'remaining_seconds' => $notification['remaining_seconds']
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
    ?>
</body>
</html>