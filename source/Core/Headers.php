<?php

header("Access-Control-Allow-Origin: " . CONF_API_ALLOW_ORIGIN . "");
header("Content-Type: " . CONF_API_CONTENT_TYPE . "; charset=" . CONF_API_CHARSET . "");
header("Access-Control-Allow-Methods: " . implode(', ', CONF_API_ALLOW_METHODS) . "");
header("Access-Control-Max-Age: " . CONF_API_MAX_AGE . "");
header("Access-Control-Allow-Headers: " . implode(', ', CONF_API_ALLOW_HEADERS) . "");
