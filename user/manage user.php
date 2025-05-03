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
            
            // Fetch the profile image path before deletion
            $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
            
            // Delete the user record from the database
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            
            // If database deletion is successful, delete the profile image file
            if ($success && !empty($user['profile_image'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/bookstore/book/" . $user['profile_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the file
                }
            }
            
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $date = $_POST['date'];
            $academicYear = $_POST['academicYear'];
            $fullName = $_POST['fullName'];
            $idNumber = $_POST['idNumber'];
            $department = $_POST['department'];
            $year = $_POST['year'];
            $semester = $_POST['semester'];
            $phone = $_POST['phone'];
            $username = $_POST['username'];
            $rememberMe = $_POST['rememberMe'];
            $accessPermission = $_POST['accessPermission'];

            $stmt = $conn->prepare("UPDATE users SET date = ?, academic_year = ?, full_name = ?, id_number = ?, department = ?, year = ?, semester = ?, phone = ?, username = ?, remember_me = ?, access_permission = ? WHERE id = ?");
            $stmt->bind_param("sssssssssssi", $date, $academicYear, $fullName, $idNumber, $department, $year, $semester, $phone, $username, $rememberMe, $accessPermission, $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        }
    }
    $conn->close();
    exit;
}

$sql = "SELECT id, date, academic_year, full_name, id_number, department, year, semester, phone, username, remember_me, access_permission, profile_image FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/manage user.css">
    <link rel="stylesheet" href="../css/themes.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Manage Users</h1>
        </div>
        <div class="search-container">
            <div class="search-group">
                <label for="searchDepartment">Department yapılacak
                <input type="text" id="searchDepartment" placeholder="Enter department" oninput="filterUsers()">
            </div>
            <div class="search-group">
                <label for="searchAcademicYear">Academic Year:</label>
                <input type="text" id="searchAcademicYear" placeholder="Enter academic year" oninput="filterUsers()">
            </div>
        </div>
        <div class="user-grid" id="userGrid">
            <?php foreach ($users as $user): ?>
                <div class="user-card" data-id="<?php echo $user['id']; ?>">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="/bookstore/book/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-img">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/140" alt="Default Profile" class="profile-img">
                    <?php endif; ?>
                    <div class="user-info">
                        <span>Date:</span> <?php echo htmlspecialchars($user['date']); ?>
                    </div>
                    <div class="user-info">
                        <span>Academic Year:</span> <?php echo htmlspecialchars($user['academic_year']); ?>
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
                        <span>Year:</span> <?php echo htmlspecialchars($user['year']); ?>
                    </div>
                    <div class="user-info">
                        <span>Semester:</span> <?php echo htmlspecialchars($user['semester']); ?>
                    </div>
                    <div class="user-info">
                        <span>Phone:</span> <?php echo htmlspecialchars($user['phone']); ?>
                    </div>
                    <div class="user-info">
                        <span>Username:</span> <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    <div class="user-info">
                        <span>Remember Me:</span> <?php echo htmlspecialchars($user['remember_me'] ?: 'Not set'); ?>
                    </div>
                    <div class="user-info">
                        <span>Access Permission:</span> <?php echo htmlspecialchars($user['access_permission']); ?>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-edit" onclick="editUser(<?php echo $user['id']; ?>)">Edit</button>
                        <button class="btn btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                        <button class="btn btn-print" onclick="printUser(<?php echo $user['id']; ?>)">Print</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Edit User</h2>
            <form id="editForm">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editDate">Date</label>
                    <input type="date" id="editDate" name="date" required>
                </div>
                <div class="form-group">
                    <label for="editAcademicYear">Academic Year</label>
                    <input type="text" id="editAcademicYear" name="academicYear" required>
                </div>
                <div class="form-group">
                    <label for="editFullName">Full Name</label>
                    <input type="text" id="editFullName" name="fullName" required>
                </div>
                <div class="form-group">
                    <label for="editIdNumber">ID Number</label>
                    <input type="text" id="editIdNumber" name="idNumber" required>
                </div>
                <div class="form-group">
                    <label for="editDepartment">Department</label>
                    <input type="text" id="editDepartment" name="department" required>
                </div>
                <div class="form-group">
                    <label for="editYear">Year</label>
                    <select id="editYear" name="year" required>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                        <option value="6">6th Year</option>
                        <option value="7">7th Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editSemester">Semester</label>
                    <select id="editSemester" name="semester" required>
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                    </select>
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
                <div class="form-group">
                    <label for="editAccessPermission">Access Permission</label>
                    <select id="editAccessPermission" name="accessPermission" required>
                        <option value="0.25">1 Week</option>
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
                <button type="submit" class="btn btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function filterUsers() {
            const searchDepartment = document.getElementById('searchDepartment').value.toLowerCase();
            const searchAcademicYear = document.getElementById('searchAcademicYear').value.toLowerCase();
            const cards = document.querySelectorAll('.user-card');

            cards.forEach(card => {
                const department = card.querySelector('.user-info:nth-child(6)').textContent.toLowerCase().replace('department: ', '');
                const academicYear = card.querySelector('.user-info:nth-child(3)').textContent.toLowerCase().replace('academic year: ', '');

                const matchesDepartment = searchDepartment === '' || department.includes(searchDepartment);
                const matchesAcademicYear = searchAcademicYear === '' || academicYear.includes(searchAcademicYear);

                if (matchesDepartment && matchesAcademicYear) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function editUser(id) {
            const card = document.querySelector(`.user-card[data-id="${id}"]`);
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');

            document.getElementById('editId').value = id;
            document.getElementById('editDate').value = card.querySelector('.user-info:nth-child(2)').textContent.replace('Date: ', '');
            document.getElementById('editAcademicYear').value = card.querySelector('.user-info:nth-child(3)').textContent.replace('Academic Year: ', '');
            document.getElementById('editFullName').value = card.querySelector('.user-info:nth-child(4)').textContent.replace('Full Name: ', '');
            document.getElementById('editIdNumber').value = card.querySelector('.user-info:nth-child(5)').textContent.replace('ID Number: ', '');
            document.getElementById('editDepartment').value = card.querySelector('.user-info:nth-child(6)').textContent.replace('Department: ', '');
            document.getElementById('editYear').value = card.querySelector('.user-info:nth-child(7)').textContent.replace('Year: ', '');
            document.getElementById('editSemester').value = card.querySelector('.user-info:nth-child(8)').textContent.replace('Semester: ', '');
            document.getElementById('editPhone').value = card.querySelector('.user-info:nth-child(9)').textContent.replace('Phone: ', '');
            document.getElementById('editUsername').value = card.querySelector('.user-info:nth-child(10)').textContent.replace('Username: ', '');
            document.getElementById('editRememberMe').value = card.querySelector('.user-info:nth-child(11)').textContent.replace('Remember Me: ', '') === 'Not set' ? '' : card.querySelector('.user-info:nth-child(11)').textContent.replace('Remember Me: ', '');
            document.getElementById('editAccessPermission').value = card.querySelector('.user-info:nth-child(12)').textContent.replace('Access Permission: ', '').replace(/ Months?/, '').replace('1 Week', '0.25');

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
                        const accessPermission = formData.get('accessPermission');
                        const permissionText = accessPermission === '0.25' ? '1 Week' : `${accessPermission} Month${accessPermission > 1 ? 's' : ''}`;
                        
                        card.querySelector('.user-info:nth-child(2)').textContent = `Date: ${formData.get('date')}`;
                        card.querySelector('.user-info:nth-child(3)').textContent = `Academic Year: ${formData.get('academicYear')}`;
                        card.querySelector('.user-info:nth-child(4)').textContent = `Full Name: ${formData.get('fullName')}`;
                        card.querySelector('.user-info:nth-child(5)').textContent = `ID Number: ${formData.get('idNumber')}`;
                        card.querySelector('.user-info:nth-child(6)').textContent = `Department: ${formData.get('department')}`;
                        card.querySelector('.user-info:nth-child(7)').textContent = `Year: ${formData.get('year')}`;
                        card.querySelector('.user-info:nth-child(8)').textContent = `Semester: ${formData.get('semester')}`;
                        card.querySelector('.user-info:nth-child(9)').textContent = `Phone: ${formData.get('phone')}`;
                        card.querySelector('.user-info:nth-child(10)').textContent = `Username: ${formData.get('username')}`;
                        card.querySelector('.user-info:nth-child(11)').textContent = `Remember Me: ${formData.get('rememberMe') || 'Not set'}`;
                        card.querySelector('.user-info:nth-child(12)').textContent = `Access Permission: ${permissionText}`;
                        closeModal();
                        alert('User updated successfully!');
                        filterUsers();
                    } else {
                        alert('Error updating user: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the user.');
                });
            };
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user? This will permanently remove all user data and their profile image.')) {
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
                        document.querySelector(`.user-card[data-id="${id}"]`).remove();
                        alert('User and associated data deleted successfully!');
                    } else {
                        alert('Error deleting user: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the user.');
                });
            }
        }

        function printUser(id) {
            const card = document.querySelector(`.user-card[data-id="${id}"]`).cloneNode(true);
            card.querySelector('.button-group').remove();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>User Details</title>
                        <link rel="stylesheet" href="../css/manage user.css">
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