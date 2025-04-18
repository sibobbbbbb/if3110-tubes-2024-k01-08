<?php

namespace src\core;


use src\utils\CSRFHandler;
use src\exceptions\HttpExceptionFactory;

class Router
{
    // Store registered routes for the app
    private $routes;
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * Add a route to the app with certain method, path, handler (Class@method), and middlewares
     * @param string $method
     * @param string $path
     * @param callable Factory function that returns the function handler (associative array controller, method) (only 1)
     * @param callable Factory function that returns the middlewares (>= 0)
     */
    private function addRoute(string $method, string $path, callable $handlerFactory, array $middlewaresFactory = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handlerFactory' => $handlerFactory,
            'middlewaresFactory' => $middlewaresFactory,
        ];
    }

    /**
     * Add a GET route to the app
     */
    public function get(string $path, callable $handlerFactory, array $middlewaresFactory = [])
    {
        $this->addRoute('GET', $path, $handlerFactory, $middlewaresFactory);
    }

    /**
     * Add a POST route to the app
     */
    public function post(string $path, callable $handlerFactory, array $middlewaresFactory = [])
    {
        $this->addRoute('POST', $path, $handlerFactory, $middlewaresFactory);
    }

    /**
     * Add a PUT route to the app
     */
    public function put(string $path, callable $handlerFactory, array $middlewaresFactory = [])
    {
        $this->addRoute('PUT', $path, $handlerFactory, $middlewaresFactory);
    }

    /**
     * Add a DELETE route to the app
     */
    public function delete(string $path, callable $handlerFactory, array $middlewaresFactory = [])
    {
        $this->addRoute('DELETE', $path, $handlerFactory, $middlewaresFactory);
    }

    /**
     * Dispatch the request to the correct handler
     */
    public function dispatch(): void
    {
        $res = new Response();
        $reqMethod = $_SERVER['REQUEST_METHOD'];
        $reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($this->matchPath($route['path'], $reqPath) && $route['method'] === $reqMethod) {
                // Initialize request & response
                $req = new Request($route['path']);
                
                try {
                    // Verify CSRF for non-GET requests
                    if ($reqMethod !== 'GET') {
                        $this->verifyCSRF($req);
                    }
                    
                    // Call all middlewares
                    $middlewareFactories = $route['middlewaresFactory'];
                    foreach ($middlewareFactories as $mf) {
                        $mf()->handle($req, $res);
                    }
    
                    // Call the handler
                    $handler = $route['handlerFactory']();
                    $controller = $handler['controller'];
                    $method = $handler['method'];
                    $controller->$method($req, $res);
                    
                    return;
                } catch (\Exception $e) {
                    $data = [
                        'statusCode' => 400,
                        'message' => $e->getMessage(),
                    ];
                    $res->renderError($data);
                    return;
                }
            }
        }

        // If not found render to 404 page
        $data = [
            'statusCode' => 404,
            'subHeading' => "Page Not Found",
            'message' => "Sorry, the page you are looking for doesnt exist",
        ];

        $res->renderError($data);
    }

    private function matchPath(string $router, string $uri): bool
    {
        // If root
        if ($router === '/') {
            // Seperate the query params if any
            $uri = explode('?', $uri)[0];
            return $uri === '/';
        }

        // Not root
        // Parse path parameters /[id]/ into regex
        // and match from the beginning to the end of the string

        // ignore trailing slashes
        $parsedUri = rtrim($uri, '/');

        // explode /
        $routerPaths = explode('/', $router);
        $uriPaths = explode('/', $parsedUri);

        // if the number of paths is different, return false
        if (count($routerPaths) !== count($uriPaths)) {
            return false;
        }

        // if the paths are different, return false
        for ($i = 0; $i < count($routerPaths); $i++) {
            // if the path is equal, continue
            if ($routerPaths[$i] === $uriPaths[$i]) {
                continue;
            }

            // if the path not a parameter, return false
            if ($routerPaths[$i][0] !== '[' || $routerPaths[$i][strlen($routerPaths[$i]) - 1] !== ']') {
                return false;
            }

            // if the path is a parameter, continue
        }

        return true;
    }

    private function verifyCSRF(Request $req): void
    {
        // Skip CSRF check for GET requests
        if ($req->getMethod() === 'GET') {
            return;
        }
        
        // Exclude certain paths from CSRF verification
        $excludedPaths = [
            '/auth/sign-out',
            '/auth/sign-up/job-seeker',
            '/auth/sign-up/company'
        ];
        
        if (in_array($req->getPath(), $excludedPaths)) {
            return;
        }         

        // Verify CSRF token from header or body
        $headerToken = $req->getHeader('X-CSRF-TOKEN');
        $bodyToken = $req->getBody()['csrf_token'] ?? null;
        $token = $headerToken ?? $bodyToken;
        
        if (!$token || !CSRFHandler::verifyToken($token)) {
            throw HttpExceptionFactory::createBadRequest('Invalid CSRF token');
        }
    }
}
