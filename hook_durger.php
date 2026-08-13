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

$BOT_TOKEN = getenv('BOT_TOKEN') ?: '1618069377:AAGPFW-KGu-vCN0xUudrK6FRqWdD3AizrSc';
$BOT_API = getenv('BOT_API') ?: 'https://api.telegram.org/bot' . $BOT_TOKEN . '/';
$WEBAPP_URL = getenv('WEBAPP_URL') ?: 'https://bots.perezcompany.com.ar/ontheball/';
//require_once "config_durger.php";
require_once "logger.php";
require_once "state.php";
require_once "telegram_client.php";
require_once 'validate.php';
$debug = [];
try {
    $start = Logger::timerStart();
    /*
    |--------------------------------------------------------------------------
    | Leer update Telegram
    |--------------------------------------------------------------------------
    */
    $raw =
        file_get_contents(
            "php://input"
        );
    file_put_contents(
        "logs/raw_update.log",
        date("Y-m-d H:i:s") . "\n" .
            file_get_contents("php://input") .
            "\n\n",
        FILE_APPEND
    );
    Logger::json(
        "BOT UPDATE RAW",
        $raw,
        BOT_LOG
    );
    $update =  json_decode($raw, true);
    Logger::json("UPDATE", $update, BOT_LOG);
    /*
    echo "<pre>";
    print_r($update);
    echo "</pre>";
    exit;
    */
    if (!$update) {
        throw new Exception(
            "Update durger inválido"
        );
    }
    /*
|--------------------------------------------------------------------------
| Telegram WebApp sendData
|--------------------------------------------------------------------------
*/
    if (isset($update["message"]["web_app_data"])) {
        $raw = $update["message"]["web_app_data"]["data"];
        $data = json_decode(            $raw,            true        );
        if (!$data) {            $data = ["raw" => $raw];        }
        State::load();
        State::event(            "SEND_DATA",            [
                "chat_id" =>        $update["message"]["chat"]["id"],
                "payload" =>        $data            ]        );
        State::save();
        Logger::json("SEND_DATA recibido",$data,BOT_LOG);
        /*
        Respuesta opcional al usuario
        */
        TelegramClient::sendMessage(
            $update["message"]["chat"]["id"],
            'recibido: ' . var_export($data, true),
            [
                "reply_markup" =>json_encode(
                    [ "inline_keyboard" => [[[    "text" => "✅ OK",    "callback_data" => "ok"]]]]
                )
            ]
            //"✅ SendData recibido correctamente:".var_export($data, true)
        /*
                Si viene 'Nativo, viene : 
                     array (
                        'time' => '2026-07-27T15:17:29.179Z',
                        'telegram' => 
                        array (
                            'id' => NULL,
                            'username' => '',
                            'first_name' => '',
                            'last_name' => '',
                            'language' => '',
                        ),
                        'form' => 
                        array (
                            'nombre' => 'd',
                            'mensaje' => 'd',
                        ),
                        'origin' => 'sendData',
                        )    */
        );
    }
    $debug[] = "Updatee recibido";
    $debug[] = var_export($update, true);
    /* Guardar evento    */
    State::load();
    State::set( "last_update",        $update    );
    State::event("BOT_UPDATE",        $update    );
    State::save();      
    /*
    |--------------------------------------------------------------------------
    | Procesar mensaje
    |--------------------------------------------------------------------------
    de la mini app, el $update:
    array (\n  'order_data' => '[{\"id\":2,\"count\":3}]',\n  
    'comment' => 'ssss',\n  
    'mode' => 'pedidosnet',\n  
    'invoice' => 1,\n  
    '_auth' => 'query_id=AAEHCIEnAAAAAAcIgScbXzAO&user=%7B%22id%22%3A662767623%2C%22first_name%22%3A%22Dami%C3%A1n%22%2C%22last_name%22%3A%22%22%2C%22username%22%3A%22PerezDamian%22%2C%22language_code%22%3A%22es%22%2C%22allows_write_to_pm%22%3Atrue%2C%22photo_url%22%3A%22https%3A%5C%2F%5C%2Ft.me%5C%2Fi%5C%2Fuserpic%5C%2F320%5C%2FP3a2zzEGYAcXB3ZanpskiS58EhW8UKFJLDuip6tS6H0.svg%22%7D&auth_date=1786575664&signature=iS_R1FqJdxtyECUBG9Ys_sLuTXUL7zLc7oi6llsYLnkKm-f4s5xqhrNIfmrJ2c98HXGajOTEXSJiTpsJbxuTDA&hash=df656ae8006d77352fc015960ef56e1aff2966a6d570752ba38cad426f84b244',\n  'method' => 'sendMessage',\n)",
    */
    if (isset($update["message"]["web_app_data"])) {
        $rawData = $update["message"]["web_app_data"]["data"];
        $sendData =  json_decode($rawData, true);
        State::load();
        State::event("SEND_DATA",["data" => $sendData]);
        State::save();
        Logger::json("SEND_DATA recibido",$sendData,BOT_LOG);
    }
    if (isset($update["message"])) {
        $message = $update["message"];
        $chat_id = $message["chat"]["id"];
        $text = $message["text"] ?? "xxx";
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
        // Create the inline keyboard and add a row of buttons
        if ($text == "/start") {
            $keyboard = [
                "inline_keyboard" => [
                    [
                        [
                            "text" => "🚀 Abrir Studio",
                            "web_app" => ["url" => WEBAPP_URL
                            ]
                        ]
                    ]
                ],
                "resize_keyboard" => true
            ];


            $keyboard = [ 
                "inline_keyboard" => [
                   [
                    ["text" => "🚀 Abrir Studio", "web_app" => ["url" => WEBAPP_URL]],
                     ["text" => "App", "web_app" => ["url" => "https://bots.perezcompany.com.ar/durger-king/public"]],
                    ["text" => "Demo", "web_app" => ["url" => "https://bots.perezcompany.com.ar/durger-king/public/demo.php"]]    
                 ]
                ],
                "resize_keyboard" => true,
            ]
                ;
            TelegramClient::sendMessage(
                $chat_id,
                "Abrir Debug Studio",
                [
                    "reply_markup" =>
                    json_encode($keyboard)
                ]
            );
            State::event(
                "BOT_START",
                [
                    "chat_id" => $chat_id
                ]
            );
            State::save();
        } else {
            State::event("BOT_MESSAGE",["chat_id" => $chat_id,"text" => $text]);
            State::save();
        }
    }
    $ms =Logger::timerEnd( $start   );
    $debug[] =  "Tiempo " . $ms . " ms";
    //REspondo via Json

    
    $stringData = $update['_auth'];
    $resultado = Validate::extractTelegramData($stringData);   
    $debug[] = 'validated_data';
    $debug[] = var_export($resultado, true);

    if (!isset($resultado['user'])) {
        throw new Exception("No se pudo extraer el usuario de los datos de inicio de Telegram.");
    }
    if (!isset($update['user_id'])) {
        $update['user_id'] = $resultado['user']['id'];
    }
    //} 



    ResponseDurger( true, $update, $debug, 200);
    
} catch (Throwable $e) {
    Logger::exception($e);
    jsonResponse(false,[ "error" =>  $e->getMessage()],     $debug, 500);
}


