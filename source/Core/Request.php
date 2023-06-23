<?php

namespace Source\Core;

class Request
{
    /**
     * Decode a given string
     */
    public static function decode(string $string)
    {
        if (self::isJson($string)) {
            return json_decode($string, true);
        }

        parse_str($string, $data);
        return $data;
    }

    /**
     * Verify if a string is a JSON or not
     */
    private static function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}
