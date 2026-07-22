<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 *
 * webhook_test.php
 *
 * Simulador de Updates Telegram
 * ==========================================================
 */

declare(strict_types=1);


require_once "config.php";
require_once "logger.php";


header(
    "Content-Type: application/json; charset=utf-8"
);



try {


    /*
    |--------------------------------------------------------------------------
    | Datos recibidos
    |--------------------------------------------------------------------------
    */


    $input =
        file_get_contents(
            "php://input"
        );



    if($input){


        $update =
            json_decode(

                $input,

                true

            );


    }
    else {


        /*
        |--------------------------------------------------------------------------
        | Update de prueba por defecto
        |--------------------------------------------------------------------------
        */


        $update=[


            "update_id"=>
                time(),


            "message"=>[


                "message_id"=>1,


                "from"=>[


                    "id"=>123456789,

                    "first_name"=>"Test",

                    "username"=>"tester"

                ],


                "chat"=>[


                    "id"=>123456789,

                    "type"=>"private"

                ],


                "date"=>time(),


                "text"=>"/start"


            ]


        ];


    }



    /*
    |--------------------------------------------------------------------------
    | Enviar internamente a bot.php
    |--------------------------------------------------------------------------
    */


    $url =
        WEBAPP_URL."bot.php";



    $curl =
        curl_init();



    curl_setopt_array(

        $curl,

        [

            CURLOPT_URL=>$url,

            CURLOPT_RETURNTRANSFER=>true,

            CURLOPT_POST=>true,

            CURLOPT_POSTFIELDS=>

                json_encode($update),

            CURLOPT_HTTPHEADER=>[

                "Content-Type: application/json"

            ]

        ]

    );



    $response =
        curl_exec($curl);



    $error =
        curl_error($curl);



    curl_close($curl);



    if($error){


        throw new Exception(
            $error
        );


    }



    echo json_encode(

        [

            "ok"=>true,

            "sent"=>$update,

            "bot_response"=>

                json_decode(

                    $response,

                    true

                )

        ],

        JSON_PRETTY_PRINT |

        JSON_UNESCAPED_UNICODE

    );



}
catch(Throwable $e){


    echo json_encode(

        [

            "ok"=>false,

            "error"=>

                $e->getMessage()

        ],

        JSON_PRETTY_PRINT

    );


}