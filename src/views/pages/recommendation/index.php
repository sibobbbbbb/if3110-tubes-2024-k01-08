<?php

use src\dao\JobType;
use src\dao\LocationType;

?>

<!-- For job lists -->
<main class="container">
    <section class="container__card">
        <!-- Title -->
        <header class="card__header">
            <!-- Back button -->
            <a href="javascript:history.back()" class="header__back">
                <svg class="back__icon" xmlns="hsttp://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>

                <span>Back</span>
            </a>


            <div class="header__group">
                <!-- Title -->
                <h1 class="header__title">
                    Jobs Recommendation
                </h1>

                <!-- Description -->
                <p class="header__information">
                    View all jobs recommendations here just for you.
                </p>
            </div>
        </header>

        <!-- Job lists -->
        <?php if (empty($jobs)): ?>
            <!-- Empty state -->
            <div class="job-list--empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" x2="12" y1="8" y2="12" />
                    <line x1="12" x2="12.01" y1="16" y2="16" />
                </svg>
                <p class="job-list--empty__text">
                    No Jobs Found
                </p>
            </div>
        <?php else: ?>
            <!-- Non empty -->
            <ul class="job-list" id="job-list">
                <?php foreach ($jobs as $job): ?>
                    <li>
                        <article class="job-article">
                            <!-- Header -->
                            <header class="job-article__header">
                                <!-- Title -->
                                <h3 class="job-article__title">
                                    <?= htmlspecialchars($job->getPosition()) ?>
                                </h3>

                                <!-- Tags -->
                                <div class="job-article__tags">
                                    <div class="badge badge--default">
                                        <?= htmlspecialchars(JobType::renderText($job->getJobType())) ?>
                                    </div>

                                    <div class="badge badge--secondary">
                                        <?= htmlspecialchars(LocationType::renderText($job->getLocationType())) ?>
                                    </div>
                                </div>

                                <div class="job-article__informations">
                                    <!-- Company -->
                                    <div class="job-article__information">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 icon--sm--margin">
                                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                                            <path d="M10 6h4" />
                                            <path d="M10 10h4" />
                                            <path d="M10 14h4" />
                                            <path d="M10 18h4" />
                                        </svg>

                                        <span>
                                            <?= htmlspecialchars($job->getCompany()->getName()) ?>
                                        </span>
                                    </div>


                                    <!-- Created at -->
                                    <div class="job-article__information">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days icon--sm--margin">
                                            <path d="M8 2v4" />
                                            <path d="M16 2v4" />
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M3 10h18" />
                                            <path d="M8 14h.01" />
                                            <path d="M12 14h.01" />
                                            <path d="M16 14h.01" />
                                            <path d="M8 18h.01" />
                                            <path d="M12 18h.01" />
                                            <path d="M16 18h.01" />
                                        </svg>

                                        <span>
                                            <?= htmlspecialchars($job->getCreatedAt()->format('m/d/Y')) ?>
                                        </span>
                                    </div>
                                </div>
                            </header>

                            <!-- Actions -->
                            <div class="job-article__actions">
                                <!-- View -->
                                <a href="/jobs/<?= htmlspecialchars($job->getJobId(), ENT_QUOTES, 'utf-8') ?>">
                                    <button class="button button--outline button--sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye icon--sm--margin">
                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span>
                                            View
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
</main>