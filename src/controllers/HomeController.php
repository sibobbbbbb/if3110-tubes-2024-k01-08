<?php

namespace src\controllers;

use src\core\{Request, Response};

class HomeController extends Controller
{
    public function renderHome(Request $req, Response $res): void
    {
        $viewPathFromPages = 'home/index.php';

        // Data to pass to the view
        $tile = 'LinkInPurry';
        $description = 'LinkInPurry is a job market platform';
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/home.css" />
                <script src="/scripts/home.js" defer></script>
            HTML;
        $data = [
            'title' => $tile,
            'description' => $description,
            'additionalTags' => $additionalTags
        ];

        $this->renderPage($viewPathFromPages, $data);
    }
}
