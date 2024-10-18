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
}
