<!-- Navigation -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/images/logo.png" alt="LETI Logo" height="60" class="me-2">
            <span class="brand-text">LETI Ahangama</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?php echo ($currentPage == 'home') ? 'active' : ''; ?>"
                        href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($currentPage == 'about') ? 'active' : ''; ?>"
                        href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($currentPage == 'courses') ? 'active' : ''; ?>"
                        href="courses.php">Courses</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($currentPage == 'news') ? 'active' : ''; ?>"
                        href="news.php">News</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($currentPage == 'contact') ? 'active' : ''; ?>"
                        href="contact.php">Contact Us</a></li>
            </ul>
            <a href="courses.php" class="btn btn-primary-custom ms-lg-3 d-none d-lg-block">Apply Now</a>
        </div>
    </div>
</nav>