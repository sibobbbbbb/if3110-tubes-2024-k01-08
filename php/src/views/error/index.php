<main class="main">
    <div class="error-container">
        <!-- Error Icon -->
        <div class="error-icon"></div>
        <h1 class="status-code"><?= $statusCode ?></h1>
        <p class="sub-heading"><?= $subHeading ?></p>
        <p class="message"><?= $message ?></p>

        <!-- Error Button -->
        <?php if ($statusCode === 500): ?>
            <button class="go-back-btn" onclick="window.location.href=window.location.pathname">Refresh</button>
        <?php else: ?>
            <button class="go-back-btn" onclick="window.location.href='/'">Go to Home</button>
        <?php endif; ?>
    </div>
</main>