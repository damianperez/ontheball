<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * telegram_client.php
 *
 * Cliente interno Bot API
 * ==========================================================
 */

declare(strict_types=1);

require_once "config.php";
require_once "logger.php";


class TelegramClient
{

    /**
     * Ejecutar método Bot API
     */
    public static function call(
        string $method,
        array $params = []
    ): array {


        $start = Logger::timerStart();


        $url = BOT_API . $method;


        Logger::info(
            "Telegram REQUEST ".$method,
            $params,
            TELEGRAM_LOG
        );


        $curl = curl_init();


        curl_setopt_array(

            $curl,

            [

                CURLOPT_URL => $url,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_POST => true,

                CURLOPT_POSTFIELDS => $params,

                CURLOPT_TIMEOUT => 30,

                CURLOPT_CONNECTTIMEOUT => 10,

                CURLOPT_SSL_VERIFYPEER => true

            ]

        );


        $response = curl_exec($curl);


        $http = curl_getinfo(

            $curl,

            CURLINFO_HTTP_CODE

        );


        $error = curl_error($curl);


        curl_close($curl);



        $time = Logger::timerEnd($start);



        if($error){

            Logger::error(

                "Telegram CURL ERROR",

                $error,

                TELEGRAM_LOG

            );


            return [

                "ok"=>false,

                "error"=>$error

            ];

        }



        $json=json_decode(

            $response,

            true

        );



        Logger::json(

            "Telegram RESPONSE ".$time."ms",

            $json,

            TELEGRAM_LOG

        );



        return $json ?? [

            "ok"=>false,

            "error"=>"Respuesta inválida",

            "raw"=>$response

        ];

    }



    /**
     * Enviar mensaje
     */
    public static function sendMessage(

        int|string $chat_id,

        string $text,

        array $extra=[]

    ):array{


        return self::call(

            "sendMessage",

            array_merge(

                [

                    "chat_id"=>$chat_id,

                    "text"=>$text,

                    "parse_mode"=>"HTML"

                ],

                $extra

            )

        );

    }



    /**
     * Información del bot
     */
    public static function getMe():array
    {

        return self::call("getMe");

    }



    /**
     * Información webhook
     */
    public static function getWebhookInfo():array
    {

        return self::call(

            "getWebhookInfo"

        );

    }



    /**
     * Configurar webhook
     */
    public static function setWebhook(

        string $url

    ):array{


        return self::call(

            "setWebhook",

            [

                "url"=>$url

            ]

        );

    }



    /**
     * Eliminar webhook
     */
    public static function deleteWebhook():array
    {

        return self::call(

            "deleteWebhook"

        );

    }

}