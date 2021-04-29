<?php

namespace Source\Support;

/**
 * Request
 */
class Request
{

    /**
     * Decode a given string
     *
     * @param  mixed $string
     * @return string|array
     */
    public static function decode(string $string)
    {
        if (self::isJson($string)) {
            return json_decode($string, true);
        }

        parse_str(file_get_contents('php://input'), $data);
        return $data;
    }

    /**
     * Verify if a string is a JSON or not
     *
     * @param  string $string
     * @return bool
     */
    public static function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
