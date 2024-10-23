<?php

namespace src\utils;

class Utils
{
    /**
     * Check if value is a valid integer
     */
    public static function isInteger($value)
    {
        return is_numeric($value) && (int)$value == $value;
    }
}
