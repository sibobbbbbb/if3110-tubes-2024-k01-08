<?php

use src\dao\ApplicationStatus;
use src\dao\JobType;
use src\dao\LocationType;

?>

<!-- For job lists -->
<main class="container">
    <section class="container__card">
        <!-- Title -->
        <header class="card__header">
            <!-- Back button -->
            <a href="/company/jobs" class="header__back">
                <svg class="back__icon" xmlns="hsttp://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>

                <span>Back</span>
            </a>


            <div class="header__group">
                <!-- Title -->
                <h1 class="header__title">
                    <?= htmlspecialchars($job->getPosition()) ?>
                </h1>

                <!-- Tags -->
                <div class="header__tags">
                    <!-- Open or Closed -->
                    <?php if ($job->getIsOpen()): ?>
                        <div class="badge badge--green">
                            Open
                        </div>
                    <?php else: ?>
                        <div class="badge badge--destructive">
                            Closed
                        </div>
                    <?php endif; ?>


                    <!-- Job type -->
                    <div class="badge badge--outline">
                        <?= htmlspecialchars(JobType::renderText($job->getJobType())) ?>
                    </div>

                    <!-- Location Type -->
                    <div class="badge badge--secondary">
                        <?= htmlspecialchars(LocationType::renderText($job->getLocationType())) ?>
                    </div>
                </div>

                <!-- Created at -->
                <div class="header__information">
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

        <!-- Application List -->
        <?php if (empty($applications)): ?>
            <!-- Empty state -->
            <div class="application-list--empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" x2="12" y1="8" y2="12" />
                    <line x1="12" x2="12.01" y1="16" y2="16" />
                </svg>
                <p class="application-list--empty__text">
                    No Jobs Found
                </p>
            </div>
        <?php else: ?>
            <!-- Non empty -->
            <ul class="application-list">
                <?php foreach ($applications as $app): ?>
                    <li>
                        <article class="application-article">
                            <!-- Header -->
                            <header class="application-article__header">
                                <!-- Title -->
                                <h3 class="application-article__title">
                                    <?= htmlspecialchars($app->getUser()->getEmail()) ?>
                                </h3>

                                <!-- Applied at -->
                                <p class="application-article__description">
                                    Applied at <?= htmlspecialchars($app->getCreatedAt()->format('m/d/Y')) ?>
                                </p>
                            </header>

                            <!-- Actions -->
                            <div class="application-article__actions">
                                <!-- Tags -->
                                <div>
                                    <?php if ($app->getStatus() == ApplicationStatus::ACCEPTED): ?>
                                        <div class="badge badge--green">
                                            Accepted
                                        </div>
                                    <?php elseif ($app->getStatus() == ApplicationStatus::REJECTED): ?>
                                        <div class="badge badge--destructive">
                                            Rejected
                                        </div>
                                    <?php else: ?>
                                        <div class="badge badge--yellow">
                                            Pending
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- View -->
                                <a href="/company/jobs/<?= htmlspecialchars($app->getJobId(), ENT_QUOTES, 'utf-8') ?>/applications/<?= htmlspecialchars($app->getApplicationId(), ENT_QUOTES, 'utf-8') ?>">
                                    <button class="button button--outline button--sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye icon--sm--margin">
                                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <span>
                                            Review
                                        </span>
                                    </button>
                                </a>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Pagination -->
            <div class="application-list__pagination">
                <?= $paginationComponent ?>
            </div>
        <?php endif; ?>
    </section>
</main>