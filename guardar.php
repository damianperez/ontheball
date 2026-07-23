<?php
declare(strict_types=1);
require_once "config.php";
require_once "logger.php";
require_once "state.php";

$debug=[];

try {

    $raw=file_get_contents(
        "php://input"
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

    Logger::json(
        "POST recibido",
        $data,
        POST_LOG
    );

    /*
    |--------------------------------------------------------------------------
    | Guardar estado
    |--------------------------------------------------------------------------
    */

    State::load();

    State::set(
        "last_post",
        $data
    );

    $tipo = $data["evento"] ?? "POST";
    State::event(
        $tipo,
        $data
    );

    State::save();

    jsonResponse(

            true,

            [

                "message"   => "Datos guardados correctamente",

                "event"     => "POST",

                "timestamp" => date("Y-m-d H:i:s"),

                "state" => [

                    "events" => count(
                        State::get("events", [])
                    )

                ]

            ],

            [

                "JSON recibido",
                "Estado actualizado",
                "Evento registrado",
                "Archivo guardado"

            ]

        );

}
catch(Throwable $e){

    Logger::exception($e);

    jsonResponse(
        false,
        [
            "error"=>$e->getMessage()
        ]
    );
}