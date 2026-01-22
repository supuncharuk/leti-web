<?php
$pageTitle = "Manage News Categories";
$currentPage = "categories";
require_once __DIR__ . '/includes/header.php';

$conn = getDbConnection();
$message = '';
$messageType = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_category'])) {
    $name = trim($_POST['name']);
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if (empty($name)) {
        $message = "Please enter a category name.";
        $messageType = "error";
    } else {
        // Check for duplicate name
        $check_stmt = $conn->prepare("SELECT id FROM news_categories WHERE name = ? AND id != ?");
        $check_stmt->bind_param("si", $name, $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $message = "A category with this name already exists.";
            $messageType = "error";
        } else {
            if ($id > 0) {
                // Update
                $stmt = $conn->prepare("UPDATE news_categories SET name = ?, updated_by = ? WHERE id = ?");
                $user_id = $_SESSION['user_id'];
                $stmt->bind_param("sii", $name, $user_id, $id);
            } else {
                // Insert
                $stmt = $conn->prepare("INSERT INTO news_categories (name, added_by, updated_by) VALUES (?, ?, ?)");
                $user_id = $_SESSION['user_id'];
                $stmt->bind_param("sii", $name, $user_id, $user_id);
            }

            if ($stmt->execute()) {
                $message = $id > 0 ? "Category updated successfully!" : "Category added successfully!";
                $messageType = "success";
            } else {
                $message = "Error: " . $conn->error;
                $messageType = "error";
            }
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM news_categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Category deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting category (it might be in use).";
        $messageType = "error";
    }
}

// Fetch all categories with user info
$categories = $conn->query("
    SELECT c.*, u1.username AS added_by_user, u2.username AS updated_by_user 
    FROM news_categories c 
    LEFT JOIN users u1 ON c.added_by = u1.id 
    LEFT JOIN users u2 ON c.updated_by = u2.id 
    ORDER BY c.name ASC
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">News Categories</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Add New Category
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">ID</th>
                        <th>Category Name</th>
                        <?php if (isAdmin()): ?>
                            <th width="20%">Added</th>
                            <th width="20%">Last Updated</th>
                        <?php endif; ?>
                        <th colspan="2" width="10%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($categories->num_rows > 0): ?>
                        <?php while ($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $row['id']; ?>
                                </td>
                                <td>
                                    <?php echo $row['name']; ?>
                                </td>
                                <?php if (isAdmin()): ?>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['added_by_user'] ? htmlspecialchars($row['added_by_user']) : '-'; ?>
                                            <?php if ($row['created_at']): ?>
                                                <br><span
                                                    style="font-size: 0.8em;"><?php echo date('Y M d, H:i', strtotime($row['created_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['updated_by_user'] ? htmlspecialchars($row['updated_by_user']) : '-'; ?>
                                            <?php if ($row['updated_at']): ?>
                                                <br><span
                                                    style="font-size: 0.8em;"><?php echo date('Y M d, H:i', strtotime($row['updated_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="editCategory(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, 'You won\'t be able to revert this!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" name="name" id="categoryName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-modal="dismiss">Cancel</button>
                    <button type="submit" name="save_category" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Add Category';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryName').value = '';
    }

    function editCategory(id, name) {
        document.getElementById('modalTitle').innerText = 'Edit Category';
        document.getElementById('categoryId').value = id;
        document.getElementById('categoryName').value = name;
        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }

    <?php if ($message): ?>
        window.addEventListener('load', function () {
            showAlert('<?php echo $message; ?>', '<?php echo $messageType; ?>', '', <?php echo $messageType === 'error' ? 'false' : 'true'; ?>);
            <?php if ($messageType === 'error'): ?>
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
                <?php if (isset($_POST['id']) && $_POST['id'] > 0): ?>
                    document.getElementById('modalTitle').innerText = 'Edit Category';
                    document.getElementById('categoryId').value = '<?php echo (int) $_POST['id']; ?>';
                    document.getElementById('categoryName').value = '<?php echo addslashes($_POST['name']); ?>';
                <?php endif; ?>
            <?php endif; ?>
        });
    <?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>