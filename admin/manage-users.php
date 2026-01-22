<?php
$pageTitle = "Manage Users";
$currentPage = "users";
require_once __DIR__ . '/includes/header.php';
checkAdmin();

$conn = getDbConnection();
$message = '';
$messageType = '';

// Handle Status Toggle
if (isset($_GET['toggle_status'])) {
    $id = (int) $_GET['toggle_status'];
    $current_status = $_GET['current'];
    $new_status = ($current_status == '1') ? '0' : '1';

    $stmt = $conn->prepare("UPDATE users SET user_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $id);
    if ($stmt->execute()) {
        $message = "User status updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Handle Change Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $id = (int) $_POST['id'];
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $id);
    if ($stmt->execute()) {
        $message = "Password changed successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_user'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $user_role = $_POST['user_role'];

    if ($id > 0) {
        // Edit User
        $stmt = $conn->prepare("UPDATE users SET username = ?, user_role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $user_role, $id);
    } else {
        // Add User
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, user_role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $user_role);
    }

    try {
        if ($stmt->execute()) {
            $message = "User saved successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // Duplicate entry
            $message = "Error: Username already exists!";
        } else {
            $message = "Error: " . $e->getMessage();
        }
        $messageType = "error";
    }
}

// Fetch all users
$users = $conn->query("SELECT id, username, user_role, user_status, created_at FROM users ORDER BY id ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Manage Users</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Add New User
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">#</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th colspan="3" width="20%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($row = $users->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $row['id']; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['username']); ?>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge <?php echo $row['user_role'] == 'admin' ? 'bg-primary' : 'bg-secondary'; ?>">
                                        <?php echo ucfirst($row['user_role']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $row['user_status'] == '1' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $row['user_status'] == '1' ? 'Active' : 'Deactivated'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick='editUser(<?php echo $row['id']; ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                                <?php if ($row['user_role'] === 'admin'): ?>
                                    <td colspan="2" class="text-center">
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick='openPasswordModal(<?php echo $row['id']; ?>, "<?php echo htmlspecialchars($row['username']); ?>")'
                                            title="Change Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </td>
                                <?php else: ?>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick='openPasswordModal(<?php echo $row['id']; ?>, "<?php echo htmlspecialchars($row['username']); ?>")'
                                            title="Change Password">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>

                                <?php if ($row['user_role'] !== 'admin'): ?>
                                    <td class="text-center">
                                        <?php if ($row['user_status'] == '1'): ?>
                                            <a href="?toggle_status=<?php echo $row['id']; ?>&current=1"
                                                class="btn btn-sm btn-outline-danger" title="Deactivate User">
                                                <i class="fas fa-user-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="?toggle_status=<?php echo $row['id']; ?>&current=0"
                                                class="btn btn-sm btn-outline-success" title="Activate User">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>

                    <div class="mb-3" id="passwordSection">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="user_role" class="form-label">User Role</label>
                        <select name="user_role" id="user_role" class="form-select" required>
                            <option value="" selected disabled>Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_user" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password for <span id="pwdUser"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="pwdUserId">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="change_password" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Add User';
        document.getElementById('userId').value = '';
        document.getElementById('username').value = '';
        document.getElementById('user_role').value = '';
        document.getElementById('password').required = true;
        document.getElementById('passwordSection').style.display = 'block';
    }

    function editUser(id) {
        document.getElementById('modalTitle').innerText = 'Loading...';
        new bootstrap.Modal(document.getElementById('userModal')).show();

        $.ajax({
            url: 'ajax/get-user.php',
            type: 'GET',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    document.getElementById('modalTitle').innerText = 'Edit User';
                    document.getElementById('userId').value = data.id;
                    document.getElementById('username').value = data.username;
                    document.getElementById('user_role').value = data.user_role;
                    document.getElementById('password').required = false;
                    document.getElementById('passwordSection').style.display = 'none';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching course details.');
            }
        });
    }

    function openPasswordModal(id, username) {
        document.getElementById('pwdUserId').value = id;
        document.getElementById('pwdUser').innerText = username;
        document.getElementById('new_password').value = '';
        new bootstrap.Modal(document.getElementById('passwordModal')).show();
    }

    <?php if ($message): ?>
        window.addEventListener('load', function() {
            showAlert('<?php echo $message; ?>', '<?php echo $messageType; ?>', '', <?php echo $messageType === 'error' ? 'false' : 'true'; ?>);
            <?php if ($messageType === 'error'): ?>
                <?php if (isset($_POST['save_user'])): ?>
                    new bootstrap.Modal(document.getElementById('userModal')).show();
                    document.getElementById('userId').value = '<?php echo (int) ($_POST['id'] ?? 0); ?>';
                    document.getElementById('username').value = '<?php echo addslashes($_POST['username'] ?? ''); ?>';
                    document.getElementById('user_role').value = '<?php echo addslashes($_POST['user_role'] ?? ''); ?>';
                    if (document.getElementById('userId').value > 0) {
                        document.getElementById('modalTitle').innerText = 'Edit User';
                        document.getElementById('passwordSection').style.display = 'none';
                        document.getElementById('password').required = false;
                    }
                <?php elseif (isset($_POST['change_password'])): ?>
                    document.getElementById('pwdUserId').value = '<?php echo (int) $_POST['id']; ?>';
                    document.getElementById('pwdUser').innerText = '<?php echo addslashes($_POST['username'] ?? 'User'); ?>';
                    new bootstrap.Modal(document.getElementById('passwordModal')).show();
                <?php endif; ?>
            <?php endif; ?>
        });
    <?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>