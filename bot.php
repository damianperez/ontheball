<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * bot.php
 *
 * Webhook receptor Telegram
 * ==========================================================
 */

declare(strict_types=1);


require_once "config.php";
require_once "logger.php";
require_once "state.php";
require_once "telegram_client.php";


$debug=[];


try {


    $start =
        Logger::timerStart();



    /*
    |--------------------------------------------------------------------------
    | Leer update Telegram
    |--------------------------------------------------------------------------
    */


    $raw =
        file_get_contents(
            "php://input"
        );


    Logger::json(

        "BOT UPDATE RAW",

        $raw,

        BOT_LOG

    );



    $update =
        json_decode(

            $raw,

            true

        );



    if(!$update){


        throw new Exception(

            "Update inválido"

        );

    }



    $debug[]="Update recibido";



    /*
    |--------------------------------------------------------------------------
    | Guardar evento
    |--------------------------------------------------------------------------
    */


    State::load();


    State::set(

        "last_update",

        $update

    );


    State::event(

        "BOT_UPDATE",

        $update

    );


    State::save();



    /*
    |--------------------------------------------------------------------------
    | Procesar mensaje
    |--------------------------------------------------------------------------
    */
    if(isset($update["message"]["web_app_data"])) {


        $rawData =
            $update["message"]["web_app_data"]["data"];


        $sendData =
            json_decode(
                $rawData,
                true
            );



        State::load();


        State::event(

            "SEND_DATA",

            [

                "data"=>$sendData

            ]

        );


        State::save();



        Logger::json(

            "SEND_DATA recibido",

            $sendData,

            BOT_LOG

        );


    }



    if(isset($update["message"])){


        $message =
            $update["message"];



        $chat_id =
            $message["chat"]["id"];



        $text =
            $message["text"] ?? "";



        Logger::info(

            "Mensaje BOT",

            $message,

            BOT_LOG

        );



        /*
        |--------------------------------------------------------------------------
        | Comando START
        |--------------------------------------------------------------------------
        */


        if($text=="/start"){



        TelegramClient::sendMessage(
            $chat_id,
            "🟢 Bot conectado correctamente\n\n".
                "WebApp Debug Studio V1.0",
            [
                "reply_markup"=>json_encode([
                    "inline_keyboard"=>[
                        [
                            [
                                "text"=>"🚀 Abrir Studio",
                                "web_app"=>[
                                    "url"=>"https://bots.perezcompany.com.ar/ontheball/index.php"
                                ]
                            ]
                        ]
                    ]
                ])
            ]
        );

            State::event(

                "BOT_START",

                [

                    "chat_id"=>$chat_id

                ]

            );


            State::save();


        }


        else {


            State::event(

                "BOT_MESSAGE",

                [

                    "chat_id"=>$chat_id,

                    "text"=>$text

                ]

            );


            State::save();


        }


    }



    /*
    |--------------------------------------------------------------------------
    | Tiempo
    |--------------------------------------------------------------------------
    */


    $ms =
        Logger::timerEnd(
            $start
        );


    $debug[]=
        "Tiempo ".$ms." ms";



    jsonResponse(

        true,

        [

            "received"=>true,

            "elapsed_ms"=>$ms

        ],

        $debug

    );



}
catch(Throwable $e){


    Logger::exception($e);


    jsonResponse(

        false,

        [

            "error"=>

            $e->getMessage()

        ],

        $debug,

        500

    );

}