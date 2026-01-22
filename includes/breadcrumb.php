<?php
/**
 * Reusable Breadcrumb Banner
 * $pageTitle: The heading to display in the banner.
 * $breadcrumbs: Optional array of breadcrumb items [label => url].
 *              If not provided, defaults to Home > $pageTitle.
 */
?>
<div class="bg-primary-custom text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 font-weight-bold" style="color:white;">
            <?php echo $pageTitle; ?>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Home</a></li>

                <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $label => $url): ?>
                        <li class="breadcrumb-item"><a href="<?php echo $url; ?>" class="text-white text-decoration-none">
                                <?php echo $label; ?>
                            </a></li>
                    <?php endforeach; ?>
                <?php endif; ?>

                <li class="breadcrumb-item active text-white-50" aria-current="page">
                    <?php echo isset($breadcrumbActive) ? $breadcrumbActive : $pageTitle; ?>
                </li>
            </ol>
        </nav>
    </div>
</div>