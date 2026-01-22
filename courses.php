<?php
$pageTitle = "Our Courses";
$currentPage = "courses";
include 'includes/header.php';
?>

<?php
$breadcrumbActive = "Courses";
include 'includes/breadcrumb.php';
?>

<div class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <h3 class="border-bottom pb-2">Our Courses</h3>
            </div>

            <?php
            $conn = getDbConnection();
            $result = $conn->query("SELECT * FROM courses ORDER BY title ASC");
            if ($result->num_rows > 0):
                while ($course = $result->fetch_assoc()):
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="<?php echo $course['image'] ? $course['image'] : 'https://placehold.co/400x200?text=Course'; ?>"
                                class="card-img-top" alt="<?php echo $course['title']; ?>"
                                style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span
                                    class="badge <?php echo $course['course_type'] == 'Full Time' ? 'bg-success' : 'bg-info text-dark'; ?> mb-2">
                                    <?php echo $course['course_type']; ?>
                                </span>
                                <h5 class="card-title"><?php echo $course['title']; ?></h5>
                                <p class="card-text small text-muted">
                                    <i class="far fa-clock me-1"></i> <?php echo $course['duration']; ?> |
                                    <i class="fas fa-certificate me-1"></i> NVQ Level <?php echo $course['nvq_level']; ?>
                                </p>
                                <p class="card-text"><?php echo substr($course['intro_text'], 0, 120); ?>...</p>
                                <a href="course-details.php?id=<?php echo $course['id']; ?>"
                                    class="btn btn-outline-danger w-100 mt-2">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No courses found in the database. Please add courses from the admin panel.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>