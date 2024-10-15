<?php

namespace src\controllers;

use src\core\{Request, Response};

/**
 * Controller for handling authentication
 * Request & Response is not stored as property to make it stateless & singleton (inspired by NestJS default singleton lifecycle).
 */
class AuthController extends Controller
{
    /**
     * Renders the sign in page
     */
    public function renderSignIn(Request $req, Response $res): void {}

    /**
     * Renders the sign up page
     */
    public function renderSignUp(Request $req, Response $res): void {}

    /**
     * Handles the sign in request
     */
    public function handleSignIn(Request $req, Response $res): void {}

    /**
     * Handles the sign up request
     */
    public function handleSignUp(Request $req, Response $res): void {}
}
