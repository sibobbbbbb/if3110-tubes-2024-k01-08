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


// Class for the entry point of the application
class Application
{
    private readonly Container $container;

    public function __construct()
    {
        // Initialize container
        $this->container = new Container();
        $this->registerContainer();

        // Initialize routes
        $this->registerRoutes();
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
            // Handle HTTP exceptions
            echo "HTTP Exception: " . $e->getMessage();
        } catch (Exception $e) {
            // Handle other exceptions as Internal Server Error
            echo "Internal Server Error: " . $e->getMessage();
        }
    }

    /**
     * Register all of the routes
     **/
    private function registerRoutes()
    {
        error_log("Registering routes...");

        $router = $this->container->get(Router::class);

        // Middlewares
        $anyAuthMiddleware = $this->container->get(AnyAuthMiddleware::class);
        $companyAuthMiddleware = $this->container->get(CompanyAuthMiddleware::class);
        $jobSeekerAuthMiddleware = $this->container->get(JobSeekerAuthMiddleware::class);

        // Home 
        $homeController = $this->container->get(HomeController::class);
        $router->addRoute('GET', '/', $homeController, 'renderHome', []);
        error_log("Home route registered");

        // Auth routes
        $authController = $this->container->get(AuthController::class);
        $router->addRoute('GET', '/auth/sign-in', $authController, 'renderSignIn', []);
        error_log("Login route registered");

        $router->addRoute('GET', '/auth/sign-up', $authController, 'renderSignUp', []);
        error_log("Register route registered");
    }

    /**
     * Bind key (class) and factory functions to container
     */
    private function registerContainer()
    {
        error_log("Starting IoC container...");

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
                return new Database($config);
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

        // Services
        $this->registerServicesToContainer();

        // Middlewares
        $this->registerMiddlewaresToContainer();

        // Controllers
        $this->registerControllersToContainer();
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
        error_log("AnyAuthMiddleware registered");
        // CompanyAuthMiddleware
        $this->container->bind(
            CompanyAuthMiddleware::class,
            function ($c) {
                return new CompanyAuthMiddleware();
            }
        );
        error_log("CompanyAuthMiddleware registered");
        // JobSeekerAuthMiddleware
        $this->container->bind(
            JobSeekerAuthMiddleware::class,
            function ($c) {
                return new JobSeekerAuthMiddleware();
            }
        );
        error_log("JobSeekerAuthMiddleware registered");
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
                $userService = $c->get(UserService::class);
                return new AuthController($userService);
            }
        );

        // Add more controllers here
    }
}
