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
            throw HttpExceptionFactory::createNotFound("Page Not Found");
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

        exit();
    }

    /**
     * Render an error page
     * @param int $statusCode The HTTP status code to send
     * @param string $subheading The error subheading
     * @param string $message The error message
     */
    public function renderError(array $data = []): void
    {
        $contentPath = __DIR__ . '/../views/error/index.php';

        // Set HTTP status code immediately
        // http_response_code($statusCode);
        

        // Check if the content file exists
        if (!file_exists($contentPath)) {
            // If error page doesn't exist, fallback to simple error display
            throw HttpExceptionFactory::createNotFound("Page Not Found");
        }

        // Start output buffering
        ob_start();

        // Get error page content
        extract($data);
        require $contentPath;

        // Output the content
        echo ob_get_clean();

        exit();
    }
}
