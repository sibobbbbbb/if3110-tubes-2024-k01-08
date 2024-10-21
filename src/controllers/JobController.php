<?php

namespace src\controllers;
use src\core\{Request, Response};
class JobController extends Controller {
    public function __construct(){
        //constructor tanpa parameter
    }

    public function renderandHandleHistory(Request $req, Response $res): void
    {
        // // Redirect if user is authenticated
        // $this->redirectIfAuthenticated($req, $res);

        // Render the view
        $viewPathFromPages = 'history/index.php';

        // Data to pass to the view (SSR)
        $title = 'LinkInPurry | Sign In';
        $description = 'Sign in to your LinkInPurry account';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/history/history.css" />
                <script src="/scripts/auth/sign-up/job-seeker.js" defer></script>
            HTML;
        $data = [
            'title' => $title,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        if ($req->getMethod() == "GET") {
            // Get
            $this->renderPage($viewPathFromPages, $data);
        } else{

        }
    }
}
