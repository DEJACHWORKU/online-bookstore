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
            $stmt = $conn->prepare("DELETE FROM Librarian WHERE id = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $fullName = $_POST['fullName'];
            $personalID = $_POST['personalID'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $username = $_POST['username'];
            $rememberMe = $_POST['rememberMe'];

            $stmt = $conn->prepare("UPDATE Librarian SET full_name = ?, personal_id = ?, email = ?, phone = ?, username = ?, remember_me = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $fullName, $personalID, $email, $phone, $username, $rememberMe, $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        }
    }
    $conn->close();
    exit;
}

$sql = "SELECT id, full_name, personal_id, email, phone, username, profile_image, remember_me FROM Librarian";
$result = $conn->query($sql);

$librarians = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $librarians[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Librarians</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/manage librarian.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Librarians</h1>
            <div class="header-buttons">
                <button class="header-btn" onclick="goBack()">Go Back</button>
                <button class="header-btn btn-logout" onclick="logout()">Logout</button>
            </div>
        </div>
        <div class="librarian-grid" id="librarianGrid">
            <?php foreach ($librarians as $librarian): ?>
                <div class="librarian-card" data-id="<?php echo $librarian['id']; ?>">
                    <?php if (!empty($librarian['profile_image'])): ?>
                        <img src="/bookstore/book/Librarian/<?php echo htmlspecialchars($librarian['profile_image']); ?>" 
                             alt="Profile" class="profile-img">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/120" alt="Default Profile" class="profile-img">
                    <?php endif; ?>
                    <div class="librarian-info">
                        <span>Full Name:</span> <?php echo htmlspecialchars($librarian['full_name']); ?>
                    </div>
                    <div class="librarian-info">
                        <span>Personal ID:</span> <?php echo htmlspecialchars($librarian['personal_id']); ?>
                    </div>
                    <div class="librarian-info">
                        <span>Email:</span> <?php echo htmlspecialchars($librarian['email']); ?>
                    </div>
                    <div class="librarian-info">
                        <span>Phone:</span> <?php echo htmlspecialchars($librarian['phone']); ?>
                    </div>
                    <div class="librarian-info">
                        <span>Username:</span> <?php echo htmlspecialchars($librarian['username']); ?>
                    </div>
                    <div class="librarian-info">
                        <span>Remember Me:</span> <?php echo htmlspecialchars($librarian['remember_me'] ?: 'Not set'); ?>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-edit" onclick="editLibrarian(<?php echo $librarian['id']; ?>)">Edit</button>
                        <button class="btn btn-delete" onclick="deleteLibrarian(<?php echo $librarian['id']; ?>)">Delete</button>
                        <button class="btn btn-print" onclick="printLibrarian(<?php echo $librarian['id']; ?>)">Print</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Edit Librarian</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editFullName">Full Name</label>
                    <input type="text" id="editFullName" name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="editPersonalID">Personal ID</label>
                    <input type="text" id="editPersonalID" name="personalID" required>
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
                    <input typeAuctioneer="text" id="editUsername" name="username" required>
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
        function editLibrarian(id) {
            const card = document.querySelector(`.librarian-card[data-id="${id}"]`);
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            document.getElementById('editId').value = id;
            document.getElementById('editFullName').value = card.querySelector('.librarian-info:nth-child(2)').textContent.replace('Full Name: ', '');
            document.getElementById('editPersonalID').value = card.querySelector('.librarian-info:nth-child(3)').textContent.replace('Personal ID: ', '');
            document.getElementById('editEmail').value = card.querySelector('.librarian-info:nth-child(4)').textContent.replace('Email: ', '');
            document.getElementById('editPhone').value = card.querySelector('.librarian-info:nth-child(5)').textContent.replace('Phone: ', '');
            document.getElementById('editUsername').value = card.querySelector('.librarian-info:nth-child(6)').textContent.replace('Username: ', '');
            document.getElementById('editRememberMe').value = card.querySelector('.librarian-info:nth-child(7)').textContent.replace('Remember Me: ', '') === 'Not set' ? '' : card.querySelector('.librarian-info:nth-child(7)').textContent.replace('Remember Me: ', '');

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
                        card.querySelector('.librarian-info:nth-child(2)').textContent = `Full Name: ${formData.get('fullName')}`;
                        card.querySelector('.librarian-info:nth-child(3)').textContent = `Personal ID: ${formData.get('personalID')}`;
                        card.querySelector('.librarian-info:nth-child(4)').textContent = `Email: ${formData.get('email')}`;
                        card.querySelector('.librarian-info:nth-child(5)').textContent = `Phone: ${formData.get('phone')}`;
                        card.querySelector('.librarian-info:nth-child(6)').textContent = `Username: ${formData.get('username')}`;
                        card.querySelector('.librarian-info:nth-child(7)').textContent = `Remember Me: ${formData.get('rememberMe') || 'Not set'}`;
                        closeModal();
                        alert('Librarian updated successfully!');
                    } else {
                        alert('Error updating librarian: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the librarian.');
                });
            };
        }

        function deleteLibrarian(id) {
            if (confirm('Are you sure you want to delete this librarian?')) {
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
                        document.querySelector(`.librarian-card[data-id="${id}"]`).remove();
                        alert('Librarian deleted successfully!');
                    } else {
                        alert('Error deleting librarian: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the librarian.');
                });
            }
        }

        function printLibrarian(id) {
            const card = document.querySelector(`.librarian-card[data-id="${id}"]`).cloneNode(true);
            card.querySelector('.button-group').remove();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Librarian Details</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 15px; margin: 0; background: #fff; }
                            .librarian-card {
                                background: #fff;
                                border-radius: 15px;
                                border: 1px solid #dfe6e9;
                                padding: 15px;
                                height: 400px;
                                width: 260px;
                                display: flex;
                                flex-direction: column;
                                justify-content: space-between;
                                overflow: hidden;
                            }
                            .profile-img {
                                width: 120px;
                                height: 120px;
                                border-radius: 50%;
                                object-fit: cover;
                                object-position: center;
                                margin: 0 auto 10px;
                                border: 3px solid #3498db;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                display: block;
                            }
                            .librarian-info {
                                margin: 6px 0;
                                font-size: 16px;
                                line-height: 1.3;
                                color: #2d3436;
                            }
                            .librarian-info span {
                                font-weight: bold;
                                color: #2980b9;
                                margin-right: 5px;
                            }
                        </style>
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

        function goBack() {
            window.history.back();
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                alert('Logging out...');
            }
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