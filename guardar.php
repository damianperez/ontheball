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

            "message"=>
            "POST recibido correctamente"

        ],

        [

            "POST guardado",

            "Evento creado"

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