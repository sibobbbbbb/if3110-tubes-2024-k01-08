<?php

namespace src\controllers;

use src\core\{Request, Response};
use src\exceptions\HttpExceptionFactory;

class HomeController extends Controller
{
    public function renderHome(Request $req, Response $res): void
    {
        $viewPath = __DIR__ . '/../views/pages/home/index.php';

        if (!file_exists($viewPath)) {
            throw HttpExceptionFactory::create(404, 'View not found');
        }

        ob_start();

        require $viewPath;

        $content = ob_get_clean();

        error_log("Rendering home page");

        echo $content;
    }
}
