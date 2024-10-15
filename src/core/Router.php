<?php

namespace src\core;

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
     * @param object $handler: instance of the class that contains the method
     * @param string $action: method name
     * @param array $middlewares: array of middlewares
     */
    public function addRoute(string $method, string $path, object $handler, string $action, array $middlewares)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $res = new Response();
        $reqMethod = $_SERVER['REQUEST_METHOD'];
        $reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($this->matchPath($route['path'], $reqPath) && $route['method'] === $reqMethod) {
                // Initialize request & response
                $req = new Request($route['path']);

                // Call all middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middleware->handle($req, $res);
                }

                // Call the handler
                $handler = $route['handler'];
                $action = $route['action'];
                $handler->$action($req, $res);

                // exit
                return;
            }
        }

        // If not found, default redirect to 404 page
        throw HttpExceptionFactory::create(404, 'Route not found');
    }

    private function matchPath(string $router, string $uri): bool
    {
        // Parse path parameters /{id}/ into regex
        // and match from the beginning to the end of the string
        $regex = '#^' . preg_replace('/{[^}]+}/', '([^/]+)', $router) . '$#';
        return preg_match($regex, $uri);
    }
}
