<?php

namespace src\controllers;

use src\core\{Request, Response};
use src\dao\UserRole;

/**
 * Controller for handling authentication
 * Request & Response is not stored as property to make it stateless & singleton (inspired by NestJS default singleton lifecycle).
 */
class AuthController extends Controller
{
    /**
     * Renders the sign in page
     */
    public function renderSignIn(Request $req, Response $res): void
    {
        // Redirect if user is authenticated
        $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'auth/sign-in/index.php';
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/auth/sign-in.css" />
            HTML;
        $scriptTag = <<<HTML
                <script src="/scripts/auth/sign-in.js" defer></script>
            HTML;

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | Sign In';
        $description = 'Sign in to your LinkInPurry account';
        $additionalTags = [$linkTag, $scriptTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags,
        ];

        $this->renderPage($viewPathFromPages, $data);
    }

    /**
     * Renders the sign up page
     */
    public function renderSignUp(Request $req, Response $res): void
    {
        // Redirect if user is authenticated
        $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'auth/sign-up/index.php';
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/auth/sign-up.css" />
            HTML;

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | Sign Up';
        $description = 'Sign up for a LinkInPurry account';
        $additionalTags = [$linkTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        $this->renderPage($viewPathFromPages, $data);
    }

    /**
     * Render the sign up job seeker page
     */
    public function renderSignUpJobSeeker(Request $req, Response $res): void
    {
        // Redirect if user is authenticated
        $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'auth/sign-up/job-seeker/index.php';
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/auth/sign-up/job-seeker.css" />
            HTML;
        $scriptTag = <<<HTML
                <script src="/scripts/auth/sign-up/job-seeker.js" defer></script>
            HTML;

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | Job Seeker Sign Up';
        $description = 'Sign up for a LinkInPurry account as a job seeker';
        $additionalTags = [$linkTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        $this->renderPage($viewPathFromPages, $data);
    }

    /**
     * Render the sign up company page
     */
    public function renderSignUpCompany(Request $req, Response $res): void
    {
        // Redirect if user is authenticated
        $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'auth/sign-up/company/index.php';
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/auth/sign-up/company.css" />
            HTML;
        $scriptTag = <<<HTML
                <script src="/scripts/auth/sign-up/company.js" defer></script>
            HTML;

        // Data to pass to view (SSR)
        $title = "LinkInPurry | Company Sign Up";
        $description = "Sign up for a LinkInPurry account as a company";
        $additionalTags = [$linkTag, $scriptTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        $this->renderPage($viewPathFromPages, $data);
    }

    /**
     * Handles the sign in request
     */
    public function handleSignIn(Request $req, Response $res): void {
        // 1. Validasi input
        // 2. Panggil service
        // 3. Bikin response (redirect atau kirim json)
    }

    /**
     * Handles the sign up for job seeker endpoint 
     */
    public function handleSignUpJobSeeker(Request $req, Response $res): void {}

    /**
     * Handle the sign up for company endpoint
     */
    public function handleSignUpCompany(Request $req, Response $res): void {}

    /**
     * Redirect user if already authenticated
     */
    public function redirectIfAuthenticated(Request $req, Response $res): void
    {
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['role'] === UserRole::JOBSEEKER) {
                // If job seeker
                $res->redirect('/jobs');
                return;
            } else {
                // If employer
                $res->redirect('/dashboard');
                return;
            }
        }
    }
}
