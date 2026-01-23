<?php
$pageTitle = "Gallery Details";
$currentPage = "gallery";
include 'includes/header.php';
?>

<?php
if (!isset($_GET['id'])) {
    header("Location: gallery.php");
    exit;
}

$album_id = (int) $_GET['id'];
$conn = getDbConnection();

// Fetch Album Info
$stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
$stmt->bind_param("i", $album_id);
$stmt->execute();
$album = $stmt->get_result()->fetch_assoc();

if (!$album) {
    header("Location: gallery.php");
    exit;
}

// Fetch Images
$images = $conn->query("SELECT * FROM gallery_images WHERE album_id = $album_id ORDER BY created_at DESC");

$pageTitle = $album['name'];
$breadcrumbs = ["Gallery" => "gallery.php"];
$breadcrumbActive = "Details";
include 'includes/breadcrumb.php';
?>

<!-- Album Images Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <!-- <h6 class="section-title bg-white text-center text-primary px-3">Album</h6> -->
            <!-- <h1 class="mb-5">
                <?php echo htmlspecialchars($album['name']); ?>
            </h1> -->
            <p class="mb-5">
                <?php echo nl2br(htmlspecialchars($album['description'])); ?>
            </p>
        </div>
        <div class="row g-4 gallery-container">
            <?php if ($images->num_rows > 0): ?>
                <?php while ($img = $images->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="position-relative overflow-hidden border rounded shadow">
                            <a href="<?php echo $img['image_path']; ?>"
                                data-caption="<?php echo htmlspecialchars($album['name']); ?>">
                                <img class="img-fluid w-100" src="<?php echo $img['image_path']; ?>" alt=""
                                    style="height: 250px; object-fit: cover;">
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No images found in this album.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/footer.php';
?>

<!-- BaguetteBox CSS/JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js"></script>
<script>
    window.addEventListener('load', function () {
        baguetteBox.run('.gallery-container');
    });
</script>