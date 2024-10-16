<?php

namespace src\controllers;

use src\core\{Request, Response};
use src\dao\UserRole;
use src\dto\DtoFactory;
use src\exceptions\BadRequestHttpException;
use src\services\AuthService;
use src\utils\{UserSession, Validator};

/**
 * Controller for handling authentication
 * Request & Response is not stored as property to make it stateless & singleton (inspired by NestJS default singleton lifecycle).
 */
class AuthController extends Controller
{
    // Dependency injection
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Renders the sign in page
     * Uses php form handling (handles GET & POST requests)
     */
    public function renderAndHandleSignIn(Request $req, Response $res): void
    {
        // Redirect if user is authenticated
        $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'auth/sign-in/index.php';

        // Data to pass to the view
        $title = 'LinkInPurry | Sign In';
        $description = 'Sign in to your LinkInPurry account';
        $linkTag = <<<HTML
                <link rel="stylesheet" href="/styles/auth/sign-in.css" />
            HTML;
        $scriptTag = <<<HTML
                <script src="/scripts/auth/sign-in.js" defer></script>
            HTML;
        $additionalTags = [$linkTag, $scriptTag];
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags,
        ];

        if ($req->getMethod() == "GET") {
            // Get
            $this->renderPage($viewPathFromPages, $data);
        } else {
            // Post
            $email = $req->getBody()['email'];
            $password = $req->getBody()['password'];

            $rules = [
                'email' => ['required', 'email'],
                'password' => ['required']
            ];

            $validator = new Validator();
            $isValid = $validator->validate($req->getBody(), $rules);
            // Invalid request body
            if (!$isValid) {
                $data['errorFields'] = $validator->getErrorFields();
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
                return;
            }

            try {
                // Authenticate the user
                $user = $this->authService->signIn($email, $password);
            } catch (BadRequestHttpException $e) {
                // Failed to authenticate
                $message = $e->getMessage();
                $data['errorFields'] = [
                    'email' => [$message],
                    'password' => [$message],
                ];
                $data['fields'] = $req->getBody();
                $this->renderPage($viewPathFromPages, $data);
                return;
            }

            // Set the user in the session
            UserSession::setUser($user);

            // If valid, redirect to the dashboard
            $this->redirectIfAuthenticated($req, $res);
        }
    }

    /**
     * Handles the sign out endpoint
     */
    public function handleSignOut(Request $req, Response $res): void
    {
        // Redirect if user is not authenticated
        if (!UserSession::isLoggedIn()) {
            $res->redirect('/');
            return;
        }

        // Sign out the user
        UserSession::destroy();

        // Redirect to the home page
        $responseData = DtoFactory::createSuccessDto('Sign out successful');
        $res->json(200, $responseData);
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
        if (UserSession::isLoggedIn()) {
            if (UserSession::getUserRole() === UserRole::JOBSEEKER) {
                // If job seeker
                $res->redirect('/jobs');
                return;
            } else {
                // If employer
                $res->redirect('/company/dashboard');
                return;
            }
        }
    }
}
