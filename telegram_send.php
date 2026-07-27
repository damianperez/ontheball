<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * telegram_send.php
 *
 * Endpoint para enviar mensajes desde la WebApp
 * hacia el usuario mediante Bot API
 * ==========================================================
 */


declare(strict_types=1);


require_once "config.php";
require_once "logger.php";
require_once "state.php";
require_once "telegram_client.php";


$debug=[];


try {


    $start = Logger::timerStart();


    $debug[]="Inicio telegram_send.php";


    /*
    |--------------------------------------------------------------------------
    | Leer JSON recibido
    |--------------------------------------------------------------------------
    */


    $raw=file_get_contents(
        "php://input"
    );


    Logger::info(

        "Telegram SEND RAW",

        $raw,

        TELEGRAM_LOG

    );


    $data=json_decode(

        $raw,

        true

    );


    if(!$data){

        throw new Exception(
            "JSON inválido"
        );

    }


    $debug[]="JSON correcto";



    /*
    |--------------------------------------------------------------------------
    | Obtener usuario
    |--------------------------------------------------------------------------
    */


    $user_id =
        $data["telegram"]["id"] ?? null;



    if(!$user_id){

        throw new Exception(
            "No existe telegram.id"
        );

    }


    $debug[]=
        "Usuario ".$user_id;



    /*
    |--------------------------------------------------------------------------
    | Preparar mensaje
    |--------------------------------------------------------------------------
    */


    $nombre =
        $data["form"]["nombre"]
        ?? "usuario";


    $mensaje =
        $data["form"]["mensaje"]
        ?? "Mensaje vacío";



    $texto =

        "🟢 <b>Telegram WebApp Debug</b>\n\n"

        ."Hola ".$nombre."\n\n"

        .$mensaje;



    /*
    |--------------------------------------------------------------------------
    | Enviar Telegram
    |--------------------------------------------------------------------------
    */


    $resultado =
        TelegramClient::sendMessage(

            $user_id,

            $texto

        );



    $debug[]=
        "Telegram respondió";



    /*
    |--------------------------------------------------------------------------
    | Guardar evento
    |--------------------------------------------------------------------------
    */


    State::load();


    State::event(

        "TELEGRAM_SEND",

        [

            "user"=>$user_id,

            "response"=>$resultado

        ]

    );


    State::save();



    $time =
        Logger::timerEnd($start);



    jsonResponse(

        true,

        [

            "telegram"=>$resultado,

            "elapsed_ms"=>$time

        ],

        $debug

    );



}
catch(Throwable $e){


    Logger::exception($e);


    jsonResponse(

        false,

        [

            "error"=>$e->getMessage()

        ],

        $debug,

        500

    );

}