<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Current date (dynamic for real use)
$current_date = new DateTime();

// Handle POST requests (approve/update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    if (isset($_POST['action']) && $_POST['action'] === 'approve' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $accessPermission = $_POST['accessPermission'];

        $stmt = $conn->prepare("UPDATE users SET access_permission = ? WHERE id = ?");
        $stmt->bind_param("si", $accessPermission, $id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }
}

// Fetch all users
$sql = "SELECT id, date, academic_year, full_name, id_number, department, year, semester, phone, username, remember_me, access_permission, profile_image FROM users";
$result = $conn->query($sql);

$users = [];
$users_to_delete = [];
if ($result === false) {
    echo "Query failed: " . $conn->error;
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Validate date
        if (!DateTime::createFromFormat('Y-m-d', $row['date'])) {
            continue; // Skip invalid dates
        }

        // Calculate expiration date
        $start_date = new DateTime($row['date']);
        $months = (int)$row['access_permission'];
        $expiration_date = clone $start_date;
        $expiration_date->modify("+{$months} months");

        // Calculate days until expiration
        $interval = $current_date->diff($expiration_date);
        $days_remaining = $interval->days * ($interval->invert ? -1 : 1);

        if ($days_remaining < 0) {
            // User has expired
            $users_to_delete[] = $row['id'];
        } elseif ($days_remaining <= 7) {
            // User expires within 7 days
            $row['days_remaining'] = $days_remaining;
            $row['expiration_date'] = $expiration_date->format('Y-m-d');
            $users[] = $row;
        }
    }
}

