<?php

use Source\Core\Response;

/**
 * Create a JSON response od the given value
 */
function response(mixed $data, int $httpResponseCode = 200): Response
{
    return (new Response($data, $httpResponseCode));
}

/**
 * Get a value of a given environment variable
 */
function env(string $variable): string
{
    return $_ENV[$variable];
}
