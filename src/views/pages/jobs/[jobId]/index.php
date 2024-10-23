<?php

use src\dao\JobType;
use src\dao\LocationType;
use src\utils\UserSession;

?>

<!-- For job lists -->
<main class="container">
    <article class="container__card">
        <!-- Company, Job Title, Job Tags, created at, Apply button OR Path to CV & Video -->
        <!-- Title -->
        <header class="card__header">
            <!-- Back button -->
            <a href="/jobs" class="header__back">
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
                    <!-- Job type -->
                    <div class="badge badge--default">
                        <?= htmlspecialchars(JobType::renderText($job->getJobType())) ?>
                    </div>

                    <!-- Location Type -->
                    <div class="badge badge--secondary ">
                        <?= htmlspecialchars(LocationType::renderText($job->getLocationType())) ?>
                    </div>

                </div>

                <div class="header__informations">
                    <!-- Company -->
                    <div class="header__information">
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
                            <?= htmlspecialchars($job->getCompanyDetail()->getName()) ?>
                        </span>
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

                <!-- Application -->
                <div class="header__application">
                    <?php if (!UserSession::isLoggedIn()): ?>
                        <a href="/auth/sign-in">
                            <button class="button button--default-color button--sm rounded-full header__action-button">
                                Sign in to Apply
                            </button>
                        </a>
                    <?php elseif ($application == null): ?>
                        <a href="/jobs/<?= htmlspecialchars($job->getJobId(), ENT_QUOTES, 'utf-8') ?>/apply">
                            <button class="button button--default-color button--sm rounded-full header__action-button">
                                Apply
                            </button>
                        </a>
                    <?php else: ?>
                        <!-- <?= $application->getCvPath() ?> -->
                        <a target="_blank" href="<?= htmlspecialchars($application->getCvPath(), ENT_QUOTES, 'utf-8') ?>">
                            <button class="button button--default-color button--sm rounded-full header__action-button">
                                CV
                            </button>
                        </a>

                        <?php if ($application->getVideoPath() != null): ?>
                            <a target="_blank" href="<?= htmlspecialchars($application->getVideoPath(), ENT_QUOTES, 'utf-8') ?>">
                                <button class="button button--secondary button--sm rounded-full header__action-button">
                                    Video
                                </button>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>


        <!-- About the job -->
        <section class="about-job">
            <h2 class="content__title">About the Job</h2>

            <!-- Content -->
            <div class="content__rich-text">
                <?= $job->getDescription() ?>
            </div>

            <!-- Attachments (Carousel) -->
            <?php if (count($job->getAttachments()) > 0) : ?>
                <div class="about-job__carousel-container">
                    <div class="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($job->getAttachments()  as $attachment) : ?>
                                <div class="carousel-item">
                                    <img src="<?= htmlspecialchars($attachment->getFilePath(), ENT_QUOTES, 'utf-8') ?>" alt="Attachment <?= htmlspecialchars($attachment->getAttachmentId(), ENT_QUOTES, 'utf-8') ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control prev">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left carousel-control__icon">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                        </button>
                        <button class="carousel-control next">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right carousel-control__icon">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </button>

                        <div class="carousel-indicators"></div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- About the company -->
        <section class="about-company">
            <h2 class="content__title">
                About the Company
            </h2>

            <p class="content__text">
                <?= htmlspecialchars($job->getCompanyDetail()->getAbout()) ?>
            </p>
        </section>
    </article>
</main>