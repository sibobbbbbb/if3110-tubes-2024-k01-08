<?php

namespace src\core;

use src\exceptions\HttpExceptionFactory;
use src\controllers\ErrorController;

class Router
{
    // Store registered routes for the app
    private $routes;
    private $controller;
    public function __construct()
    {
        $this->routes = [];
        $this->controller = new ErrorController();
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
            }
        }

        // If not found, default redirect to 404 page
        $this->controller->handleError(404, "Page Not Found", "Sorry, the page you are looking for doesn't exist.");
        // throw HttpExceptionFactory::createNotFound('Route not found');
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
}
