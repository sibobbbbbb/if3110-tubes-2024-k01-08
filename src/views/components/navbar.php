<?php

use src\dao\UserRole;
use src\utils\UserSession;

$isUserLoggedIn = false;
?>

<header class="header">
    <!-- Logo -->
    <a href="/" class="header__home">
        <svg
            class="header__home-icon"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cat">
            <path d="M12 5c.67 0 1.35.09 2 .26 1.78-2 5.03-2.84 6.42-2.26 1.4.58-.42 7-.42 7 .57 1.07 1 2.24 1 3.44C21 17.9 16.97 21 12 21s-9-3-9-7.56c0-1.25.5-2.4 1-3.44 0 0-1.89-6.42-.5-7 1.39-.58 4.72.23 6.5 2.23A9.04 9.04 0 0 1 12 5Z" />
            <path d="M8 14v.5" />
            <path d="M16 14v.5" />
            <path d="M11.25 16.25h1.5L12 17l-.75-.75Z" />
        </svg>
        <span>
            LinkInPurry
        </span>
    </a>

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
                <ul id="navbar-links-ul" class="nav__list">
                    <li class="nav__item">
                        <a href="/jobs" class="nav__link">Find Jobs</a>
                    </li>
                </ul>
            <?php elseif (UserSession::getUserRole() == UserRole::JOBSEEKER) : ?>
                <ul id="navbar-links-ul" class="nav__list">
                    <li class="nav__item">
                        <a href="/jobs" class="nav__link">Find Jobs</a>
                    </li>

                    <li class="nav__item">
                        <a href="/jobs/applications" class="nav__link">My Applications</a>
                    </li>
                </ul>
            <?php elseif (UserSession::getUserRole() == UserRole::COMPANY) : ?>
                <ul id="navbar-links-ul" class="nav__list">
                    <li class="nav__item">
                        <a href="/company/dashboard" class="nav__link">Dashboard</a>
                    </li>

                    <li class="nav__item">
                        <a href="/company/profile" class="nav__link">Profile</a>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- Auth -->
            <div>
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

    <div id="sidebar-blur" class="sidebar__blur"></div>
</header>