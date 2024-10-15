<?php

namespace src\controllers;

use src\exceptions\HttpExceptionFactory;

/**
 * Base controller 
 * Request & Response is not stored as property to make it stateless & singleton (inspired by NestJS default singleton lifecycle).
 * Also inspired by ExpressJS middleware/route handler signature.
 */
abstract class Controller
{
    /**
     * Render a page with layout
     * @param string $viewPathFromViews The path to the view file from the src/views/pages directory
     * @param array $data The data to pass to the view e.g. (title, description, etc.) !!! DON'T USE "content" key. !!! 
     */
    public function renderPage(string $viewPathFromPages, array $data = []): void
    {
        $layoutPath = __DIR__ . '/../views/layouts/root-layout.php';
        $contentPath = __DIR__ . "/../views/pages/$viewPathFromPages";

        // Check if the layout and content files exist
        if (!file_exists($layoutPath) || !file_exists($contentPath)) {
            throw HttpExceptionFactory::create(404, "Page Not Found");
        }

        // Start output buffering
        ob_start();

        // Get page content
        extract($data);
        require $contentPath;
        $content = ob_get_clean();

        // Render the layout
        require $layoutPath;
        echo ob_get_clean();
    }
}
