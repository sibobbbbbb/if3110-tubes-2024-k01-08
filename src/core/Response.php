<?php

namespace src\core;


class Response
{
    public function json(int $statusCode, array $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
        exit();
    }
}
