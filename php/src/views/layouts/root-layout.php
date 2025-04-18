<!DOCTYPE html>

<html lang="en">

<head>
    <!-- Viewport -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="color-scheme" content="light" />
    <meta name="theme-color" content="white" />

    <!-- SEO -->
    <title>
        <?php echo $title ?>
    </title>
    <meta
        name="description"
        content="<?php echo $description ?>" />
    <meta name="application-name" content="LinkInPurry" />
    <meta name="keywords" content="LinkInPurry, Job Market" />
    <meta name="category" content="job-market" />

    <!-- Icon -->
    <link
        rel="icon"
        type="image/x-icon"
        href="/favicon.ico"
        sizes="48x48" />
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.webmanifest" />

    <!-- Opengraph -->
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta
        property="og:description"
        content="<?php echo $description ?>" />
    <meta property="og:site_name" content="LinkInPurry" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:image" content="/link-preview.jpg" />
    <meta property="og:image:alt" content="LinkInPurry Preview" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:type" content="website" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta
        name="twitter:description"
        content="<?php echo $description ?>" />
    <meta name="twitter:image" content="/link-preview.jpg" />
    <meta name="twitter:image:alt" content="LinkInPurry Preview" />

    <!-- Global Styles -->
    <link rel="stylesheet" href="/styles/global.css" />

    <!-- Global scripts -->
    <script src="/scripts/global.js" defer></script>

    <!-- Additional tags -->
    <?= $additionalTags ?>

    <!-- Global Font (Inter)-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap"
        media="print"
        onload="this.media='all'" />
    <noscript>
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" />
    </noscript>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= \src\utils\CSRFHandler::generateToken() ?>">
</head>

<body>
    <!-- Navbar -->
    <?php
    // Check if the navbar is available
    $navbarPath = __DIR__ . '/../components/navbar.php';
    if (file_exists($navbarPath)) {
        require $navbarPath;
    }
    ?>

    <!-- Main Content -->
    <?= $content ?>

    <!-- Footer -->
    <?php
    $footerPath = __DIR__ . '/../components/footer.php';
    if (file_exists($footerPath)) {
        require $footerPath;
    }
    ?>
</body>

</html>