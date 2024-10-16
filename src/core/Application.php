<?php

namespace src\core;

use Exception;
use src\exceptions\BaseHttpException;
use src\core\{Config, Router, Container};
use src\database\Database;
use src\repositories\UserRepository;
use src\services\{UserService, AuthService};
use src\middlewares\{AnyAuthMiddleware, CompanyAuthMiddleware, JobSeekerAuthMiddleware};
use src\controllers\{AuthController, HomeController};
use src\utils\UserSession;

// Class for the entry point of the application
class Application
{
    private readonly Container $container;

    public function __construct()
    {
        // Initialize container
        $this->container = new Container();
        $this->registerContainer();
        error_log("Container initialized");

        // Initialize routes
        $this->registerRoutes();
        error_log("Routes initialized");

        // Initialize session
        UserSession::start();
    }

    /**
     * For run the application, and handling global exceptions
     * Inspired by NestJS's Global Exception Filter to prevent the application from crashing
     */
    public function run()
    {
        try {
            // Run the application
            error_log("Running application...");
            $router = $this->container->get(Router::class);
            $router->dispatch();
        } catch (BaseHttpException $e) {
            // Global exception filters
            // Redirct to /error?code=xxx&message=xxx
            echo "HTTP Exception: " . $e->getMessage();
        } catch (Exception $e) {
            // Handle other exceptions as Internal Server Error
            // Redirct to /error?code=500&message=xxx
            echo "Internal Server Error: " . $e->getMessage();
        }
    }

    /**
     * Register all of the routes
     **/
    private function registerRoutes()
    {
        $router = $this->container->get(Router::class);

        // Middlewares factory function
        $anyAuthMiddlewareFactoryFunction = function () {
            return [$this->container->get(AnyAuthMiddleware::class)];
        };
        $companyAuthMiddleware = function () {
            return [$this->container->get(CompanyAuthMiddleware::class)];
        };
        $jobSeekerAuthMiddleware = function () {
            return [$this->container->get(JobSeekerAuthMiddleware::class)];
        };


        // Home 
        $router->get(
            '/',
            function () {
                $controller = $this->container->get(HomeController::class);
                $method = 'renderHome';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
        );


        // Auth routes
        // Sign in (render)
        $router->get(
            '/auth/sign-in',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'renderAndHandleSignIn';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
        );
        // Sign in (form handling request)
        $router->post(
            '/auth/sign-in',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'renderAndHandleSignIn';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
        );

        // Sign out
        $router->post(
            '/auth/sign-out',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'handleSignOut';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
        );

        // Sign up
        $router->get(
            '/auth/sign-up',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'renderSignUp';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            }
        );

        // Sign up job seeker
        $router->get(
            '/auth/sign-up/job-seeker',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'renderSignUpJobSeeker';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            }
        );

        // Sign up company
        $router->get(
            '/auth/sign-up/company',
            function () {
                $controller = $this->container->get(AuthController::class);
                $method = 'renderSignUpCompany';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            }
        );
    }

    /**
     * Bind key (class) and factory functions to container
     */
    private function registerContainer()
    {
        // Config
        $this->container->bind(
            Config::class,
            function ($c) {
                return new Config();
            }
        );
        error_log("Config registered");

        // Database
        $this->container->bind(
            Database::class,
            function ($c) {
                $config = $c->get(Config::class);
                // initialize db & connect when first created
                $db = new Database($config);
                $db->connect();
                return $db;
            }
        );
        error_log("Database registered");

        // Router
        $this->container->bind(
            Router::class,
            function ($c) {
                return new Router();
            }
        );
        error_log("Router registered");

        // Repositories
        $this->registerRepositoriesToContainer();
        error_log("Repositories registered");

        // Services
        $this->registerServicesToContainer();
        error_log("Services registered");

        // Middlewares
        $this->registerMiddlewaresToContainer();
        error_log("Middlewares registered");

        // Controllers
        $this->registerControllersToContainer();
        error_log("Controllers registered");
    }

    /**
     * Register repositories to container
     */
    private function registerRepositoriesToContainer()
    {
        // UserRepository
        $this->container->bind(
            UserRepository::class,
            function ($c) {
                $db = $c->get(Database::class);
                return new UserRepository($db);
            }
        );

        // Add more repositories here
    }

    /**
     * Register services to container
     */
    private function registerServicesToContainer()
    {
        // UserService
        $this->container->bind(
            UserService::class,
            function ($c) {
                $userRepository = $c->get(UserRepository::class);
                return new UserService($userRepository);
            }
        );

        // Authentication Service
        $this->container->bind(
            AuthService::class,
            function ($c) {
                $userRepository = $c->get(UserRepository::class);
                return new AuthService($userRepository);
            }
        );
    }

    /**
     * Register middlewares to container
     */
    private function registerMiddlewaresToContainer()
    {
        // AnyAuthMiddleware
        $this->container->bind(
            AnyAuthMiddleware::class,
            function ($c) {
                return new AnyAuthMiddleware();
            }
        );

        // CompanyAuthMiddleware
        $this->container->bind(
            CompanyAuthMiddleware::class,
            function ($c) {
                return new CompanyAuthMiddleware();
            }
        );

        // JobSeekerAuthMiddleware
        $this->container->bind(
            JobSeekerAuthMiddleware::class,
            function ($c) {
                return new JobSeekerAuthMiddleware();
            }
        );

        // Add more middlewares here
    }

    /**
     * Register controllers to container
     */
    private function registerControllersToContainer()
    {
        // Home Controller
        $this->container->bind(
            HomeController::class,
            function ($c) {
                return new HomeController();
            }
        );

        // AuthController
        $this->container->bind(
            AuthController::class,
            function ($c) {
                $authService = $c->get(AuthService::class);
                return new AuthController($authService);
            }
        );

        // Add more controllers here
    }
}
