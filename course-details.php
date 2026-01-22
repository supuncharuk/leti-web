<?php
$pageTitle = "Course Details";
$currentPage = "courses";
include 'includes/header.php';
?>

<?php
$conn = getDbConnection();
$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$course = null;

if ($courseId > 0) {
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_assoc();
}

if (!$course) {
    echo "<div class='container py-5'><div class='alert alert-warning'>Course not found. <a href='courses.php'>Back to courses</a></div></div>";
    include 'includes/footer.php';
    exit();
}

// Parse Lists
$modules = json_parse_list($course['modules']);
$careers = json_parse_list($course['career_opportunities']);
$requirements = json_parse_list($course['entry_requirements']);

function json_parse_list($json)
{
    $list = json_decode($json, true);
    return is_array($list) ? $list : [];
}

$pageTitle = $course['title'];
$breadcrumbs = ["Courses" => "courses.php"];
$breadcrumbActive = "Details";
include 'includes/breadcrumb.php';
?>

<div class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <img src="<?php echo $course['image'] ? $course['image'] : 'https://placehold.co/800x400?text=Course'; ?>"
                    alt="Course Image" class="img-fluid rounded mb-4 w-100">

                <h3 class="mb-3">පාඨමාලාව පිළිබඳව හැඳින්වීම</h3>
                <div class="mb-4">
                    <?php echo nl2br($course['intro_text']); ?>
                </div>

                <?php if (!empty($modules)): ?>
                    <h4 class="mt-4 mb-3">ඔබට පුහුණුව ලැබිය හැකි අංශ</h4>
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach ($modules as $module): ?>
                            <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> <?php echo $module; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (!empty($careers)): ?>
                    <h4 class="mt-4 mb-3">Career Opportunities</h4>
                    <p>Graduates can pursue careers as:</p>
                    <ul>
                        <?php foreach ($careers as $career): ?>
                            <li><?php echo $career; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary-custom text-white">
                        <h5 class="mb-0">Course Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3"><strong><i class="fas fa-tag me-2 text-danger"></i> වර්ගය:</strong><br>
                                <?php echo $course['course_type']; ?></li>
                            <li class="mb-3"><strong><i class="far fa-clock me-2 text-danger"></i> කාල
                                    සීමාව:</strong><br>
                                <?php echo $course['duration']; ?></li>
                            <li class="mb-3"><strong><i class="fas fa-graduation-cap me-2 text-danger"></i>
                                    Level:</strong><br>
                                NVQ Level <?php echo $course['nvq_level']; ?>
                            </li>
                            <li class="mb-3"><strong><i class="fas fa-language me-2 text-danger"></i>
                                    මාධ්‍යය:</strong><br>
                                <?php echo $course['medium']; ?></li>
                            <li class="mb-3"><strong><i class="fas fa-calendar-alt me-2 text-danger"></i> බඳවා
                                    ගැනීම:</strong><br>
                                <?php echo $course['intake']; ?></li>
                            <li class="mb-3"><strong><i class="fas fa-money-bill-wave me-2 text-danger"></i>
                                    ගාස්තුව:</strong><br>
                                <?php echo $course['fee']; ?></li>
                        </ul>
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-primary-custom">Download Brochure</button>
                            <a href="contact.php" class="btn btn-outline-danger">Contact for Admission</a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($requirements)): ?>
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Entry Requirements</h5>
                        </div>
                        <div class="card-body">
                            <ul>
                                <?php foreach ($requirements as $req): ?>
                                    <li><?php echo $req; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $conn->close(); ?>

<?php include 'includes/footer.php'; ?>