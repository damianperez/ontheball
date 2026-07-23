<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * clear.php
 *
 * Limpieza controlada del sistema
 * ==========================================================
 */
declare(strict_types=1);

require_once "config.php";
require_once "logger.php";
require_once "state.php";

$debug=[];

try {

    $debug[]="Inicio limpieza";

    /*
    |--------------------------------------------------------------------------
    | Limpiar estado
    |--------------------------------------------------------------------------
    */

    State::load();

    State::event(
        "CLEAR",
        [
            "fecha"=>date(
                "Y-m-d H:i:s"
            )
        ]
    );

    State::set(
        "last_post",
        []
    );

    State::set(
        "last_senddata",
        []
    );

    State::set(
        "last_update",
        []
    );

    State::set(
        "events",
        []
    );

    State::save();

    $debug[]="Estado reiniciado";

    /*
    |--------------------------------------------------------------------------
    | Limpiar logs
    |--------------------------------------------------------------------------
    */

    $logs=[
        BOT_LOG,
        POST_LOG,
        TELEGRAM_LOG,
        ERROR_LOG
    ];

    $clean=[];

    foreach($logs as $log){

        if(file_exists($log)){

            file_put_contents(
                $log,
                ""
            );

            $clean[]=$log;
        }

    }

    $debug[]=
        count($clean)
        ." logs limpiados";

    Logger::info(
        "Sistema limpiado",
        $clean
    );

    jsonResponse(
        true,
        [
            "message"=>
                "Sistema limpiado",
            "logs"=>$clean
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