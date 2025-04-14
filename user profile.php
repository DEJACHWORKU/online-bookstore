<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login1.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_Db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id_number, academic_year, year, semester, phone, username, date, access_permission, full_name, department, profile_image, remember_me FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$profile_image = !empty($user['profile_image']) ? '/bookstore/book/' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/200';

$remember_me = 'Not set';
if (isset($user['remember_me'])) {
    $rm_value = strtolower(trim($user['remember_me']));
    if ($rm_value === '1' || $rm_value === 'yes' || $rm_value === 'true') {
        $remember_me = 'Yes';
    } elseif ($rm_value === '0' || $rm_value === 'no' || $rm_value === 'false') {
        $remember_me = 'No';
    } else {
        $remember_me = htmlspecialchars($user['remember_me']);
    }
}

$access_permission = floatval($user['access_permission']);
if ($access_permission == 0.25) {
    $access_display = '1 Week';
} else {
    $access_display = number_format($access_permission, 0) . ' Month' . ($access_permission == 1 ? '' : 's');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --print-color: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .profile-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 2fr;
            min-height: 500px;
        }
        
        .profile-sidebar {
            background: var(--primary-color);
            color: white;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: var(--transition);
        }
        
        .profile-img:hover {
            transform: scale(1.05);
        }
        
        .user-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .user-title {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 20px;
        }
        
        .profile-main {
            padding: 40px;
        }
        
        .section-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .info-value {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            border-left: 3px solid var(--accent-color);
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-print {
            background: var(--print-color);
        }
        
        .btn-print:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                min-height: 100vh !important;
            }
            
            .profile-container {
                max-width: 800px !important;
                margin: 0 auto !important;
                padding: 20px !important;
            }
            
            .profile-card {
                box-shadow: none !important;
                grid-template-columns: 1fr 2fr !important;
                min-height: auto !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            
            .btn-group {
                display: none !important;
            }
            
            .profile-sidebar {
                background: var(--primary-color) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .info-value {
                background: var(--light-color) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            @page {
                size: auto;
                margin: 10mm;
            }
        }
        
        @media (max-width: 768px) {
            .profile-card {
                grid-template-columns: 1fr;
            }
            
            .profile-sidebar {
                padding: 20px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            @media print {
                .profile-card {
                    grid-template-columns: 1fr !important;
                }
                
                .profile-sidebar {
                    padding: 20px !important;
                }
                
                .profile-main {
                    padding: 20px !important;
                }
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <?php if ($user): ?>
            <div class="profile-card">
                <div class="profile-sidebar">
                    <img src="<?php echo $profile_image; ?>" alt="User Profile" class="profile-img">
                    <h2 class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="user-title"><?php echo htmlspecialchars($user['department']); ?></p>
                </div>
                
                <div class="profile-main">
                    <div class="section-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Personal Information</span>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">ID Number</span>
                            <div class="info-value"><?php echo htmlspecialchars($user['id_number']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Academic Year</span>
                            <div class="info-value"><?php echo htmlspecialchars($user['academic_year']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Year & Semester</span>
                            <div class="info-value">Year <?php echo htmlspecialchars($user['year']); ?>, Sem <?php echo htmlspecialchars($user['semester']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Phone</span>
                            <div class="info-value"><?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                    </div>
                    
                    <div class="section-title">
                        <i class="fas fa-key"></i>
                        <span>Account Details</span>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Username</span>
                            <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Join Date</span>
                            <div class="info-value"><?php echo htmlspecialchars($user['date']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Remember Me</span>
                            <div class="info-value"><?php echo $remember_me; ?></div>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Access Level</span>
                            <div class="info-value"><?php echo $access_display; ?></div>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <a href="user.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                        <button onclick="window.print()" class="btn btn-print">
                            <i class="fas fa-print"></i>
                            Print Profile
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="profile-card" style="grid-template-columns: 1fr; text-align: center; padding: 40px;">
                <h2 style="color: var(--primary-color); margin-bottom: 20px;">User not found</h2>
                <p style="margin-bottom: 30px;">We couldn't retrieve your profile information.</p>
                <a href="user.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        window.onbeforeprint = function() {
            document.body.style.background = 'white';
            document.querySelectorAll('.profile-sidebar').forEach(el => {
                el.style.backgroundColor = '#4361ee';
            });
            document.querySelectorAll('.info-value').forEach(el => {
                el.style.backgroundColor = '#f8f9fa';
            });
        };
        
        window.onafterprint = function() {
            document.body.style.background = '';
        };
    </script>
</body>
</html>