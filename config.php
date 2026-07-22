<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 * config.php
 * ----------------------------------------------------------
 * Configuración general del proyecto.
 * ==========================================================
 */

declare(strict_types=1);

date_default_timezone_set('America/Argentina/Buenos_Aires');

/*
|--------------------------------------------------------------------------
| Telegram
|--------------------------------------------------------------------------
*/

define('BOT_TOKEN', '1618069377:AAGPFW-KGu-vCN0xUudrK6FRqWdD3AizrSc');

define(
    'BOT_API',
    'https://api.telegram.org/bot' . BOT_TOKEN . '/'
);

define(
    'WEBAPP_URL',
    'https://bots.perezcompany.com.ar/ontheball/'
);

/*
|--------------------------------------------------------------------------
| Proyecto
|--------------------------------------------------------------------------
*/

define('PROJECT_NAME', 'Telegram WebApp Debug Studio');

define('VERSION', '1.0.0');

define('DEBUG', true);

/*
|--------------------------------------------------------------------------
| Directorios
|--------------------------------------------------------------------------
*/

define('ROOT_PATH', __DIR__);

define('LOG_PATH', ROOT_PATH . '/logs');

define('DATA_PATH', ROOT_PATH . '/data');

/*
|--------------------------------------------------------------------------
| Archivos
|--------------------------------------------------------------------------
*/

define('BOT_LOG', LOG_PATH . '/bot.log');

define('POST_LOG', LOG_PATH . '/post.log');

define('TELEGRAM_LOG', LOG_PATH . '/telegram.log');

define('ERROR_LOG', LOG_PATH . '/error.log');

define('STATE_FILE', DATA_PATH . '/estado.json');

/*
|--------------------------------------------------------------------------
| Crear carpetas automáticamente
|--------------------------------------------------------------------------
*/

foreach ([LOG_PATH, DATA_PATH] as $dir) {

    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

}

/*
|--------------------------------------------------------------------------
| Crear estado inicial
|--------------------------------------------------------------------------
*/

if (!file_exists(STATE_FILE)) {

    file_put_contents(
        STATE_FILE,
        json_encode(
            [
                "updated" => "",
                "last_post" => [],
                "last_senddata" => [],
                "last_update" => [],
                "history" => []
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
    );

}

/*
|--------------------------------------------------------------------------
| Respuesta JSON estándar
|--------------------------------------------------------------------------
*/

function jsonResponse(
    bool $ok,
    mixed $data = null,
    array $debug = [],
    int $http = 200
): never {

    http_response_code($http);

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        [
            "ok" => $ok,
            "time" => date('Y-m-d H:i:s'),
            "memory" => memory_get_usage(true),
            "php" => PHP_VERSION,
            "data" => $data,
            "debug" => $debug
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    exit;

}