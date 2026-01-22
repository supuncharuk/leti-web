<?php
$pageTitle = "Home";
$currentPage = "home";
include 'includes/header.php';
?>

<!-- Hero Slider -->
<header id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <img src="assets/images/hero_banner.png" class="d-block w-100" alt="Welding Workshop"
                style="height: 600px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 p-3 rounded">
                <h1>Excellence in Engineering Training</h1>
                <p>Building the skilled workforce of tomorrow through hands-on welding and technical education.</p>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <div
                style="height: 600px; background-color: #ccc; display: flex; align-items: center; justify-content: center;">
                <div class="text-center">
                    <i class="fas fa-tools fa-5x text-secondary mb-3"></i>
                    <h1>State-of-the-Art Workshops</h1>
                </div>
            </div>
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-3 rounded">
                <h5>Practical Training</h5>
                <p>Master industry-standard equipment and techniques.</p>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <div
                style="height: 600px; background-color: #bbb; display: flex; align-items: center; justify-content: center;">
                <div class="text-center">
                    <i class="fas fa-users fa-5x text-secondary mb-3"></i>
                    <h1>Vibrant Student Life</h1>
                </div>
            </div>
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-3 rounded">
                <h5>Community & Growth</h5>
                <p>Be part of a supportive learning environment.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</header>

<!-- About Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="mb-4">ලුහු ඉංජිනේරු අභ්‍යාස ආයතනය වෙත සාදරෙයෙන් පිළිගනිමු</h2>
                <p class="lead text-muted">වෙල්ඩින් සහ තාක්ෂණික විශිෂ්ටත්වය සඳහා කැප වූ ප්‍රමුඛතම ආයතනයකි.</p>
                <p>අහංගම ලුහු ඉංජිනේරු අභ්‍යාස ආයතනය ලෝක මට්ටමේ පෑස්සුම්කරුවන් සහ කාර්මික ශිල්පීන් බිහි කිරීමට කැපවී
                    සිටී.
                    MMAW, FCAW සහ TIG වෙල්ඩින් පිළිබඳ අපගේ විශේෂිත NVQ පාඨමාලා අපගේ සිසුන් ගෝලීය රැකියා වෙළඳපොළට
                    සූදානම් බව සහතික කරයි.</p>
                <a href="about.php" class="btn btn-primary-custom mt-3">තවත් කියවන්න</a>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <img src="assets/images/tig_welding.png" class="img-fluid rounded shadow" alt="TIG Welding">
            </div>
        </div>
    </div>
</section>

<!-- Featured Courses -->
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title">Our Featured Courses</h2>
        <div class="row">
            <?php
            $conn = getDbConnection();
            $featuredCourses = $conn->query("SELECT * FROM courses ORDER BY created_at DESC LIMIT 3");
            if ($featuredCourses->num_rows > 0):
                while ($course = $featuredCourses->fetch_assoc()):
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?php echo $course['image'] ? $course['image'] : 'https://placehold.co/400x200?text=Course'; ?>"
                                class="card-img-top" alt="<?php echo $course['title']; ?>"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $course['title']; ?></h5>
                                <p class="card-text text-muted"><?php echo substr($course['intro_text'], 0, 100); ?>...</p>
                            </div>
                            <div class="card-footer bg-white border-0 pb-3">
                                <a href="course-details.php?id=<?php echo $course['id']; ?>"
                                    class="btn btn-outline-danger w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p class="text-center">No courses available at the moment.</p>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="courses.php" class="btn btn-primary-custom">View All Courses</a>
        </div>
    </div>
</section>

<!-- Latest News -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title">Latest News & Events</h2>
        <div class="row">
            <?php
            $latestNews = $conn->query("SELECT * FROM news ORDER BY publish_date DESC LIMIT 3");
            if ($latestNews->num_rows > 0):
                while ($news = $latestNews->fetch_assoc()):
                    $d = strtotime($news['publish_date']);
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="d-flex">
                            <div class="bg-primary-custom text-white p-3 text-center me-3"
                                style="min-width: 80px; height: fit-content;">
                                <span class="d-block h4 mb-0"><?php echo date('d', $d); ?></span>
                                <span class="d-block small"><?php echo date('M', $d); ?></span>
                            </div>
                            <div>
                                <h5><a href="news-details.php?id=<?php echo $news['id']; ?>"
                                        class="text-decoration-none text-dark"><?php echo $news['title']; ?></a></h5>
                                <p class="text-muted small"><?php echo substr(strip_tags($news['content']), 0, 80); ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p class="text-center">No news articles found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>