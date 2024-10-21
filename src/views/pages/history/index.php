<?php

use src\dao\ApplicationStatus;

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
                    Applications History
                </h1>

                <!-- Description -->
                <p class="header__information">
                    View all your job applications here.
                </p>
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
                    No Applications Found
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
                                    <?= htmlspecialchars($app->getJob()->getPosition()) ?>
                                </h3>

                                <!-- Company -->
                                <div class="application-article__information">
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
                                        <?= htmlspecialchars($app->getJob()->getCompany()->getName()) ?>
                                    </span>
                                </div>


                                <!-- Applied at -->
                                <div class="application-article__information">
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
                                        <?= htmlspecialchars($app->getCreatedAt()->format('m/d/Y')) ?>
                                    </span>
                                </div>
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
                                            View
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