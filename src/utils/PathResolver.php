<?php

namespace src\utils;

class PathResolver
{
    /**
     * Remove trailing slashes from the given path.
     * Remove duplicate slashes from the given path.
     * @param string $path
     */
    public static function resolve(string $path): string
    {
        $path = rtrim($path, '/');
        $path = preg_replace('/\/+/', '/', $path);
        return $path;
    }

    /**
     * Get base url of the application.
     */
    public static function getBaseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . "://" . $host;
    }
}
