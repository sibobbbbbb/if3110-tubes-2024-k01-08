<?php

namespace src\core;

use Exception;

// Request object instance to store each request data (method, query params, path params, uri, body, headers, ect)
class Request
{
    // GET, POST, PUT, DELETE, PATCH, etc
    private string $method;

    // Path of the request (for example: /users/123/)
    private string $reqPath;

    // Path of the route (for example: /users/{id}/)
    // This value should be the same as the route path if no path parameters
    private string $routePath;

    // Associative array to store query parameters from the URI {search} => 'foo'
    private array $queryParams;

    // Associative array to store path parameters from the URI {fooID} => 123
    private $pathParams;

    // Store the parsed body of the request into associative array
    private array $body;

    // Store fils from multipart form data
    private array $files;

    // Initialize request object
    public function __construct(string $routePath)
    {
        // Method (uppercase)
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Request path
        $this->reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Route path
        $this->routePath = $routePath;

        // Parse query parameters
        $this->parseQueryParams();

        // Extract path parameters
        $this->extractPathParams();

        // Parse body
        $this->extractBody();
    }

    /**
     * Get HTTP method of the request
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get path of the request
     */
    public function getPath(): string
    {
        return $this->reqPath;
    }

    /**
     * Get path of the route
     */
    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * Get query parameters from the URI
     */
    public function getQueryParams(string $id): string | null
    {
        return $this->queryParams[$id] ?? null;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get path parameters from the URI
     */
    public function getPathParams(string $id): string | null
    {
        return $this->pathParams[$id] ?? null;
    }

    /**
     * Get body of the request
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Extract path parameters from the URI /wkwk/{:FOO}/{:BAR}
     * and store it in pathParams
     */
    private function extractPathParams(): void
    {
        $path = explode('/', $this->reqPath);
        $route = explode('/', $this->routePath);

        $this->pathParams = [];
        foreach ($route as $index => $part) {
            // Check if { is in the first index and } is in the last index
            if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
                $this->pathParams[$part] = htmlspecialchars($path[$index], ENT_QUOTES, 'UTF-8');
            }
        }
    }

    /**
     * Parse query parameters from the URI
     */
    private function parseQueryParams(): void
    {
        $this->queryParams = [];
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if ($query) {
            $queryParams = explode('&', $query);
            foreach ($queryParams as $param) {
                $param = explode('=', $param);
                if (count($param) === 2) {
                    $this->queryParams[$param[0]] = htmlspecialchars($param[1], ENT_QUOTES, 'UTF-8');
                }
            }
        }
    }

    /**
     * Parse body using json
     */
    private function extractBody(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        // Initialize as an empty associative array
        $this->body = [];

        // Check if the request has a body
        if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'PATCH') {
            // Parse JSON content
            if (strpos($contentType, 'application/json') !== false) {
                $input = file_get_contents('php://input');
                $jsonBody = json_decode($input, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->body = $jsonBody ?? [];
                } else {
                    throw new Exception('Invalid JSON payload');
                }
                $this->sanitizeBody();
            }
            // Parse URL-encoded form data
            elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                parse_str(file_get_contents('php://input'), $this->body);
                $this->sanitizeBody();
            }
            // Parse multipart form data
            elseif (strpos($contentType, 'multipart/form-data') !== false) {
                foreach ($_POST as $key => $value) {
                    $this->body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }

                foreach ($_FILES as $key => $file) {
                    $this->body[$key] = $file;
                }
            }
        }

        // // Sanitize the body data
        // $this->sanitizeBody();
    }

    private function sanitizeBody(): void
    {
        array_walk_recursive($this->body, function (&$value) {
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });
    }
}
