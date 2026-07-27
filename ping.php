<?php

require_once "bootstrap.php";

$start = microtime(true);

jsonResponse(
    true,
    [
        "message" => "PONG",
        "server_time" => date("Y-m-d H:i:s"),
        "php" => PHP_VERSION,
        "version" => APP_VERSION,
        "response_ms" => round((microtime(true) - $start) * 1000, 2)
    ],
    [
        "Servidor operativo"
    ]
);