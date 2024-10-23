<?php

namespace src\core;

use src\exceptions\HttpExceptionFactory;
class Response
{
    public function json(int $statusCode, array $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
        exit();
    }

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
        $statusCode = $data['statusCode'];

        if (!isset($data['subHeading'])) {
            switch ($statusCode) {
                case 100:
                    $data['subHeading'] = 'Continue';
                    break;
                case 101:
                    $data['subHeading'] = 'Switching Protocols';
                    break;
                case 200:
                    $data['subHeading'] = 'OK';
                    break;
                case 201:
                    $data['subHeading'] = 'Created';
                    break;
                case 202:
                    $data['subHeading'] = 'Accepted';
                    break;
                case 203:
                    $data['subHeading'] = 'Non-Authoritative Information';
                    break;
                case 204:
                    $data['subHeading'] = 'No Content';
                    break;
                case 205:
                    $data['subHeading'] = 'Reset Content';
                    break;
                case 206:
                    $data['subHeading'] = 'Partial Content';
                    break;
                case 300:
                    $data['subHeading'] = 'Multiple Choices';
                    break;
                case 301:
                    $data['subHeading'] = 'Moved Permanently';
                    break;
                case 302:
                    $data['subHeading'] = 'Found';
                    break;
                case 303:
                    $data['subHeading'] = 'See Other';
                    break;
                case 304:
                    $data['subHeading'] = 'Not Modified';
                    break;
                case 305:
                    $data['subHeading'] = 'Use Proxy';
                    break;
                case 307:
                    $data['subHeading'] = 'Temporary Redirect';
                    break;
                case 400:
                    $data['subHeading'] = 'Bad Request';
                    break;
                case 401:
                    $data['subHeading'] = 'Unauthorized';
                    break;
                case 402:
                    $data['subHeading'] = 'Payment Required';
                    break;
                case 403:
                    $data['subHeading'] = 'Forbidden';
                    break;
                case 404:
                    $data['subHeading'] = 'Not Found';
                    break;
                case 405:
                    $data['subHeading'] = 'Method Not Allowed';
                    break;
                case 406:
                    $data['subHeading'] = 'Not Acceptable';
                    break;
                case 407:
                    $data['subHeading'] = 'Proxy Authentication Required';
                    break;
                case 408:
                    $data['subHeading'] = 'Request Timeout';
                    break;
                case 409:
                    $data['subHeading'] = 'Conflict';
                    break;
                case 410:
                    $data['subHeading'] = 'Gone';
                    break;
                case 411:
                    $data['subHeading'] = 'Length Required';
                    break;
                case 412:
                    $data['subHeading'] = 'Precondition Failed';
                    break;
                case 413:
                    $data['subHeading'] = 'Request Entity Too Large';
                    break;
                case 414:
                    $data['subHeading'] = 'Request-URI Too Long';
                    break;
                case 415:
                    $data['subHeading'] = 'Unsupported Media Type';
                    break;
                case 416:
                    $data['subHeading'] = 'Requested Range Not Satisfiable';
                    break;
                case 417:
                    $data['subHeading'] = 'Expectation Failed';
                    break;
                case 500:
                    $data['subHeading'] = 'Internal Server Error';
                    break;
                case 501:
                    $data['subHeading'] = 'Not Implemented';
                    break;
                case 502:
                    $data['subHeading'] = 'Bad Gateway';
                    break;
                case 503:
                    $data['subHeading'] = 'Service Unavailable';
                    break;
                case 504:
                    $data['subHeading'] = 'Gateway Timeout';
                    break;
                case 505:
                    $data['subHeading'] = 'HTTP Version Not Supported';
                    break;
                default:
                    $data['subHeading'] = 'Unknown Status';
                    break;
            }
        }
        
        $title = $data['subHeading'];
        $description = $data['message'];
        $additionalTags = <<<HTML
                <link rel="stylesheet" href="/styles/global.css" />
                <link rel="stylesheet" href="/styles/error/error.css" />
            HTML;
        $data['additionalTags'] = $additionalTags;

        $layoutPath = __DIR__ . '/../views/layouts/root-layout.php';
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

        // Get page content
        extract($data);
        require $contentPath;
        $content = ob_get_clean();

        // Render the layout
        require $layoutPath;
        echo ob_get_clean();

        exit();
    }
}