<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * verify.php
 *
 * Diagnóstico completo del sistema
 * ==========================================================
 */

declare(strict_types=1);


require_once "config.php";
require_once "logger.php";
require_once "state.php";
require_once "telegram_client.php";


$debug=[];

$checks=[];


$start = Logger::timerStart();



try {


    /*
    |--------------------------------------------------------------------------
    | PHP
    |--------------------------------------------------------------------------
    */


    $checks["php"]=[

        "ok"=>true,

        "version"=>PHP_VERSION

    ];


    $debug[]="PHP OK";



    /*
    |--------------------------------------------------------------------------
    | Directorios
    |--------------------------------------------------------------------------
    */


    $folders=[

        "logs"=>LOG_DIR,

        "data"=>DATA_DIR

    ];



    foreach($folders as $name=>$dir){


        $exists =
            is_dir($dir);



        $write =
            $exists &&
            is_writable($dir);



        $checks["folder_".$name]=[

            "exists"=>$exists,

            "writable"=>$write,

            "path"=>$dir

        ];


    }



    $debug[]="Directorios verificados";



    /*
    |--------------------------------------------------------------------------
    | Estado
    |--------------------------------------------------------------------------
    */


    State::load();


    $state =
        State::get();



    $checks["state"]=[

        "ok"=>true,

        "events"=>
            count(
                $state["events"] ?? []
            )

    ];



    /*
    |--------------------------------------------------------------------------
    | Telegram Bot API
    |--------------------------------------------------------------------------
    */


    $bot =
        TelegramClient::getMe();



    $checks["telegram"]=[

        "ok"=>
            $bot["ok"] ?? false,

        "bot"=>
            $bot["result"] ?? null

    ];



    $debug[]="Telegram consultado";



    /*
    |--------------------------------------------------------------------------
    | Webhook
    |--------------------------------------------------------------------------
    */


    $webhook =
        TelegramClient::getWebhookInfo();



    $checks["webhook"]=[

        "ok"=>
            $webhook["ok"] ?? false,

        "data"=>
            $webhook["result"] ?? null

    ];



    /*
    |--------------------------------------------------------------------------
    | Tiempo
    |--------------------------------------------------------------------------
    */


    $ms =
        Logger::timerEnd($start);



    State::load();


    State::event(

        "VERIFY",

        [

            "time_ms"=>$ms,

            "checks"=>$checks

        ]

    );


    State::save();



    jsonResponse(

        true,

        [

            "elapsed_ms"=>$ms,

            "checks"=>$checks

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