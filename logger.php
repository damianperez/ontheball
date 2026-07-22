<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 * logger.php
 * ----------------------------------------------------------
 * Sistema de logging y depuración.
 * ==========================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

class Logger
{
    /**
     * Escribe una línea en un archivo.
     */
    private static function write(
        string $file,
        string $level,
        string $title,
        mixed $data = null
    ): void {

        if (!DEBUG) {
            return;
        }

        $date = date('Y-m-d H:i:s');

        $memory = round(memory_get_usage(true) / 1024 / 1024, 2);

        $text = PHP_EOL;
        $text .= str_repeat("=", 80) . PHP_EOL;
        $text .= "DATE    : {$date}" . PHP_EOL;
        $text .= "LEVEL   : {$level}" . PHP_EOL;
        $text .= "TITLE   : {$title}" . PHP_EOL;
        $text .= "IP      : " . ($_SERVER['REMOTE_ADDR'] ?? '-') . PHP_EOL;
        $text .= "METHOD  : " . ($_SERVER['REQUEST_METHOD'] ?? '-') . PHP_EOL;
        $text .= "URI     : " . ($_SERVER['REQUEST_URI'] ?? '-') . PHP_EOL;
        $text .= "MEMORY  : {$memory} MB" . PHP_EOL;
        $text .= str_repeat("-", 80) . PHP_EOL;

        if ($data !== null) {

            if (is_array($data) || is_object($data)) {

                $text .= json_encode(
                    $data,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                );

            } else {

                $text .= (string)$data;

            }

            $text .= PHP_EOL;
        }

        $text .= str_repeat("=", 80) . PHP_EOL;

        file_put_contents(
            $file,
            $text,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * INFO
     */
    public static function info(
        string $title,
        mixed $data = null,
        string $file = POST_LOG
    ): void {

        self::write(
            $file,
            "INFO",
            $title,
            $data
        );

    }

    /**
     * WARNING
     */
    public static function warning(
        string $title,
        mixed $data = null,
        string $file = ERROR_LOG
    ): void {

        self::write(
            $file,
            "WARNING",
            $title,
            $data
        );

    }

    /**
     * ERROR
     */
    public static function error(
        string $title,
        mixed $data = null,
        string $file = ERROR_LOG
    ): void {

        self::write(
            $file,
            "ERROR",
            $title,
            $data
        );

    }

    /**
     * Guarda un bloque JSON bonito.
     */
    public static function json(
        string $title,
        mixed $json,
        string $file = POST_LOG
    ): void {

        self::write(
            $file,
            "JSON",
            $title,
            $json
        );

    }

    /**
     * Guarda excepción.
     */
    public static function exception(Throwable $e): void
    {

        self::write(
            ERROR_LOG,
            "EXCEPTION",
            $e->getMessage(),
            [
                "file" => $e->getFile(),
                "line" => $e->getLine(),
                "trace" => $e->getTraceAsString()
            ]
        );

    }

    /**
     * Lee un log.
     */
    public static function read(string $file): string
    {

        if (!file_exists($file)) {
            return "";
        }

        return file_get_contents($file);

    }

    /**
     * Limpia un log.
     */
    public static function clear(string $file): void
    {

        file_put_contents($file, "");

    }

    /**
     * Limpia todos los logs.
     */
    public static function clearAll(): void
    {

        foreach ([
            BOT_LOG,
            POST_LOG,
            TELEGRAM_LOG,
            ERROR_LOG
        ] as $log) {

            file_put_contents($log, "");

        }

    }

    /**
     * Cronómetro.
     */
    public static function timerStart(): float
    {

        return microtime(true);

    }

    public static function timerEnd(float $start): float
    {

        return round(
            (microtime(true) - $start) * 1000,
            2
        );

    }

}