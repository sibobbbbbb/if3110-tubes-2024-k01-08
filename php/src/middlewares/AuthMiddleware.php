<?php

namespace src\middlewares;

use src\core\{Request, Response};
use src\utils\UserSession;
use src\utils\CSRFHandler;

abstract class AuthMiddleware extends Middleware
{
    // Allowed roles
    protected array $roles;

    /**
     * Constructor
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Handle the middleware
     * @param array $roles: if empty, any role can access the route. If not empty, only the specified roles can access the route.
     * @param Request $req: the request object
     * @param Response $res: the response object
     */
    public function handle(Request $req, Response $res)
    {
        // If user is not logged in, redirect to sign in page
        if (!UserSession::isLoggedIn()) {
            $res->redirect('/auth/sign-in');
        }

        // If no roles specified, any role can access the route
        if (empty($this->roles)) {
            return;
        }

        // If user is logged in, but not in the specified roles, redirect to error page
        if (!in_array(UserSession::getUserRole(), $this->roles)) {
            $data = [
                'statusCode' => 403,
                'message' => "You are not authorized to see this page.",
            ];
    
            $res->renderError($data);
        }

        // Verify CSRF for non-GET requests
        if ($req->getMethod() !== 'GET') {
            $token = $req->getHeader('X-CSRF-TOKEN') ?? $req->getBody()['csrf_token'] ?? null;
            
            if (!CSRFHandler::verifyToken($token)) {
                $data = [
                    'statusCode' => 403,
                    'message' => "Invalid CSRF token.",
                ];
                
                $res->renderError($data);
            }
        }

        // continue
    }
}
