<?php

namespace src\controllers;

use src\utils\CSRFHandler;
use src\exceptions\HttpExceptionFactory;

/**
 * Base controller 
 * Request & Response is not stored as property to make it stateless & singleton (inspired by NestJS default singleton lifecycle).
 * Also inspired by ExpressJS middleware/route handler signature.
 */
abstract class Controller
{
    /**
     * Verifikasi token CSRF
     */
    protected function verifyCSRFToken(array $requestBody): void
    {
        if (!isset($requestBody['csrf_token']) || !CSRFHandler::verifyToken($requestBody['csrf_token'])) {
            throw HttpExceptionFactory::createBadRequest('Invalid CSRF token');
        }
        
        // Regenerate token CSRF setelah digunakan
        CSRFHandler::regenerateToken();
    }
}
