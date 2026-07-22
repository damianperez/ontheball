<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 * guardar.php
 * ----------------------------------------------------------
 * Recibe datos desde fetch()
 * ==========================================================
 */

declare(strict_types=1);

require_once "config.php";
require_once "logger.php";
require_once "state.php"; 

//pedirle las modificaciones al estado
$timer = Logger::timerStart();

$debug = [];

try {

    $debug[] = "Inicio guardar.php";

    /*
    |----------------------------------------------------------
    | Leer RAW
    |----------------------------------------------------------
    */

    $raw = file_get_contents("php://input");

    $debug[] = "RAW recibido";

    Logger::info(
        "RAW POST",
        $raw,
        POST_LOG
    );

    /*
    |----------------------------------------------------------
    | Decodificar JSON
    |----------------------------------------------------------
    */

    $data = json_decode($raw, true);

    if (!$data) {

        throw new Exception("JSON inválido");

    }

    Logger::json(
        "POST JSON",
        $data,
        POST_LOG
    );

    $debug[] = "JSON correcto";

    /*
    |----------------------------------------------------------
    | Leer estado anterior
    |----------------------------------------------------------
    */

    $estado = [];

    if (file_exists(STATE_FILE)) {

        $estado = json_decode(

            file_get_contents(STATE_FILE),

            true

        );

    }

    /*
    |----------------------------------------------------------
    | Actualizar estado
    |----------------------------------------------------------
    */

    $estado["updated"] = date("Y-m-d H:i:s");

    $estado["last_post"] = $data;

    if (!isset($estado["history"])) {

        $estado["history"] = [];

    }

    $estado["history"][] = [

        "fecha" => date("H:i:s"),

        "tipo" => "POST",

        "payload" => $data

    ];

    /*
    |----------------------------------------------------------
    | Limitar historial
    |----------------------------------------------------------
    */

    if (count($estado["history"]) > 100) {

        $estado["history"] = array_slice(

            $estado["history"],

            -100

        );

    }

    /*
    |----------------------------------------------------------
    | Guardar
    |----------------------------------------------------------
    */

    file_put_contents(

        STATE_FILE,

        json_encode(

            $estado,

            JSON_PRETTY_PRINT |

            JSON_UNESCAPED_UNICODE

        )

    );

    $debug[] = "estado.json actualizado";

    /*
    |----------------------------------------------------------
    | Tiempo
    |----------------------------------------------------------
    */

    $ms = Logger::timerEnd($timer);

    Logger::info(

        "Tiempo guardar.php",

        $ms . " ms",

        POST_LOG

    );

    $debug[] = "Tiempo: {$ms} ms";

    /*
    |----------------------------------------------------------
    | Respuesta
    |----------------------------------------------------------
    */

    jsonResponse(

        true,

        [

            "mensaje" => "POST recibido correctamente",

            "payload" => $data,

            "elapsed_ms" => $ms

        ],

        $debug

    );

}
catch (Throwable $e) {

    Logger::exception($e);

    jsonResponse(

        false,

        [

            "error" => $e->getMessage()

        ],

        $debug,

        500

    );

}