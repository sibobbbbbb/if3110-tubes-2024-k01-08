<!-- Additional tags -->
<?= $additionalTags ?>

<main class="main">
    <div class="error-container">
        <!-- Error Icon -->
        <div class="error-icon"></div>
        <h1><?= $statusCode ?></h1>
        <p><?= $subHeading ?></p>
        <p><?= $message ?></p>

        <!-- Error Button -->
        <?php if ($statusCode === 404): ?>
            <button class="go-back-btn" onclick="window.location.href='/'">Go to Home</button>
        <?php else: ?>
            <button class="go-back-btn" onclick="location.reload()">Refresh</button>
        <?php endif; ?>
    </div>
</main>