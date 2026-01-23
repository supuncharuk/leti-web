<?php
$pageTitle = "Manage News";
$currentPage = "news";
require_once __DIR__ . '/includes/header.php';

$conn = getDbConnection();
$message = '';
$messageType = '';

// Form data for retention
$formData = [
    'id' => '',
    'title' => '',
    'category_id' => '',
    'publish_date' => date('Y-m-d'),
    'content' => '',
    'existing_image' => ''
];

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Also delete image if exists
    $stmt = $conn->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $img = $stmt->get_result()->fetch_assoc()['image'];
    if ($img && file_exists(__DIR__ . '/../' . $img)) {
        unlink(__DIR__ . '/../' . $img);
    }

    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "News article deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_news'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = trim($_POST['title']);
    $category_id = (int) $_POST['category_id'];
    $publish_date = $_POST['publish_date'];
    $content = $_POST['content'];

    $image_path = $_POST['existing_image'];

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/news/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file_ext, $allowed_ext)) {
            $message = "Error: Only .jpg, .jpeg, .png, and .webp files are allowed.";
            $messageType = "error";
        } else {
            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Delete old image if updating
                if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                    unlink(__DIR__ . '/../' . $image_path);
                }
                $image_path = "assets/img/news/" . $file_name;
            }
        }
    }

    if ($messageType !== 'error') {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE news SET title = ?, category_id = ?, publish_date = ?, image = ?, content = ?, updated_by = ? WHERE id = ?");
            $user_id = $_SESSION['user_id'];
            $stmt->bind_param("sisssii", $title, $category_id, $publish_date, $image_path, $content, $user_id, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO news (title, category_id, publish_date, image, content, added_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $user_id = $_SESSION['user_id'];
            $stmt->bind_param("sisssii", $title, $category_id, $publish_date, $image_path, $content, $user_id, $user_id);
        }

        if ($stmt->execute()) {
            $message = "News article saved successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
    }

    // Retain form data if there's an error
    if ($messageType === 'error') {
        $formData = [
            'id' => $id,
            'title' => $title,
            'category_id' => $category_id,
            'publish_date' => $publish_date,
            'content' => $content,
            'existing_image' => $_POST['existing_image']
        ];
    }
}

// Fetch all news with user info
$news = $conn->query("
    SELECT n.*, c.name as category_name, u1.username AS added_by_user, u2.username AS updated_by_user 
    FROM news n 
    LEFT JOIN news_categories c ON n.category_id = c.id 
    LEFT JOIN users u1 ON n.added_by = u1.id 
    LEFT JOIN users u2 ON n.updated_by = u2.id 
    ORDER BY n.publish_date DESC
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">News Articles</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newsModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Add New Article
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="10%">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <?php if (isAdmin()): ?>
                            <th>Added</th>
                            <th>Last Updated</th>
                        <?php endif; ?>
                        <th colspan="2" width="15%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($news->num_rows > 0): ?>
                        <?php while ($row = $news->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($row['image']): ?>
                                        <img src="../<?php echo $row['image']; ?>" class="rounded" width="60" alt="">
                                    <?php else: ?>
                                        <img src="https://placehold.co/60" class="rounded" alt="">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $row['title']; ?>
                                </td>
                                <td class="text-center"><span class="badge bg-info text-dark">
                                        <?php echo $row['category_name']; ?>
                                    </span></td>
                                <td class="text-center">
                                    <?php echo date('M d, Y', strtotime($row['publish_date'])); ?>
                                </td>
                                <?php if (isAdmin()): ?>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['added_by_user'] ? htmlspecialchars($row['added_by_user']) : '-'; ?>
                                            <?php if ($row['created_at']): ?>
                                                <br><span
                                                    style="font-size: 0.8em;"><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['updated_by_user'] ? htmlspecialchars($row['updated_by_user']) : '-'; ?>
                                            <?php if ($row['updated_at']): ?>
                                                <br><span
                                                    style="font-size: 0.8em;"><?php echo date('M d, H:i', strtotime($row['updated_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary me-2"
                                        onclick='editNews(<?php echo $row['id']; ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, 'This article will be permanently removed!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No news articles found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- News Modal -->
<div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add News Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="newsId" value="<?php echo $formData['id']; ?>">
                    <input type="hidden" name="existing_image" id="existingImage"
                        value="<?php echo $formData['existing_image']; ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Article Title</label>
                                <input type="text" name="title" id="newsTitle" class="form-control" required
                                    value="<?php echo htmlspecialchars($formData['title']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" id="newsCategory" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $cats = $conn->query("SELECT * FROM news_categories ORDER BY name ASC");
                                    while ($cat = $cats->fetch_assoc()) {
                                        $selected = $formData['category_id'] == $cat['id'] ? 'selected' : '';
                                        echo "<option value='{$cat['id']}' {$selected}>{$cat['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="publish_date" class="form-label">Publish Date</label>
                                <input type="date" name="publish_date" id="newsDate" class="form-control" required
                                    value="<?php echo $formData['publish_date']; ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Article Image</label>
                                <input type="file" name="image" id="newsImage" class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp" <?php echo $formData['id'] ? '' : 'required'; ?>>
                                <?php if ($formData['existing_image']): ?>
                                    <small class="text-muted">Currently:
                                        <?php echo basename($formData['existing_image']); ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="newsContent" class="form-control" rows="10"
                            required><?php echo htmlspecialchars($formData['content']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_news" class="btn btn-primary">Save Article</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#newsContent').summernote({
            placeholder: 'Write your news content here...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough']],
                ['font', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview', 'help']]
            ],
            styleTags: [
                'p',
                { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
                'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
            ]
        });
    });

    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Add News Article';
        document.getElementById('newsId').value = '';
        document.getElementById('existingImage').value = '';
        document.getElementById('newsTitle').value = '';
        document.getElementById('newsCategory').value = '';
        document.getElementById('newsDate').value = '<?php echo date('Y-m-d'); ?>';
        $('#newsContent').summernote('code', '');
    }

    function editNews(id) {
        document.getElementById('modalTitle').innerText = 'Loading...';
        new bootstrap.Modal(document.getElementById('newsModal')).show();

        $.ajax({
            url: 'ajax/get-news.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const data = response.data;
                    document.getElementById('modalTitle').innerText = 'Edit News Article';
                    document.getElementById('newsId').value = data.id;
                    document.getElementById('existingImage').value = data.image || '';
                    document.getElementById('newsTitle').value = data.title;
                    document.getElementById('newsCategory').value = data.category_id;
                    document.getElementById('newsDate').value = data.publish_date;
                    $('#newsContent').summernote('code', data.content);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function () {
                alert('An error occurred while fetching course details.');
            }
        });
    }

    <?php if ($message): ?>
        window.addEventListener('load', function () {
            showAlert('<?php echo $message; ?>', '<?php echo $messageType; ?>', '', <?php echo $messageType === 'error' ? 'false' : 'true'; ?>);
            <?php if ($messageType === 'error'): ?>
                new bootstrap.Modal(document.getElementById('newsModal')).show();
                document.getElementById('modalTitle').innerText = '<?php echo $formData['id'] ? 'Edit News Article' : 'Add News Article'; ?>';
            <?php endif; ?>
        });
    <?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>