function ResponseDurger( bool $ok, mixed $data = null, array $debug = [], int $http = 200)
{
    

   if (!is_array($data) || !isset($data["method"]) || !isset($data["mode"]) || !isset($data["_auth"])) {
        $ok = false;    
        http_response_code($http);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(        [
            "ok" => $ok,
            "data" => $data,
            "error" => "the data must be an array with 'method' and 'mode' keys",
            "time" => date('Y-m-d H:i:s'),
            "memory" => memory_get_usage(true),
            "php" => PHP_VERSION,
            "debug" => $debug
        ],  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE    );
        exit;
        }
   if ( $data["method"] === "makeOrder" && $data["mode"] === "pedidosnet" )
    {
        jsonResponseDurger( $ok, $data, $debug, $http);
    } elseif ( $data["method"] === "makeOrder" && $data["mode"] === "durger" ){
        jsonResponse( $ok, $data, $debug, $http);
    } elseif ( $data["method"] === "sendMessage" && $data["mode"] === "pedidosnet" )
    {
        jsonResponse( $ok, $data, $debug, $http);
        TelegramClient::sendMessage(
                $data["user_id"],
                "Orden recibida correctamente, gracias por su compra!"
                
            );
    
    } 
    
}




function jsonResponseDurger( bool $ok, mixed $data = null, array $debug = [], int $http = 200): never 
{
    http_response_code($http);
    header('Content-Type: application/json; charset=utf-8');
    //[{id: 1, count: 3}, {id: 2, count: 4}, {id: 8, count: 6}, {id: 9, count: 2}]
    $data['order_id']='';
    $items_pedidos = json_decode($data["order_data"], true) ?? [];
    $total_price = 0;    
    foreach ($items_pedidos as $index => $item) {
        if (isset($item["id"])) {
            $data['order_id'] .= str_pad( $item["id"] . $item["count"], 4, '0', STR_PAD_LEFT );
            $total_price += precios()[$item["id"]]["price"] * ($item["count"] ?? 1);
        }        
    }    
    $order_text = parseOrder($data["order_data"]);
    $order_text .= "\nTotal: $" . number_format($total_price, 2);
    //[{id: 1, count: 3}, {id: 2, count: 4}, {id: 8, count: 6}, {id: 9, count: 2}]
    echo json_encode(
        [
            "ok" => $ok,            
            "debug" => $debug,
            "recibido" => [
                "DATA" => $data,
                "comment" => $data["comment"],
                "method" => $data["method"],
                "mode" => $data["mode"],
                "order_data" => $items_pedidos,               
                ],
           "order_id" => $data['order_id'] ?? (9999 + random_int(1, 10)),
           "invoice_url" => $data['invoice_url'] ?? 'http://eldia.com.ar?order_id=' . ($data['order_id'] ?? 'error'), 
            "respuesta" => $order_text,
            "error" => false,
            "time" => date('Y-m-d H:i:s'),
            "memory" => memory_get_usage(true),
            "php" => PHP_VERSION,            
            "debug" => $debug
        ],
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );
    exit;
}

 function parseOrder(string $order = '[]'): string
    {
        if ($order == '[]') {
            return 'Nothing';
        }

        $order = json_decode($order, true);
        $order_text = '';
        $store_items = precios();
        foreach ($order as $item) {
            $order_text .= (
                $item['count'] . 'x ' .
                $store_items[$item['id']]['name'] . ' ' .
                $store_items[$item['id']]['emoji'] . ' $' .
                ($store_items[$item['id']]['price'] * $item['count']) . "\n"
            );
        }
        return $order_text;
    }

