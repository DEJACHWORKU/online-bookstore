<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            $stmt = $conn->prepare("SELECT profile_image FROM Admin WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();
            $stmt->close();
            
            $stmt = $conn->prepare("DELETE FROM Admin WHERE id = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            
            if ($success && !empty($admin['profile_image'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/bookstore/book/Admin/" . $admin['profile_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $fullName = $_POST['fullName'];
            $adminID = $_POST['adminID'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $username = $_POST['username'];
            $rememberMe = $_POST['rememberMe'];

            $stmt = $conn->prepare("UPDATE Admin SET full_name = ?, admin_id = ?, email = ?, phone = ?, username = ?, remember_me = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $fullName, $adminID, $email, $phone, $username, $rememberMe, $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        }
    }
    $conn->close();
    exit;
}

$sql = "SELECT id, full_name, admin_id, email, phone, username, profile_image, remember_me FROM Admin";
$result = $conn->query($sql);

$admins = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/manage admin.css">
    <link rel="stylesheet" href="../css/themes.css">
</head>
<body>
  
    <div class="container">
        <div class="header">
            <h1>Manage Admins full information</h1>
        </div>
        <div class="admin-grid" id="adminGrid">
            <?php foreach ($admins as $admin): ?>
                <div class="admin-card" data-id="<?php echo $admin['id']; ?>">
                    <?php if (!empty($admin['profile_image'])): ?>
                        <img src="/bookstore/book/Admin/<?php echo htmlspecialchars($admin['profile_image']); ?>" 
                             alt="Profile" class="profile-img">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/120" alt="Default Profile" class="profile-img">
                    <?php endif; ?>
                    <div class="admin-info">
                        <span>Full Name:</span> <?php echo htmlspecialchars($admin['full_name']); ?>
                    </div>
                    <div class="admin-info">
                        <span>Admin ID:</span> <?php echo htmlspecialchars($admin['admin_id']); ?>
                    </div>
                    <div class="admin-info">
                        <span>Email:</span> <?php echo htmlspecialchars($admin['email']); ?>
                    </div>
                    <div class="admin-info">
                        <span>Phone:</span> <?php echo htmlspecialchars($admin['phone']); ?>
                    </div>
                    <div class="admin-info">
                        <span>Username:</span> <?php echo htmlspecialchars($admin['username']); ?>
                    </div>
                    <div class="admin-info">
                        <span>Remember Me:</span> <?php echo htmlspecialchars($admin['remember_me'] ?: 'Not set'); ?>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-edit" onclick="editAdmin(<?php echo $admin['id']; ?>)">Edit</button>
                        <button class="btn btn-delete" onclick="deleteAdmin(<?php echo $admin['id']; ?>)">Delete</button>
                        <button class="btn btn-print" onclick="printAdmin(<?php echo $admin['id']; ?>)">Print</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Edit Admin</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editFullName">Full Name</label>
                    <input type="text" id="editFullName" name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="editAdminID">Admin ID</label>
                    <input type="text" id="editAdminID" name="adminID" required>
                </div>
                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" name="email" required>
                </div>
                <div class="form-group">
                    <label for="editPhone">Phone</label>
                    <input type="tel" id="editPhone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" id="editUsername" name="username" required>
                </div>
                <div class="form-group">
                    <label for="editRememberMe">Remember Me</label>
                    <input type="text" id="editRememberMe" name="rememberMe">
                </div>
                <button type="submit" class="btn btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme initialization
            const savedTheme = localStorage.getItem('bookstoreTheme');
            if (savedTheme) {
                document.body.className = savedTheme;
            }

            // Theme switcher toggle
            const settingsToggle = document.querySelector('.settings-btn');
            const themeOptions = document.querySelector('.theme-options');
            if (settingsToggle && themeOptions) {
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

                document.addEventListener('click', function(e) {
                    if (!settingsToggle.contains(e.target) && !themeOptions.contains(e.target)) {
                        themeOptions.style.display = 'none';
                    }
                });
            }
        });

        function editAdmin(id) {
            const card = document.querySelector(`.admin-card[data-id="${id}"]`);
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            document.getElementById('editId').value = id;
            document.getElementById('editFullName').value = card.querySelector('.admin-info:nth-child(2)').textContent.replace('Full Name: ', '');
            document.getElementById('editAdminID').value = card.querySelector('.admin-info:nth-child(3)').textContent.replace('Admin ID: ', '');
            document.getElementById('editEmail').value = card.querySelector('.admin-info:nth-child(4)').textContent.replace('Email: ', '');
            document.getElementById('editPhone').value = card.querySelector('.admin-info:nth-child(5)').textContent.replace('Phone: ', '');
            document.getElementById('editUsername').value = card.querySelector('.admin-info:nth-child(6)').textContent.replace('Username: ', '');
            document.getElementById('editRememberMe').value = card.querySelector('.admin-info:nth-child(7)').textContent.replace('Remember Me: ', '') === 'Not set' ? '' : card.querySelector('.admin-info:nth-child(7)').textContent.replace('Remember Me: ', '');

            modal.style.display = 'flex';

            form.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'update');

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        card.querySelector('.admin-info:nth-child(2)').textContent = `Full Name: ${formData.get('fullName')}`;
                        card.querySelector('.admin-info:nth-child(3)').textContent = `Admin ID: ${formData.get('adminID')}`;
                        card.querySelector('.admin-info:nth-child(4)').textContent = `Email: ${formData.get('email')}`;
                        card.querySelector('.admin-info:nth-child(5)').textContent = `Phone: ${formData.get('phone')}`;
                        card.querySelector('.admin-info:nth-child(6)').textContent = `Username: ${formData.get('username')}`;
                        card.querySelector('.admin-info:nth-child(7)').textContent = `Remember Me: ${formData.get('rememberMe') || 'Not set'}`;
                        closeModal();
                        alert('Admin updated successfully!');
                    } else {
                        alert('Error updating admin: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the admin.');
                });
            };
        }

        function deleteAdmin(id) {
            if (confirm('Are you sure you want to delete this admin?')) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`.admin-card[data-id="${id}"]`).remove();
                        alert('Admin deleted successfully!');
                    } else {
                        alert('Error deleting admin: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the admin.');
                });
            }
        }

        function printAdmin(id) {
            const card = document.querySelector(`.admin-card[data-id="${id}"]`).cloneNode(true);
            card.querySelector('.button-group').remove();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Admin Details</title>
                        <link rel="stylesheet" href="../css/manage admin.css">
                    </head>
                    <body>
                        ${card.outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>