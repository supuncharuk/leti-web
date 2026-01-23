<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/auth.php';
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle : 'Admin Panel'; ?> - LETI Ahangama
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Summernote Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>




    <link href="assets/css/styles.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">
</head>

<body>

    <div id="sidebar-overlay"></div>

    <div id="sidebar">
        <div class="sidebar-header text-center position-relative">
            <button id="close-sidebar" title="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
            <h4 class="mb-0">LETI Admin</h4>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'courses') ? 'active' : ''; ?>"
                    href="manage-courses.php">
                    <i class="fas fa-graduation-cap"></i> Manage Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'categories') ? 'active' : ''; ?>"
                    href="manage-categories.php">
                    <i class="fas fa-tags"></i> News Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'news') ? 'active' : ''; ?>" href="manage-news.php">
                    <i class="fas fa-newspaper"></i> Manage News
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($currentPage == 'gallery') ? 'active' : ''; ?>" href="manage-gallery.php">
                    <i class="fas fa-images"></i> Manage Gallery
                </a>
            </li>
            <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($currentPage == 'users') ? 'active' : ''; ?>" href="manage-users.php">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item mt-5">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4 rounded">
            <div class="container-fluid">
                <button type="button" id="toggle-sidebar" class="btn btn-outline-primary me-3">
                    <i class="fas fa-bars" id="toggle-icon"></i>
                </button>
                <span class="navbar-text fw-bold">
                    Welcome,
                    <?php echo $_SESSION['user_name']; ?>!
                </span>
                <div class="ms-auto">
                    <a href="../index.php" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a>
                </div>
            </div>
        </nav>