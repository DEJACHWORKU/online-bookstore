<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINE BOOKSTORE SYSTEM</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
</head>

<body>
    <header class="header" id="header">
        <a href="" class="logo">
            <i class='bx bxs-book' style="color: #61e5ff;"></i>
            BOOKSTORE
        </a>
        <i class='bx bx-menu' id="menu-icon"></i>
        <nav class="navbar">
            <a href="#home" class="active">Home <i class='bx bx-home'></i></a>
            <a href="#stats">ABOUT US <i class='bx bx-info-circle'></i></a>
            <a href="#services">USER HELP <i class='bx bx-help-circle'></i></a>
            <a href="#notification" id="notification-link">NOTIFICATION <i class='bx bx-bell'></i></a>
            <div class="dropdown">
                <a href="#" class="dropdown-toggle">LOGIN <i class='bx bx-chevron-down'></i></a>
                <div class="dropdown-menu">
                    <a href="admin_login.php">ADMIN</a>
                    <a href="librarian_login.php">LIBRARIAN</a>
                    <a href="book/index.php">USER</a>
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
                <p class="counter" data-target="1500">0</p>
            </div>
            <div class="stats-box">
                <i class='bx bx-book'></i>
                <h3>Total Books</h3>
                <p class="counter" data-target="5000">0</p>
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
                <p>Regarding your question of when you can use the system, all permissions are decided by the University leader, system admin, or librarian. As per university rules, access to the online bookstore is granted only while you are enrolled in an active university program. If you are dismissed from the university records, you lose your access permission, or if you graduate, your access will also be revoked.</p>
            </div>
        </div>
    </section>

    <section class="portfolio" id="#notification">
        <h2 class="heading">Latest six <span> Upload Book</span></h2>
        <div class="portfolio-container">
            <div class="portfolio-box">
                <img src="image/TRAVEL.png" alt="">
            </div>
            <div class="portfolio-box">
                <img src="image/CAR_RENTAL_.png" alt="">
            </div>
            <div class="portfolio-box">
                <img src="image/MOVIES_.png" alt="">
            </div>
            <div class="portfolio-box">
                <img src="image/D'LIFE_.png" alt="">
            </div>
            <div class="portfolio-box">
                <img src="image/PORTFOLIO_.png" alt="">
            </div>
            <div class="portfolio-box">
                <img src="image/G-FOOD_.png" alt="">
            </div>
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
                <a href="" title="Back to Top">
                    <i class='bx bx-up-arrow-alt' id="home"></i>
                </a>
            </div>
        </div>
        <div class="footer-text">
            <p>Copyright ©2025 All Rights Reserved by ARSI University.</p>
        </div>
    </footer>

    <!-- Notification Popup -->
    <div id="notification-popup" class="notification-popup">
        <div class="notification-content">
            <i class='bx bx-bell-ring notification-icon'></i>
            <h2>Notification Alert</h2>
            <p>Do you want to see detailed new notifications?</p>
            <div class="notification-buttons">
                <button id="yes-btn" class="btn-yes">YES</button>
                <button id="cancel-btn" class="btn-cancel">CANCEL</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>