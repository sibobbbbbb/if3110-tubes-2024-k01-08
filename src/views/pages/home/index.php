<?php

use src\dao\UserRole;
use src\utils\UserSession;

// Determine search form action
$searchForJobsAction = "/jobs";
if (UserSession::isLoggedIn() && UserSession::getUserRole() === UserRole::COMPANY) {
    $searchForJobsAction = "/company/jobs";
}
// echo var_dump($searchForJobsAction);
?>

<!-- Content -->
<main class="main">
    <div class="container">
        <section class="hero">
            <h1 class="home__title">Find Your Purr-fect Job</h1>
            <h2>Link to success through global networking and professional insights</h2>
            <div class="search-bar">
                <button type="button" onclick="window.location.href='<?= $searchForJobsAction ?>'"
                    class="button button--default-size button--default-color sidebar__action-button">
                    Search For Jobs
                </button>
            </div>
        </section>
        <section>
            <h2 class="home__sub-title">Why Choose LinkInPurry?</h2>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">💼</div>
                    <h3>Exclusive Pet Industry Jobs</h3>
                    <p>Access a wide range of opportunities tailored for animal lovers and pet professionals.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">👥</div>
                    <h3>Grow Your Network</h3>
                    <p>Connect with like-minded professionals and industry leaders in the pet world.</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">💬</div>
                    <h3>Purr-sonal Messaging</h3>
                    <p>Communicate directly with employers and colleagues through our secure platform.</p>
                </div>
            </div>
        </section>
    </div>
</main>