// Delete expired users
if (!empty($users_to_delete)) {
    $ids = implode(',', array_map('intval', $users_to_delete)); // Sanitize IDs
    $delete_sql = "DELETE FROM users WHERE id IN ($ids)";
    if (!$conn->query($delete_sql)) {
        echo "Delete failed: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Approval</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #2c3e50;
            padding: 15px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            color: #ecf0f1;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 28px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 20px;
        }

        .user-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 480px;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #dfe6e9;
            overflow: hidden;
        }

        .user-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .profile-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: contain;
            margin: 0 auto 15px;
            border: 3px solid #3498db;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background: #f5f5f5;
            display: block;
        }

        .user-info {
            margin: 8px 0;
            font-size: 15px;
            line-height: 1.4;
            color: #2d3436;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .user-info span {
            font-weight: bold;
            color: #2980b9;
            margin-right: 5px;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            justify-content: flex-start;
        }

        .btn {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1 1 auto;
        }

        .btn-approve {
            background: linear-gradient(45deg, #f1c40f, #e67e22);
            color: white;
        }

        .btn:hover {
            opacity: 0.95;
            transform: scale(1.08);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            width: 90%;
            max-width: 550px;
            position: relative;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            max-height: 80vh;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #7f8c8d;
            transition: color 0.3s;
        }

        .close:hover {
            color: #e74c3c;
        }

        .form-group {
            margin: 15px 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #2c3e50;
            font-weight: bold;
        }

        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .btn-save {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            font-size: 16px;
        }

        .empty-message {
            text-align: center;
            font-size: 18px;
            color: #7f8c8d;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .user-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
            .user-card {
                min-height: 460px;
                max-width: 100%;
                padding: 15px;
            }
            .profile-img {
                width: 120px;
                height: 120px;
                margin: 0 auto 10px;
            }
            .user-info {
                font-size: 14px;
            }
            .button-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .user-grid {
                grid-template-columns: 1fr;
            }
            .user-card {
                min-height: 440px;
                max-width: 100%;
                padding: 10px;
            }
            .profile-img {
                width: 100px;
                height: 100px;
            }
            .user-info {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Approval</h1>
        </div>
        <div class="user-grid" id="userGrid">
            <?php if (empty($users)): ?>
                <div class="empty-message">No users require approval at this time.</div>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-card" data-id="<?php echo $user['id']; ?>" data-days-remaining="<?php echo $user['days_remaining']; ?>">
                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="/bookstore/book/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                 alt="Profile" class="profile-img">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/140" alt="Default Profile" class="profile-img">
                        <?php endif; ?>
                        <div class="user-info">
                            <span>Date:</span> <?php echo htmlspecialchars($user['date']); ?>
                        </div>
                        <div class="user-info">
                            <span>Full Name:</span> <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>
                        <div class="user-info">
                            <span>ID Number:</span> <?php echo htmlspecialchars($user['id_number']); ?>
                        </div>
                        <div class="user-info">
                            <span>Department:</span> <?php echo htmlspecialchars($user['department']); ?>
                        </div>
                        <div class="user-info">
                            <span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?>
                        </div>
                        <div class="user-info">
                            <span>Username:</span> <?php echo htmlspecialchars($user['username']); ?>
                        </div>
                        <div class="user-info">
                            <span>Access Permission:</span> <?php echo htmlspecialchars($user['access_permission']); ?> Months
                        </div>
                        <div class="user-info">
                            <span>Expires On:</span> <?php echo htmlspecialchars($user['expiration_date']); ?>
                        </div>
                        <div class="user-info">
                            <span>Days Remaining:</span> <?php echo $user['days_remaining']; ?>
                        </div>
                        <div class="button-group">
                            <button class="btn btn-approve" onclick="approveUser(<?php echo $user['id']; ?>)">Approve</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Approve User</h2>
            <form id="approveForm">
                <input type="hidden" id="approveId" name="id">
                <div class="form-group">
                    <label for="approveAccessPermission">Extend Access Permission</label>
                    <select id="approveAccessPermission" name="accessPermission" required>
                        <option value="1">1 Month</option>
                        <option value="2">2 Months</option>
                        <option value="3">3 Months</option>
                        <option value="4">4 Months</option>
                        <option value="5">5 Months</option>
                        <option value="6">6 Months</option>
                        <option value="7">7 Months</option>
                        <option value="8">8 Months</option>
                        <option value="9">9 Months</option>
                        <option value="10">10 Months</option>
                        <option value="11">11 Months</option>
                        <option value="12">12 Months</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-save">Approve</button>
            </form>
        </div>
    </div>

    <script>
        // Simulate notification for users with 1 week remaining
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.user-card');
            cards.forEach(card => {
                const daysRemaining = parseInt(card.getAttribute('data-days-remaining'));
                if (daysRemaining === 7) {
                    const username = card.querySelector('.user-info:nth-child(6)').textContent.replace('Username: ', '');
                    alert(`Notification: User ${username}'s access expires in 1 week. Please approve or it will be deleted.`);
                    console.log(`Notification sent for user ${username}: Access expires in 1 week.`);
                }
            });
        });

        function approveUser(id) {
            const card = document.querySelector(`.user-card[data-id="${id}"]`);
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');

            document.getElementById('approveId').value = id;

            modal.style.display = 'flex';

            form.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'approve');

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const newAccessPermission = formData.get('accessPermission');
                        const startDate = new Date(card.querySelector('.user-info:nth-child(1)').textContent.replace('Date: ', ''));
                        const expirationDate = new Date(startDate);
                        expirationDate.setMonth(startDate.getMonth() + parseInt(newAccessPermission));
                        
                        // Update card display
                        card.querySelector('.user-info:nth-child(7)').textContent = `Access Permission: ${newAccessPermission} Months`;
                        card.querySelector('.user-info:nth-child(8)').textContent = `Expires On: ${expirationDate.toISOString().split('T')[0]}`;
                        const daysRemaining = Math.ceil((expirationDate - new Date()) / (1000 * 60 * 60 * 24));
                        card.querySelector('.user-info:nth-child(9)').textContent = `Days Remaining: ${daysRemaining}`;
                        
                        // Remove card after a short delay to show update
                        setTimeout(() => card.remove(), 1000);
                        closeModal();
                        alert('User approved successfully!');
                    } else {
                        alert('Error approving user: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the user.');
                });
            };
        }

        function closeModal() {
            document.getElementById('approveModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('approveModal');
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>