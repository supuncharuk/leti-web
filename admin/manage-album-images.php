<?php
$pageTitle = "Manage Album Images";
$currentPage = "gallery";
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='manage-gallery.php';</script>";
    exit;
}

$album_id = (int) $_GET['id'];
$conn = getDbConnection();

// Fetch Album Details
$albumStmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
$albumStmt->bind_param("i", $album_id);
$albumStmt->execute();
$album = $albumStmt->get_result()->fetch_assoc();

if (!$album) {
    echo "<div class='alert alert-danger'>Album not found!</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$message = '';
$messageType = '';

// Handle Image Delete
if (isset($_GET['delete_img'])) {
    $imgId = (int) $_GET['delete_img'];

    $stmt = $conn->prepare("SELECT image_path FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $imgId);
    $stmt->execute();
    $img = $stmt->get_result()->fetch_assoc()['image_path'];

    if ($img && file_exists(__DIR__ . '/../' . $img)) {
        unlink(__DIR__ . '/../' . $img);
    }

    $stmt = $conn->prepare("DELETE FROM gallery_images WHERE id = ?");
    $stmt->bind_param("i", $imgId);
    if ($stmt->execute()) {
        $message = "Image deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting image.";
        $messageType = "error";
    }
}

// Handle Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_images'])) {
    $target_dir = "../assets/img/gallery/albums/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $count = count($_FILES['images']['name']);
    $successCount = 0;

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['images']['error'][$i] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                $file_name = time() . '_' . uniqid() . '_' . $i . '.' . $file_ext;
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                    $image_path = "assets/img/gallery/albums/" . $file_name;
                    $stmt = $conn->prepare("INSERT INTO gallery_images (album_id, image_path) VALUES (?, ?)");
                    $stmt->bind_param("is", $album_id, $image_path);
                    if ($stmt->execute()) {
                        $successCount++;
                    }
                }
            }
        }
    }

    if ($successCount > 0) {
        $message = "$successCount images uploaded successfully!";
        $messageType = "success";
    } else {
        $message = "No images were uploaded. Please check file types.";
        $messageType = "warning";
    }
}

// Fetch Images
$images = $conn->query("SELECT * FROM gallery_images WHERE album_id = $album_id ORDER BY created_at DESC");
?>

<div class="mb-4">
    <a href="manage-gallery.php" class="btn btn-outline-secondary mb-3"><i class="fas fa-arrow-left"></i> Back to
        Albums</a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0">
                <?php echo htmlspecialchars($album['name']); ?>
            </h3>
            <p class="text-muted">Manage images for this album</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload"></i> Upload Images
        </button>
    </div>
</div>

<div class="row g-3">
    <?php if ($images->num_rows > 0): ?>
        <?php while ($row = $images->fetch_assoc()): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <div class="card h-100 position-relative group-hover">
                    <a href="../<?php echo $row['image_path']; ?>" target="_blank">
                        <img src="../<?php echo $row['image_path']; ?>" class="card-img-top"
                            style="height: 150px; object-fit: cover;">
                    </a>
                    <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                        onclick="confirmDelete(<?php echo $row['id']; ?>, 'Delete this image permanently?', 'delete_img')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 text-muted">
            <i class="fas fa-images fa-3x mb-3"></i>
            <p>No images in this album yet.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Select Images (Multiple allowed)</label>
                        <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.webp"
                            required>
                        <div class="form-text">Supported: JPG, PNG, WEBP.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="upload_images" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, text, paramName = 'delete') {
        if (confirm(text)) {
            window.location.href = `?id=<?php echo $album_id; ?>&${paramName}=` + id;
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