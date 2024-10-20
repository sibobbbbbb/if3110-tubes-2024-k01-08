<?php

namespace src\core;

use Exception;
use src\exceptions\BaseHttpException;
use src\core\{Config, Router, Container};
use src\database\Database;
use src\services\{ApplicationService, UserService, AuthService, CompanyService, JobService, UploadService};
use src\middlewares\{AnyAuthMiddleware, CompanyAuthMiddleware, JobSeekerAuthMiddleware};
use src\controllers\{AuthController, CompanyController, HomeController, JobController};
use src\repositories\{ApplicationRepository, UserRepository, JobRepository};
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
            return $this->container->get(AnyAuthMiddleware::class);
        };
        $companyAuthMiddlewareFactoryFunction = function () {
            return $this->container->get(CompanyAuthMiddleware::class);
        };
        $jobSeekerAuthMiddleware = function () {
            return $this->container->get(JobSeekerAuthMiddleware::class);
        };


        /**
         * Home
         */
        $router->get(
            '/home',
            function () {
                $controller = $this->container->get(HomeController::class);
                $method = 'renderHome';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
        );


        /**
         * Auth Routes
         */
        $signInFactoryFunction = function () {
            $controller = $this->container->get(AuthController::class);
            $method = 'renderAndHandleSignIn';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Sign in (render)
        $router->get(
            '/auth/sign-in',
            $signInFactoryFunction
        );
        // Sign in (form handling request)
        $router->post(
            '/auth/sign-in',
            $signInFactoryFunction
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

        $signUpCompanyFactoryFunction = function () {
            $controller = $this->container->get(AuthController::class);
            $method = 'renderandhandleSignUpCompany';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Sign up (render)
        $router->get(
            '/auth/sign-up/company',
            $signUpCompanyFactoryFunction
        );
        // Sign up (form handling request)
        $router->post(
            '/auth/sign-up/company',
            $signUpCompanyFactoryFunction
        );

        $signUpJobSeekerFactoryFunction = function () {
            $controller = $this->container->get(AuthController::class);
            $method = 'renderandhandleSignUpJobSeeker';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Sign up (render)
        $router->get(
            '/auth/sign-up/job-seeker',
            $signUpJobSeekerFactoryFunction
        );
        // Sign up (form handling request)
        $router->post(
            '/auth/sign-up/job-seeker',
            $signUpJobSeekerFactoryFunction
        );

        /**
         * Company Routes
         */
        // Create job
        $createJobFactoryFunction = function () {
            $controller = $this->container->get(CompanyController::class);
            $method = 'renderAndHandleCreateJob';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Render
        $router->get(
            '/company/jobs/create',
            $createJobFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );
        // Handle
        $router->post(
            '/company/jobs/create',
            $createJobFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );

        // Get company jobs
        $router->get(
            '/company/jobs',
            function () {
                $controller = $this->container->get(CompanyController::class);
                $method = 'renderCompanyJobs';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
            [$companyAuthMiddlewareFactoryFunction]
        );

        // Update company profile
        $companyProfileFactoryFunction = function () {
            $controller = $this->container->get(CompanyController::class);
            $method = 'renderAndHandleUpdateCompany';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Render
        $router->get(
            '/company/profile',
            $companyProfileFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );
        // Handle
        $router->post(
            '/company/profile',
            $companyProfileFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );

        // Edit job
        $editJobFactoryFunction = function () {
            $controller = $this->container->get(CompanyController::class);
            $method = 'renderAndHandleEditJob';
            return [
                'controller' => $controller,
                'method' => $method
            ];
        };
        // Render
        $router->get(
            '/company/jobs/[jobId]/edit',
            $editJobFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );
        // Handle
        $router->post(
            '/company/jobs/[jobId]/edit',
            $editJobFactoryFunction,
            [$companyAuthMiddlewareFactoryFunction]
        );

        // Delete job
        $router->delete(
            '/company/jobs/[jobId]',
            function () {
                $controller = $this->container->get(CompanyController::class);
                $method = 'handleDeleteJob';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
            [$companyAuthMiddlewareFactoryFunction]
        );


        /**
         * Delete a job attachment
         */
        $router->delete(
            '/company/jobs/attachments/[attachmentId]',
            function () {
                $controller = $this->container->get(CompanyController::class);
                $method = 'handleDeleteJobAttachment';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
            [$companyAuthMiddlewareFactoryFunction]
        );

        $router->get(
            '/history',
            function () {
                $controller = $this->container->get(JobController::class);
                $method = 'renderandHandleHistory';
                return [
                    'controller' => $controller,
                    'method' => $method
                ];
            },
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

        // Job Repository
        $this->container->bind(
            JobRepository::class,
            function ($c) {
                $db = $c->get(Database::class);
                return new JobRepository($db);
            }
        );

        // Application Repository
        $this->container->bind(
            ApplicationRepository::class,
            function ($c) {
                $db = $c->get(Database::class);
                return new ApplicationRepository($db);
            }
        );

        // Add if needed
    }

    /**
     * Register services to container
     */
    private function registerServicesToContainer()
    {
        // Authentication Service
        $this->container->bind(
            AuthService::class,
            function ($c) {
                $userRepository = $c->get(UserRepository::class);
                return new AuthService($userRepository);
            }
        );

        // Upload Service
        $this->container->bind(
            UploadService::class,
            function ($c) {
                return new UploadService();
            }
        );

        // User Service
        $this->container->bind(
            UserService::class,
            function ($c) {
                $userRepository = $c->get(UserRepository::class);
                return new UserService($userRepository);
            }
        );

        // Job Service
        $this->container->bind(
            JobService::class,
            function ($c) {
                return new JobService();
            }
        );

        // Company Service
        $this->container->bind(
            CompanyService::class,
            function ($c) {
                $userRepository = $c->get(UserRepository::class);
                $jobRepository = $c->get(JobRepository::class);
                $uploadService = $c->get(UploadService::class);
                return new CompanyService($userRepository, $jobRepository, $uploadService);
            }
        );

        // Application Service
        $this->container->bind(
            ApplicationService::class,
            function ($c) {
                return new ApplicationService();
            }
        );

        // Add more if needed

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

        // JobController
        $this->container->bind(
            JobController::class,
            function ($c) {
                return new JobController();
            }
        );

        // CompanyController
        $this->container->bind(
            CompanyController::class,
            function ($c) {
                $companyService = $c->get(CompanyService::class);
                return new CompanyController($companyService);
            }
        );
    }
}
