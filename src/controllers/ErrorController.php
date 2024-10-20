<?php

namespace src\controllers;
class ErrorController extends Controller
{
    public function __construct()
    {
        // Constructor tanpa parameter
    }

     /**
     * Menangani rendering halaman kesalahan
     * @param int $statusCode
     * @param string $subheading
     * @param string $message
     */
    public function handleError(int $statusCode, string $subheading, string $message): void
    {
        $data = [
            'additionalTags' => '<link rel="stylesheet" href="/styles/error/error.css" />',
            'statusCode' => $statusCode,
            'subHeading' => $subheading,
            'message' => $message,
        ];

        $this->renderError($data);
    }
}