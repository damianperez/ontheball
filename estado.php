<?php
declare(strict_types=1);
require_once "config.php";
require_once "logger.php";
require_once "state.php";
$debug=[];
try{
    Logger::info(
        "Consultar estado"
    );
    State::load();
    $estado = State::get();
    $debug[]="Estado leído";
    jsonResponse(
        true,
        $estado,
        $debug
    );
}
catch(Throwable $e){
    Logger::exception($e);
    jsonResponse(
        false,
        $e->getMessage(),
        [],
        500
    );
}