<?php

use src\dao\UserRole;
use src\utils\UserSession;

$heroButtonAnchorHref = "/jobs";
$heroButtonText = "Search For Jobs";
if (UserSession::isLoggedIn() && UserSession::getUserRole() === UserRole::COMPANY) {
    $heroButtonAnchorHref = "/company/jobs";
    $heroButtonText = "Manage Jobs Posts";
}
?>

<!-- Content -->
<main class="container">
    <!-- Hero -->
    <section class="hero">
        <h1 class="hero__title">Find Your Purr-fect Job</h1>

        <p class="hero__description">Link to success through global networking and professional insights</p>

        <a href="<?= htmlspecialchars($heroButtonAnchorHref, ENT_QUOTES, 'utf-8') ?>">
            <button type="button" class="button button--lg button--default-color sidebar__action-button">
                <?= htmlspecialchars($heroButtonText) ?>
            </button>
        </a>
    </section>

    <!-- Features -->
    <section class="features">
        <!-- Title -->
        <h2 class="features__title">Why Choose LinkInPurry?</h2>

        <!-- Cards Features -->
        <ul class="features__list">
            <li class="features__item">
                <div class="feature__icon">💼</div>
                <h3 class="feature__title">Exclusive Industry Jobs</h3>
                <p class="feature__description">Access a wide range of opportunities for all professionals.</p>
            </li>

            <li class="features__item">
                <div class="feature__icon">👥</div>
                <h3 class="feature__title">Hire The Best</h3>
                <p class="feature__description">
                    Find the best talent for your company with our advanced search tools.
                </p>
            </li>

            <li class="features__item">
                <div class="feature__icon">🏢</div>
                <h3 class="feature__title">Applicant Management</h3>
                <p class="feature__description">Powerful tools for companies to manage and evaluate applications.</p>
            </li>
        </ul>
    </section>
</main>