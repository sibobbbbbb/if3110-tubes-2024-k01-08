<?php

class Response
{
    public function json($statusCode, $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}
