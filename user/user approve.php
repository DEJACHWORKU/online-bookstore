<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_book_DB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getExpirationDate($startDate, $accessPermission) {
    if ($accessPermission === 'Approved') {
        return date('Y-m-d', strtotime("+30 days", strtotime($startDate)));
    }
    $parts = explode(' ', $accessPermission);
    $duration = (int)$parts[0];
    $unit = $parts[1];
    $interval = ($unit === 'Week') ? "weeks" : "months";
    return date('Y-m-d', strtotime("+$duration $interval", strtotime($startDate)));
}

$currentDate = date('Y-m-d');

$stmt = $conn->prepare("SELECT id, date, access_permission FROM users");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $expirationDate = getExpirationDate($row['date'], $row['access_permission']);
    if ($currentDate > $expirationDate) {
        $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->bind_param("i", $row['id']);
        $deleteStmt->execute();
        $deleteStmt->close();
    }
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'approve' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $newPermission = $_POST['accessPermission'];
            $stmt = $conn->prepare("UPDATE users SET access_permission = ?, date = ? WHERE id = ?");
            $newDate = date('Y-m-d');
            $stmt->bind_param("ssi", $newPermission, $newDate, $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        } elseif ($_POST['action'] === 'unapprove' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            echo json_encode(['success' => $success, 'error' => $success ? '' : $stmt->error]);
            $stmt->close();
        } elseif ($_POST['action'] === 'approve_all' && isset($_POST['ids'])) {
            $ids = json_decode($_POST['ids'], true);
            $newPermission = $_POST['accessPermission'];
            $newDate = date('Y-m-d');
            $success = true;
            foreach ($ids as $id) {
                $stmt = $conn->prepare("UPDATE users SET access_permission = ?, date = ? WHERE id = ?");
                $stmt->bind_param("ssi", $newPermission, $newDate, $id);
                if (!$stmt->execute()) $success = false;
                $stmt->close();
            }
            echo json_encode(['success' => $success, 'error' => $success ? '' : 'Error approving some users']);
        } elseif ($_POST['action'] === 'unapprove_all' && isset($_POST['ids'])) {
            $ids = json_decode($_POST['ids'], true);
            $success = true;
            foreach ($ids as $id) {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) $success = false;
                $stmt->close();
            }
            echo json_encode(['success' => $success, 'error' => $success ? '' : 'Error unapproving some users']);
        }
    }
    $conn->close();
    exit;
}

$deptStmt = $conn->prepare("SELECT DISTINCT department FROM users ORDER BY department");
$deptStmt->execute();
$deptResult = $deptStmt->get_result();
$departments = [];
while ($row = $deptResult->fetch_assoc()) {
    $departments[] = $row['department'];
}
$deptStmt->close();

$yearStmt = $conn->prepare("SELECT DISTINCT academic_year FROM users ORDER BY academic_year");
$yearStmt->execute();
$yearResult = $yearStmt->get_result();
$academic_years = [];
while ($row = $yearResult->fetch_assoc()) {
    $academic_years[] = $row['academic_year'];
}
$yearStmt->close();

$search_department = isset($_GET['department']) ? $_GET['department'] : '';
$search_academic_year = isset($_GET['academic_year']) ? $_GET['academic_year'] : '';

$sql = "SELECT id, date, academic_year, full_name, id_number, department, year, semester, access_permission, profile_image FROM users WHERE 1=1";
$params = [];
$types = '';

if ($search_department !== '') {
    $sql .= " AND department = ?";
    $params[] = $search_department;
    $types .= 's';
}

if ($search_academic_year !== '') {
    $sql .= " AND academic_year = ?";
    $params[] = $search_academic_year;
    $types .= 's';
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$users = [];
$approval_count = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $expirationDate = getExpirationDate($row['date'], $row['access_permission']);
        $remainingSeconds = strtotime($expirationDate) - strtotime($currentDate);
        $remainingDays = floor($remainingSeconds / (24 * 60 * 60));
        
        if ($remainingDays <= 30) {
            $row['expiration_date'] = $expirationDate;
            $row['remaining_days'] = $remainingDays;
            $users[] = $row;
            $approval_count++;
        }
    }
}

