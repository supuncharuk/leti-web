<?php
require_once 'includes/config.php';
$conn = getDbConnection();
$newsId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$newsItem = null;

if ($newsId > 0) {
    $stmt = $conn->prepare("SELECT n.*, c.name as category_name FROM news n LEFT JOIN news_categories c ON n.category_id = c.id WHERE n.id = ?");
    $stmt->bind_param("i", $newsId);
    $stmt->execute();
    $newsItem = $stmt->get_result()->fetch_assoc();
}

if (!$newsItem) {
    $pageTitle = "News Not Found";
    include 'includes/header.php';
    echo "<div class='container py-5'><div class='alert alert-warning'>News article not found. <a href='news.php'>Back to news</a></div></div>";
    include 'includes/footer.php';
    exit();
}

$pageTitle = $newsItem['title'];
$currentPage = "news";
include 'includes/header.php';

$breadcrumbs = ["News" => "news.php"];
$breadcrumbActive = "Details";
include 'includes/breadcrumb.php';
?>

<div class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <h1 class="mb-3"><?php echo $newsItem['title']; ?></h1>
                <div class="text-muted mb-4">
                    <i class="far fa-calendar-alt me-2"></i>
                    <?php echo date('F d, Y', strtotime($newsItem['publish_date'])); ?> |
                    <span class="badge bg-primary ms-2"><?php echo $newsItem['category_name']; ?></span>
                </div>

                <img src="<?php echo $newsItem['image'] ? $newsItem['image'] : 'https://placehold.co/800x400?text=News'; ?>"
                    class="img-fluid rounded mb-4 w-100" alt="News Banner">

                <div class="article-content">
                    <?php echo nl2br($newsItem['content']); ?>
                </div>

                <!-- <div class="alert alert-info mt-4">
                    <strong>Note:</strong> Selected candidates will be called for an interview in the first week of
                    February.
                </div> -->

                <!-- Share Buttons -->
                <div class="mt-5 border-top pt-4">
                    <h5>Share this post:</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm"><i class="fab fa-facebook"></i> Facebook</button>
                        <button class="btn btn-outline-info btn-sm"><i class="fab fa-twitter"></i> Twitter</button>
                        <button class="btn btn-outline-success btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                    </div>
                </div>

                <!-- Next/Previous Navigation -->
                <nav aria-label="News navigation" class="mt-5">
                    <div class="row justify-content-between">
                        <div class="col-6 text-start">
                            <a href="#" class="btn btn-outline-secondary btn-sm disabled">
                                <i class="fas fa-arrow-left me-1"></i> Previous News
                            </a>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="btn btn-outline-secondary btn-sm">
                                Next News <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <!-- Recent News Widget -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Recent News</h5>
                        <ul class="list-unstyled mb-0">
                            <?php
                            $recentNews = $conn->query("SELECT id, title, publish_date, image FROM news WHERE id != $newsId ORDER BY publish_date DESC LIMIT 3");
                            while ($rn = $recentNews->fetch_assoc()):
                                ?>
                                <li class="mb-3">
                                    <a href="news-details.php?id=<?php echo $rn['id']; ?>"
                                        class="text-decoration-none text-dark d-flex align-items-center">
                                        <img src="<?php echo $rn['image'] ? $rn['image'] : 'https://placehold.co/60x60?text=News'; ?>"
                                            class="rounded me-3" width="60" height="60" style="object-fit:cover;"
                                            alt="Thumb">
                                        <div>
                                            <h6 class="mb-0 small fw-bold"><?php echo substr($rn['title'], 0, 40); ?>...
                                            </h6>
                                            <small
                                                class="text-muted"><?php echo date('M d, Y', strtotime($rn['publish_date'])); ?></small>
                                        </div>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Categories</h5>
                        <ul class="list-group list-group-flush">
                            <?php
                            $allCats = $conn->query("SELECT c.*, COUNT(n.id) as news_count FROM news_categories c LEFT JOIN news n ON c.id = n.category_id GROUP BY c.id ORDER BY name ASC");
                            while ($cat = $allCats->fetch_assoc()):
                                ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <a href="news.php?category=<?php echo $cat['id']; ?>"
                                        class="text-decoration-none text-secondary"><?php echo $cat['name']; ?></a>
                                    <span class="badge bg-primary rounded-pill"><?php echo $cat['news_count']; ?></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>