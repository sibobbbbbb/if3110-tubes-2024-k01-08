<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <?= $additionalTags ?? '' ?>
</head>
<body>
    <main class="main">
        <div class="error-container">
            <div class="error-icon"></div>
            <h1><?= $statusCode ?></h1>
            <p><?= $subHeading ?></p>
            <p><?= $message ?></p>

            <?php if ($statusCode === 404): ?>
                <button class="go-back-btn" onclick="window.location.href='/home'">Go to Home</button>
            <?php else: ?>
                <button class="go-back-btn" onclick="location.reload()'">Refresh</button>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>