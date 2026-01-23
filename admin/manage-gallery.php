<?php
$pageTitle = "Manage Gallery Albums";
$currentPage = "gallery";
require_once __DIR__ . '/includes/header.php';

$conn = getDbConnection();
$message = '';
$messageType = '';

$formData = [
    'id' => '',
    'name' => '',
    'description' => '',
    'existing_image' => ''
];

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // First delete all images associated with this album from storage
    $imgQuery = $conn->query("SELECT image_path FROM gallery_images WHERE album_id = $id");
    while ($imgRow = $imgQuery->fetch_assoc()) {
        if (file_exists(__DIR__ . '/../' . $imgRow['image_path'])) {
            unlink(__DIR__ . '/../' . $imgRow['image_path']);
        }
    }

    // Delete album cover
    $stmt = $conn->prepare("SELECT cover_image FROM albums WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cover = $stmt->get_result()->fetch_assoc()['cover_image'];
    if ($cover && file_exists(__DIR__ . '/../' . $cover)) {
        unlink(__DIR__ . '/../' . $cover);
    }

    $stmt = $conn->prepare("DELETE FROM albums WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Album deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_album'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $name = trim($_POST['name']);
    $description = $_POST['description'];
    $image_path = $_POST['existing_image'];

    // Handle Cover Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/gallery/covers/";
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
                if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                    unlink(__DIR__ . '/../' . $image_path);
                }
                $image_path = "assets/img/gallery/covers/" . $file_name;
            }
        }
    }

    if ($messageType !== 'error') {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE albums SET name = ?, description = ?, cover_image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $description, $image_path, $id);
        } else {
            $stmt = $conn->prepare("INSERT INTO albums (name, description, cover_image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $description, $image_path);
        }

        if ($stmt->execute()) {
            $message = "Album saved successfully!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
    }
}

$albums = $conn->query("SELECT * FROM albums ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Gallery Albums</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#albumModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Create New Album
    </button>
</div>

<div class="row">
    <?php if ($albums->num_rows > 0): ?>
        <?php while ($row = $albums->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($row['cover_image']): ?>
                        <img src="../<?php echo $row['cover_image']; ?>" class="card-img-top"
                            alt="<?php echo htmlspecialchars($row['name']); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center"
                            style="height: 200px;">
                            <i class="fas fa-image fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </h5>
                        <p class="card-text text-muted small">
                            <?php echo substr(strip_tags($row['description']), 0, 100) . '...'; ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center">
                        <a href="manage-album-images.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-images"></i> Manage Images
                        </a>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-1"
                                onclick='editAlbum(<?php echo json_encode($row); ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirmDelete(<?php echo $row['id']; ?>, 'This will delete the album and ALL its images!')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 text-muted">
            <h4>No albums found.</h4>
            <p>Create your first album to get started!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Album Modal -->
<div class="modal fade" id="albumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create Album</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="albumId">
                    <input type="hidden" name="existing_image" id="existingImage">

                    <div class="mb-3">
                        <label for="name" class="form-label">Album Name</label>
                        <input type="text" name="name" id="albumName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="albumDescription" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Cover Image</label>
                        <input type="file" name="image" id="albumImage" class="form-control"
                            accept=".jpg,.jpeg,.png,.webp">
                        <div id="currentImageDisplay" class="mt-2" style="display:none;">
                            <small class="text-muted">Current Cover:</small><br>
                            <img src="" id="imgPreview" class="img-thumbnail" width="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_album" class="btn btn-primary">Save Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Create Album';
        document.getElementById('albumId').value = '';
        document.getElementById('existingImage').value = '';
        document.getElementById('albumName').value = '';
        document.getElementById('albumDescription').value = '';
        document.getElementById('albumImage').value = '';
        document.getElementById('currentImageDisplay').style.display = 'none';
    }

    function editAlbum(data) {
        new bootstrap.Modal(document.getElementById('albumModal')).show();
        document.getElementById('modalTitle').innerText = 'Edit Album';
        document.getElementById('albumId').value = data.id;
        document.getElementById('existingImage').value = data.cover_image || '';
        document.getElementById('albumName').value = data.name;
        document.getElementById('albumDescription').value = data.description;

        if (data.cover_image) {
            document.getElementById('currentImageDisplay').style.display = 'block';
            document.getElementById('imgPreview').src = '../' + data.cover_image;
        } else {
            document.getElementById('currentImageDisplay').style.display = 'none';
        }
    }

    <?php if ($message): ?>
            window.addEventListener('load', function () {
                showAlert('<?php echo $message; ?>', '<?php echo $messageType; ?>');
            });
    <?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>