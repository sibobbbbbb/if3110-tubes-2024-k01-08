<?php

use src\dao\UserRole;
use src\utils\UserSession;

// Determine search form action
$searchFormAction = "/jobs";
if (UserSession::isLoggedIn()) {
    $userRole = UserSession::getUserRole();
    if ($userRole == UserRole::JOBSEEKER) {
        $searchFormAction = "/jobs";
    } else if ($userRole == UserRole::COMPANY) {
        $searchFormAction = "/company/jobs";
    }
}
?>

<header class="header">
    <div class="header__content">
        <div class="header__logo-and-search">
            <!-- Logo -->
            <a href="/" class="header__logo">
                <svg
                    class="logo__icon"
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cat">
                    <path d="M12 5c.67 0 1.35.09 2 .26 1.78-2 5.03-2.84 6.42-2.26 1.4.58-.42 7-.42 7 .57 1.07 1 2.24 1 3.44C21 17.9 16.97 21 12 21s-9-3-9-7.56c0-1.25.5-2.4 1-3.44 0 0-1.89-6.42-.5-7 1.39-.58 4.72.23 6.5 2.23A9.04 9.04 0 0 1 12 5Z" />
                    <path d="M8 14v.5" />
                    <path d="M16 14v.5" />
                    <path d="M11.25 16.25h1.5L12 17l-.75-.75Z" />
                </svg>
                <span class="logo__text">
                    LinkInPurry
                </span>
            </a>

            <!-- Search input -->
            <search class="header__search">
                <form id="navbar-search-form" action="<?= htmlspecialchars($searchFormAction) ?>" method="GET" class="header__form-search">
                    <svg class="header__search-logo" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.3-4.3" />
                    </svg>

                    <input id="navbar-search-input" type="text" name="search" class="input header__search-input" placeholder="Search for jobs"
                        <?php if (isset($filters['search'])) : ?>
                        value="<?= htmlspecialchars($filters['search'], ENT_QUOTES, 'utf-8') ?>"
                        <?php else: ?>
                        value=""
                        <?php endif; ?> />
                </form>
            </search>
        </div>

        <!-- Sidebar Open Button -->
        <button id="sidebar-open-button" aria-label="Open Navigation Bar" class="header__menu-button">
            <svg class="header__menu-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12" />
                <line x1="4" x2="20" y1="6" y2="6" />
                <line x1="4" x2="20" y1="18" y2="18" />
            </svg>
        </button>

        <!-- Sidebar -->
        <div id="sidebar-container" class="sidebar">
            <!-- Close -->
            <button id="sidebar-close-button" aria-label="Close Navigation Bar" class="button button--icon button--ghost">
                <svg class="sidebar__close-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18" />
                    <path d="m6 6 12 12" />
                </svg>
            </button>

            <nav class="nav">
                <!-- Nav links -->
                <?php if (!UserSession::isLoggedIn()): ?>
                    <ul id="navbar-links-ul">
                        <li class="nav__item">
                            <a href="/jobs" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase-business nav__icon">
                                    <path d="M12 12h.01" />
                                    <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                    <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                                    <rect width="20" height="14" x="2" y="6" rx="2" />
                                </svg>
                                <span>
                                    Jobs
                                </span>
                            </a>
                        </li>
                    </ul>
                <?php elseif (UserSession::getUserRole() == UserRole::JOBSEEKER) : ?>
                    <ul id="navbar-links-ul" class="nav__list">
                        <li class="nav__item">
                            <a href="/jobs" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase-business nav__icon">
                                    <path d="M12 12h.01" />
                                    <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                    <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                                    <rect width="20" height="14" x="2" y="6" rx="2" />
                                </svg>
                                <span>
                                    Jobs
                                </span>
                            </a>
                        </li>

                        <li class="nav__item">
                            <a href="/history" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-clock nav__icon">
                                    <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v3" />
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                                    <circle cx="8" cy="16" r="6" />
                                    <path d="M9.5 17.5 8 16.25V14" />
                                </svg>
                                <span>
                                    History
                                </span>
                            </a>
                        </li>
                        <li class="nav__item">
                            <a href="/suggestion" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-pen nav__icon">
                                    <path d="M2 21a8 8 0 0 1 10.821-7.487" />
                                    <path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                                    <circle cx="10" cy="8" r="5" />
                                </svg>
                                <span>
                                    Suggestion
                                </span>
                            </a>
                        </li>
                    </ul>
                <?php elseif (UserSession::getUserRole() == UserRole::COMPANY) : ?>
                    <ul id="navbar-links-ul" class="nav__list">
                        <li class="nav__item">
                            <a href="/company/jobs" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase-business nav__icon">
                                    <path d="M12 12h.01" />
                                    <path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                    <path d="M22 13a18.15 18.15 0 0 1-20 0" />
                                    <rect width="20" height="14" x="2" y="6" rx="2" />
                                </svg>
                                <span>
                                    Jobs
                                </span>
                            </a>
                        </li>

                        <li class="nav__item">
                            <a href="/company/profile" class="nav__link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 nav__icon">
                                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                                    <path d="M10 6h4" />
                                    <path d="M10 10h4" />
                                    <path d="M10 14h4" />
                                    <path d="M10 18h4" />
                                </svg>
                                <span>
                                    Profile
                                </span>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>

                <!-- Auth -->
                <div class="nav__auth">
                    <?php if (UserSession::isLoggedIn()): ?>
                        <button type="button" id="sign-out-button" class="button button--default-size button--destructive sidebar__action-button">Sign Out</button>
                    <?php else: ?>
                        <a href="/auth/sign-in">
                            <button type="button" class="button button--default-size button--default-color sidebar__action-button">Sign In</button>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>

    <div id="sidebar-blur" class="sidebar__blur"></div>
</header>