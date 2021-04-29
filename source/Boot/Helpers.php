<?php

use Source\Support\Response;

/**
 * Create a JSON response od the given value
 *
 * @param  mixed $data
 * @return Response
 */
function response($data, int $httpResponseCode = 200): Response
{
    return (new Response($data, $httpResponseCode));
}

/**
 * Get a value of a given environment variable
 *
 * @param string $variable
 * @return string
 */
function env(string $variable): string
{
    return $_ENV[$variable];
}