$total_displayed_users = count($users);
error_log("user approve.php - Total displayed users: $total_displayed_users");

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Approval</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/user approve.css">
</head>
<body>
    <div class="container">
        <div class="header">
            WELCOME TO USER APPROVAL SYSTEM - THIS PAGE SHOWS USERS WITH 30 DAYS OR LESS REMAINING ACCESS
        </div>
        <div class="controls">
            <div class="action-buttons">
                <button class="btn btn-select-all" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square"></i> 
                    <span id="selectAllText">Select All</span></button>
                <button class="btn btn-approve-all" onclick="approveAll()">
                    <i class="fas fa-check-circle"></i> Approve All
                </button>
                <button class="btn btn-unapprove-all" onclick="unapproveAll()">
                    <i class="fas fa-times-circle"></i> Unapprove All
                </button>
                <div class="selected-counter">
                    <i class="fas fa-users"></i>
                    <span id="selectedCount">0</span> user(s) selected
                </div>
            </div>
            <form class="search-form" method="GET" action="">
                <select name="department">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept); ?>" <?php echo $search_department === $dept ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="academic_year">
                    <option value="">All Academic Years</option>
                    <?php foreach ($academic_years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year); ?>" <?php echo $search_academic_year === $year ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
        <div class="user-grid" id="userGrid">
            <?php if (empty($users)): ?>
                <p>No users found with 30 days or less remaining access.</p>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-card" data-id="<?php echo $user['id']; ?>">
                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="/bookstore/book/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-img">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/100" alt="Default Profile" class="profile-img">
                        <?php endif; ?>
                        <div class="user-info">
                            <span>Full Name:</span> <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>
                        <div class="user-info">
                            <span>Date:</span> <?php echo htmlspecialchars($user['date']); ?>
                        </div>
                        <div class="user-info">
                            <span>Academic Year:</span> <?php echo htmlspecialchars($user['academic_year']); ?>
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
                            <span>Access Permission:</span> <?php echo htmlspecialchars($user['access_permission']); ?>
                        </div>
                        <div class="user-info">
                            <span>Expires On:</span> <?php echo htmlspecialchars($user['expiration_date']); ?>
                        </div>
                        <div class="user-info">
                            <span>Remaining Days:</span> 
                            <?php 
                            if ($user['remaining_days'] >= 0) {
                                echo $user['remaining_days'] . ' days left';
                            } else {
                                echo 'Expired';
                            }
                            ?>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" class="select-user" data-id="<?php echo $user['id']; ?>"> Select User</div>
                        <div class="button-group">
                            <button class="btn btn-approve" onclick="approveUser(<?php echo $user['id']; ?>)"><i class="fas fa-check"></i> Approve</button>
                            <button class="btn btn-unapprove" onclick="unapproveUser(<?php echo $user['id']; ?>)"><i class="fas fa-trash"></i> Unapprove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="approveModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Approve User Access</h2>
            <form id="approveForm">
                <input type="hidden" id="approveId" name="id">
                <div class="form-group">
                    <label for="approveAccessPermission">New Access Permission</label>
                    <select id="approveAccessPermission" name="accessPermission" required>
                        <option value="" disabled selected>Select duration</option>
                        <option value="1 Month">1 Month</option>
                        <option value="2 Months">2 Months</option>
                        <option value="3 Months">3 Months</option>
                        <option value="4 Months">4 Months</option>
                        <option value="5 Months">5 Months</option>
                        <option value="6 Months">6 Months</option>
                        <option value="7 Months">7 Months</option>
                        <option value="8 Months">8 Months</option>
                        <option value="9 Months">9 Months</option>
                        <option value="10 Months">10 Months</option>
                        <option value="11 Months">11 Months</option>
                        <option value="12 Months">12 Months</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        let isAllSelected = false;

        function toggleSelectAll() {
            isAllSelected = !isAllSelected;
            const checkboxes = document.querySelectorAll('.select-user');
            const selectAllButton = document.querySelector('#selectAllText');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = isAllSelected;
            });
            
            selectAllButton.textContent = isAllSelected ? 'Deselect All' : 'Select All';
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.select-user:checked').length;
            document.getElementById('selectedCount').textContent = selectedCount;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.select-user');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
        });

        function approveUser(id) {
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');
            document.getElementById('approveId').value = id;
            document.getElementById('approveAccessPermission').value = '';
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
                        location.reload();
                        updateSelectedCount();
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

        function unapproveUser(id) {
            if (confirm('Are you sure you want to unapprove and delete this user?')) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=unapprove&id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                        updateSelectedCount();
                    } else {
                        alert('Error unapproving user: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while unapproving the user.');
                });
            }
        }

        function approveAll() {
            const selectedIds = Array.from(document.querySelectorAll('.select-user:checked')).map(cb => cb.dataset.id);
            if (selectedIds.length === 0) {
                alert('Please select at least one user to approve.');
                return;
            }
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');
            document.getElementById('approveId').value = '';
            document.getElementById('approveAccessPermission').value = '';
            modal.style.display = 'flex';

            form.onsubmit = function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'approve_all');
                formData.append('ids', JSON.stringify(selectedIds));

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                        updateSelectedCount();
                    } else {
                        alert('Error approving users: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving users.');
                });
            };
        }

        function unapproveAll() {
            const selectedIds = Array.from(document.querySelectorAll('.select-user:checked')).map(cb => cb.dataset.id);
            if (selectedIds.length === 0) {
                alert('Please select at least one user to unapprove.');
                return;
            }
            if (confirm('Are you sure you want to unapprove and delete all selected users?')) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=unapprove_all&ids=${JSON.stringify(selectedIds)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                        updateSelectedCount();
                    } else {
                        alert('Error unapproving users: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while unapproving users.');
                });
            }
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