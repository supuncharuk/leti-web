<?php
$pageTitle = "Dashboard";
$currentPage = "dashboard";
require_once __DIR__ . '/includes/header.php';

$conn = getDbConnection();

// Fetch stats
$courseCount = $conn->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
$newsCount = $conn->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
$categoryCount = $conn->query("SELECT COUNT(*) FROM news_categories")->fetch_row()[0];

?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div>
                <h6 class="text-muted mb-1">Total Courses</h6>
                <h3 class="mb-0 fw-bold">
                    <?php echo $courseCount; ?>
                </h3>
            </div>
            <div class="stats-icon bg-primary">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div>
                <h6 class="text-muted mb-1">News Articles</h6>
                <h3 class="mb-0 fw-bold">
                    <?php echo $newsCount; ?>
                </h3>
            </div>
            <div class="stats-icon bg-success">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div>
                <h6 class="text-muted mb-1">News Categories</h6>
                <h3 class="mb-0 fw-bold">
                    <?php echo $categoryCount; ?>
                </h3>
            </div>
            <div class="stats-icon bg-warning">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent News</span>
                <a href="manage-news.php" class="btn btn-sm btn-link p-0">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Title</th>
                                <th>Category</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recentNews = $conn->query("SELECT n.title, c.name as category, n.publish_date 
                                                      FROM news n 
                                                      LEFT JOIN news_categories c ON n.category_id = c.id 
                                                      ORDER BY n.created_at DESC LIMIT 5");
                            if ($recentNews->num_rows > 0):
                                while ($row = $recentNews->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <?php echo $row['title']; ?>
                                        </td>
                                        <td><span class="badge bg-info text-dark">
                                                <?php echo $row['category']; ?>
                                            </span></td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($row['publish_date'])); ?>
                                        </td>
                                    </tr>
                                <?php
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No news articles yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                System Info
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">PHP Version</span>
                        <span>
                            <?php echo phpversion(); ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Database</span>
                        <span>MySQL</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Admin User</span>
                        <span>
                            <?php echo $_SESSION['user_name']; ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>