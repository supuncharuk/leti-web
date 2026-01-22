<?php
$pageTitle = "Manage Courses";
$currentPage = "courses";
require_once __DIR__ . '/includes/header.php';

$conn = getDbConnection();
$message = '';
$messageType = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Also delete image if exists
    $stmt = $conn->prepare("SELECT image FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $img = $stmt->get_result()->fetch_assoc()['image'];
    if ($img && file_exists(__DIR__ . '/../' . $img)) {
        unlink(__DIR__ . '/../' . $img);
    }

    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Course deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_course'])) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = trim($_POST['title']);
    $intro_text = $_POST['intro_text'];
    $duration = $_POST['duration'];
    $nvq_level = $_POST['nvq_level'];
    $medium = $_POST['medium'];
    $intake = $_POST['intake'];
    $fee = $_POST['fee'];

    // Process Lists (Modules, Careers, Requirements) - Store as JSON
    $modules = json_encode(array_filter(array_map('trim', explode("\n", $_POST['modules']))));
    $career_opps = json_encode(array_filter(array_map('trim', explode("\n", $_POST['career_opportunities']))));
    $requirements = json_encode(array_filter(array_map('trim', explode("\n", $_POST['entry_requirements']))));

    $image_path = $_POST['existing_image'];

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/img/courses/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                unlink(__DIR__ . '/../' . $image_path);
            }
            $image_path = "assets/img/courses/" . $file_name;
        }
    }

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE courses SET title = ?, image = ?, intro_text = ?, course_type = ?, modules = ?, career_opportunities = ?, duration = ?, nvq_level = ?, medium = ?, intake = ?, fee = ?, entry_requirements = ?, updated_by = ? WHERE id = ?");
        $user_id = $_SESSION['user_id'];
        $stmt->bind_param("ssssssssssssii", $title, $image_path, $intro_text, $_POST['course_type'], $modules, $career_opps, $duration, $nvq_level, $medium, $intake, $fee, $requirements, $user_id, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (title, image, intro_text, course_type, modules, career_opportunities, duration, nvq_level, medium, intake, fee, entry_requirements, added_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $user_id = $_SESSION['user_id'];
        $stmt->bind_param("ssssssssssssii", $title, $image_path, $intro_text, $_POST['course_type'], $modules, $career_opps, $duration, $nvq_level, $medium, $intake, $fee, $requirements, $user_id, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Course saved successfully!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Fetch all courses with user info
$courses = $conn->query("
    SELECT c.*, u1.username AS added_by_user, u2.username AS updated_by_user 
    FROM courses c 
    LEFT JOIN users u1 ON c.added_by = u1.id 
    LEFT JOIN users u2 ON c.updated_by = u2.id 
    ORDER BY c.title ASC
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Manage Courses</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal" onclick="resetForm()">
        <i class="fas fa-plus"></i> Add New Course
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th width="20%">Image</th>
                        <th>Course Title</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Level</th>
                        <?php if (isAdmin()): ?>
                            <th>Added</th>
                            <th>Last Updated</th>
                        <?php endif; ?>
                        <th colspan="2" width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($courses->num_rows > 0): ?>
                        <?php while ($row = $courses->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($row['image']): ?>
                                        <img src="../<?php echo $row['image']; ?>" class="rounded" width="60" alt="">
                                    <?php else: ?>
                                        <img src="assets/images/60.svg" class="rounded" alt="">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $row['title']; ?>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge <?php echo $row['course_type'] == 'Full Time' ? 'bg-success' : 'bg-info text-dark'; ?>">
                                        <?php echo $row['course_type']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php echo $row['duration']; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $row['nvq_level']; ?>
                                </td>
                                <?php if (isAdmin()): ?>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['added_by'] ? htmlspecialchars($row['added_by_user']) : '-'; ?>
                                        </small>
                                        <?php if ($row['created_at']): ?>
                                            <br><span
                                                style="font-size: 0.8em;"><?php echo date('Y M d, H:i', strtotime($row['created_at'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo $row['updated_by'] ? htmlspecialchars($row['updated_by_user']) : '-'; ?>
                                        </small>
                                        <?php if ($row['updated_at']): ?>
                                            <br><span
                                                style="font-size: 0.8em;"><?php echo date('Y M d, H:i', strtotime($row['updated_at'])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick='editCourse(<?php echo $row['id']; ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(<?php echo $row['id']; ?>, 'This course and its details will be permanently removed!')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="courseId">
                    <input type="hidden" name="existing_image" id="existingImage">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Course Title</label>
                                        <input type="text" name="title" id="courseTitle" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="course_type" class="form-label">Course Type</label>
                                        <select name="course_type" id="courseType" class="form-select" required>
                                            <option value="Full Time">Full Time</option>
                                            <option value="Part Time">Part Time</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Course Image</label>
                                <input type="file" name="image" id="courseImage" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="intro_text" class="form-label">Introduction Text</label>
                                <textarea name="intro_text" id="courseIntro" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 text-dark p-3 rounded" style="background: #f1f4f9;">
                            <h6 class="fw-bold mb-3 border-bottom pb-2">Quick Info</h6>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label small mb-1">Duration</label>
                                    <input type="text" name="duration" id="courseDuration"
                                        class="form-control form-control-sm" placeholder="e.g. 1 Year (Full-time)">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label small mb-1">NVQ Level</label>
                                    <input type="text" name="nvq_level" id="courseLevel"
                                        class="form-control form-control-sm" placeholder="e.g. 4, 3">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label small mb-1">Medium</label>
                                    <input type="text" name="medium" id="courseMedium"
                                        class="form-control form-control-sm" placeholder="e.g. Sinhala">
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label small mb-1">Intake</label>
                                    <input type="text" name="intake" id="courseIntake"
                                        class="form-control form-control-sm" placeholder="e.g. January">
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label small mb-1">Fee</label>
                                    <input type="text" name="fee" id="courseFee" class="form-control form-control-sm"
                                        placeholder="e.g. Free">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Course Modules (One per line)</label>
                            <textarea name="modules" id="courseModules" class="form-control" rows="6"
                                placeholder="M01 - Safety&#10;M02 - Calculation..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Career Opportunities (One per line)</label>
                            <textarea name="career_opportunities" id="courseCareers" class="form-control" rows="6"
                                placeholder="Mechanic&#10;Supervisor..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Entry Requirements (One per line)</label>
                            <textarea name="entry_requirements" id="courseRequirements" class="form-control" rows="6"
                                placeholder="O/L Examination&#10;Age 16-30..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_course" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Add Course';
        document.getElementById('courseId').value = '';
        document.getElementById('existingImage').value = '';
        document.getElementById('courseTitle').value = '';
        document.getElementById('courseType').value = 'Full Time';
        document.getElementById('courseIntro').value = '';
        document.getElementById('courseDuration').value = '';
        document.getElementById('courseLevel').value = '';
        document.getElementById('courseMedium').value = '';
        document.getElementById('courseIntake').value = '';
        document.getElementById('courseFee').value = '';
        document.getElementById('courseModules').value = '';
        document.getElementById('courseCareers').value = '';
        document.getElementById('courseRequirements').value = '';
    }

    function editCourse(id) {
        document.getElementById('modalTitle').innerText = 'Loading...';
        new bootstrap.Modal(document.getElementById('courseModal')).show();

        $.ajax({
            url: 'ajax/get-course.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const data = response.data;
                    document.getElementById('modalTitle').innerText = 'Edit Course';
                    document.getElementById('courseId').value = data.id;
                    document.getElementById('existingImage').value = data.image || '';
                    document.getElementById('courseTitle').value = data.title;
                    document.getElementById('courseType').value = data.course_type;
                    document.getElementById('courseIntro').value = data.intro_text;
                    document.getElementById('courseDuration').value = data.duration;
                    document.getElementById('courseLevel').value = data.nvq_level;
                    document.getElementById('courseMedium').value = data.medium;
                    document.getElementById('courseIntake').value = data.intake;
                    document.getElementById('courseFee').value = data.fee;
                    document.getElementById('courseModules').value = '';
                    document.getElementById('courseCareers').value = '';
                    document.getElementById('courseRequirements').value = '';

                    // Parse JSON lists back to newlines for textareas
                    try {
                        const modules = JSON.parse(data.modules || '[]');
                        document.getElementById('courseModules').value = modules.join('\n');

                        const careers = JSON.parse(data.career_opportunities || '[]');
                        document.getElementById('courseCareers').value = careers.join('\n');

                        const requirements = JSON.parse(data.entry_requirements || '[]');
                        document.getElementById('courseRequirements').value = requirements.join('\n');
                    } catch (e) {
                        console.error("Error parsing JSON lists", e);
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function () {
                alert('An error occurred while fetching course details.');
            }
        });
    }

    <?php if ($message): ?>
        window.addEventListener('load', function () {
            showAlert('<?php echo $message; ?>', '<?php echo $messageType; ?>', '', <?php echo $messageType === 'error' ? 'false' : 'true'; ?>);
            <?php if ($messageType === 'error'): ?>
                new bootstrap.Modal(document.getElementById('courseModal')).show();
            <?php endif; ?>
        });
    <?php endif; ?>
</script>

<?php
$conn->close();
require_once __DIR__ . '/includes/footer.php';
?>