<?php
$pageTitle = "Gallery";
$currentPage = "gallery";
include 'includes/header.php';
?>

<?php
$breadcrumbActive = "Gallery";
include 'includes/breadcrumb.php';
?>

<!-- Gallery Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <!-- <h6 class="section-title bg-white text-center text-primary px-3">Gallery</h6>
            <h1 class="mb-5">Our Photo Albums</h1> -->
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $conn = getDbConnection();
            $albums = $conn->query("SELECT * FROM albums ORDER BY created_at DESC");
            if ($albums->num_rows > 0): ?>
                <?php while ($album = $albums->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="package-item">
                            <div class="overflow-hidden position-relative">
                                <?php if ($album['cover_image']): ?>
                                    <img class="img-fluid rounded border border-primary border-2" src="<?php echo $album['cover_image']; ?>"
                                        alt="<?php echo htmlspecialchars($album['name']); ?>"
                                        style="width: 100%; height: 250px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded border border-primary border-2"
                                        style="width: 100%; height: 250px;">
                                        <i class="fa fa-image fa-3x text-primary"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: .5s;">
                                    <a href="gallery-details.php?id=<?php echo $album['id']; ?>"
                                        class="btn btn-primary py-2 px-4">View Album</a>
                                </div>
                            </div>
                            <div class="text-center p-4">
                                <h3 class="mb-2">
                                    <?php echo htmlspecialchars($album['name']); ?>
                                </h3>
                                <!-- <p class="mb-0 text-muted">
                                    <?php echo substr(strip_tags($album['description']), 0, 100) . '...'; ?>
                                </p> -->
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No albums found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .package-item .position-relative:hover div {
        opacity: 1 !important;
    }
</style>

<?php
$conn->close();
require_once 'includes/footer.php';
?>