<?php
$pageTitle = "News & Events";
$currentPage = "news";
include 'includes/header.php';
?>

<?php
$breadcrumbActive = "News";
include 'includes/breadcrumb.php';
?>

<div class="section-padding">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <div class="btn-group" role="group" aria-label="News Filters">
                    <a href="news.php"
                        class="btn btn-outline-secondary <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All</a>
                    <?php
                    $conn = getDbConnection();
                    $cats = $conn->query("SELECT * FROM news_categories ORDER BY name ASC");
                    while ($cat = $cats->fetch_assoc()):
                        $isActive = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active' : '';
                        ?>
                        <a href="news.php?category=<?php echo $cat['id']; ?>"
                            class="btn btn-outline-secondary <?php echo $isActive; ?>">
                            <?php echo $cat['name']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="row" id="news-grid">
            <?php
            $catFilter = isset($_GET['category']) ? "WHERE category_id = " . (int) $_GET['category'] : "";
            $sql = "SELECT n.*, c.name as category_name 
                    FROM news n 
                    LEFT JOIN news_categories c ON n.category_id = c.id 
                    $catFilter 
                    ORDER BY n.publish_date DESC";
            $newsList = $conn->query($sql);

            if ($newsList->num_rows > 0):
                while ($news = $newsList->fetch_assoc()):
                    ?>
                    <div class="col-md-4 mb-4 news-item">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?php echo $news['image'] ? $news['image'] : 'https://placehold.co/400x250?text=News'; ?>"
                                class="card-img-top" alt="News Image" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2"><?php echo $news['category_name']; ?></span>
                                <h5 class="card-title">
                                    <a href="news-details.php?id=<?php echo $news['id']; ?>"
                                        class="text-decoration-none text-dark">
                                        <?php echo $news['title']; ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small">
                                    <?php echo substr(strip_tags($news['content']), 0, 100); ?>...</p>
                                <a href="news-details.php?id=<?php echo $news['id']; ?>" class="btn btn-link px-0">Read More <i
                                        class="fas fa-arrow-right small"></i></a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No news articles found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>