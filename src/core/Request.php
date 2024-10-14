<?php

// Typehinting
enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case OPTIONS = 'OPTIONS';
    // Add more if needed
}

// Request object instance to store each request data (method, query params, path params, uri, body, headers, ect)
class Request
{
    // GET, POST, PUT, DELETE, PATCH, etc
    private RequestMethod $method;

    // Path of the request (for example: /users/123/)
    private string $reqPath;

    // Path of the route (for example: /users/{id}/)
    // This value should be the same as the route path if no path parameters
    private string $routePath;

    // Associative array to store query parameters from the URI {search} => 'foo'
    private array $queryParams;

    // Associative array to store path parameters from the URI {fooID} => 123
    private $pathParams;

    // Store the parsed body of the request
    private object $body;

    // Initialize request object
    public function __construct(string $routePath)
    {
        // Method (uppercase)
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        // Request path
        $this->reqPath = $_SERVER['REQUEST_URI'] ?? '/';
        // Route path
        $this->routePath = $routePath;
        // Query params
        $this->queryParams = $_SERVER['QUERY_STRING'] ?? [];
        // Parse path params: find {FOO} in the URI and store it in pathParams
        $this->extractPathParams();
        // Parse body
        $this->extractBody();
    }

    /**
     * Get HTTP method of the request
     */
    public function getMethod(): RequestMethod
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
    public function getBody(): object
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
                $this->pathParams[$part] = $path[$index];
            }
        }
    }

    /**
     * Extract the body of the request
     */
    private function extractBody(): void
    {
        $this->body = [];

        // If GET or DELETE, no body
        if ($this->method === RequestMethod::GET || $this->method === RequestMethod::DELETE) {
            return;
        }

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if (strcasecmp($contentType, 'application/json') == 0) {
            // Handle JSON input
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data');
            }

            $this->body = $decoded;
            return;
        } else if (strcasecmp($contentType, 'application/x-www-form-urlencoded') == 0) {
            // Handle form data
            $this->body = $_POST;
            return;
        } else if (strpos($contentType, 'multipart/form-data') !== false) {
            // Handle multipart form data - uses $_POST for form fields and $_FILES for file uploads
            $this->body = array_merge($_POST, $_FILES);
            return;
        } else {
            // If none of the above, return raw input
            $this->body = file_get_contents("php://input");
            return;
        }
    }
}