function precios()
{
     //<div class="cafe-item js-item" data-item-id="1" data-item-price="4990">
    return $products = [
    1 => [
        'name' => 'Burger',
        'emoji' => '🍔',
        'price' => 4.99,
    ],
    2 => [
        'name' => 'Fries',
        'emoji' => '🍟',
        'price' => 1.49,
    ],
    3 => [
        'name' => 'Hotdog',
        'emoji' => '🌭',
        'price' => 3.49,
    ],
    4 => [
        'name' => 'Tako',
        'emoji' => '🌮',
        'price' => 3.99,
    ],
    5 => [
        'name' => 'Pizza',
        'emoji' => '🍕',
        'price' => 7.99,
    ],
    6 => [
        'name' => 'Donut',
        'emoji' => '🍩',
        'price' => 1.49,
    ],
    7 => [
        'name' => 'Popcorn',
        'emoji' => '🍿',
        'price' => 1.99,
    ],
    8 => [
        'name' => 'Coke',
        'emoji' => '🥤',
        'price' => 1.49,
    ],
    9 => [
        'name' => 'Cake',
        'emoji' => '🍰',
        'price' => 10.99,
    ],
    10 => [
        'name' => 'Icecream',
        'emoji' => '🍦',
        'price' => 5.99,
    ],
    11 => [
        'name' => 'Cookie',
        'emoji' => '🍪',
        'price' => 3.99,
    ],
    12 => [
        'name' => 'Flan',
        'emoji' => '🍮',
        'price' => 7.99,
    ],
];

}