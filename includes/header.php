<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? getPageTitle($pageTitle) : $siteName; ?>
    </title>
    <meta name="description"
        content="Welcome to the Light Engineering Training Institute Ahangama. Empowering students with technical skills for a brighter future.">

    <?php include 'css-links-inc.php'; ?>
</head>

<body>

    <!-- Top Bar -->
    <div class="bg-primary-custom text-white py-2">
        <div class="container d-flex justify-content-between align-items-center top-bar-content">
            <small><i class="fas fa-envelope me-2"></i> lightengineering612@gmail.com</small>
            <small><i class="fas fa-phone me-2"></i> +9411 234 5678</small>
        </div>
    </div>

    <?php include 'navbar.php'; ?>