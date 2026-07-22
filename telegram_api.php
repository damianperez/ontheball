<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 * telegram_api.php
 * ----------------------------------------------------------
 * Cliente para la Telegram Bot API
 * ==========================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

class TelegramAPI
{
    /**
     * Llamada genérica a cualquier método de Telegram
     */
    public static function call(
        string $method,
        array $params = []
    ): array {

        $timer = Logger::timerStart();

        $url = BOT_API . $method;

        Logger::info(
            "Telegram API -> {$method}",
            $params,
            TELEGRAM_LOG
        );

        $ch = curl_init($url);

        curl_setopt_array($ch, [

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_POSTFIELDS => $params,

            CURLOPT_CONNECTTIMEOUT => 10,

            CURLOPT_TIMEOUT => 30,

            CURLOPT_SSL_VERIFYPEER => true,

            CURLOPT_HTTPHEADER => [
                "Expect:"
            ]

        ]);

        $response = curl_exec($ch);

        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $error = curl_error($ch);

        curl_close($ch);

        $ms = Logger::timerEnd($timer);

        if ($error != "") {

            Logger::error(
                "cURL ERROR",
                $error,
                TELEGRAM_LOG
            );

            return [

                "ok" => false,

                "error" => $error,

                "http" => $http

            ];

        }

        $json = json_decode($response, true);

        Logger::json(
            "Respuesta Telegram ({$ms} ms)",
            $json,
            TELEGRAM_LOG
        );

        return $json;

    }

    /**
     * Enviar mensaje
     */
    public static function sendMessage(
        int|string $chatId,
        string $text,
        array $extra = []
    ): array {

        return self::call(

            "sendMessage",

            array_merge(

                [

                    "chat_id" => $chatId,

                    "text" => $text,

                    "parse_mode" => "HTML"

                ],

                $extra

            )

        );

    }

    /**
     * Enviar foto
     */
    public static function sendPhoto(
        int|string $chatId,
        string $photo,
        string $caption = ""
    ): array {

        return self::call(

            "sendPhoto",

            [

                "chat_id" => $chatId,

                "photo" => $photo,

                "caption" => $caption

            ]

        );

    }

    /**
     * Enviar documento
     */
    public static function sendDocument(
        int|string $chatId,
        CURLFile|string $document,
        string $caption = ""
    ): array {

        return self::call(

            "sendDocument",

            [

                "chat_id" => $chatId,

                "document" => $document,

                "caption" => $caption

            ]

        );

    }

    /**
     * Responder callback
     */
    public static function answerCallbackQuery(
        string $callbackId,
        string $text = ""
    ): array {

        return self::call(

            "answerCallbackQuery",

            [

                "callback_query_id" => $callbackId,

                "text" => $text

            ]

        );

    }

    /**
     * Obtener información del bot
     */
    public static function getMe(): array
    {

        return self::call("getMe");

    }

    /**
     * Obtener webhook
     */
    public static function getWebhookInfo(): array
    {

        return self::call("getWebhookInfo");

    }

    /**
     * Configurar webhook
     */
    public static function setWebhook(
        string $url
    ): array {

        return self::call(

            "setWebhook",

            [

                "url" => $url

            ]

        );

    }

    /**
     * Borrar webhook
     */
    public static function deleteWebhook(): array
    {

        return self::call("deleteWebhook");

    }

}