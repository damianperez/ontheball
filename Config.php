<?php

namespace OxMohsen\TgBot;

class Config
{
    /**
     * your bot token (get it from botfather).
     *
     * @var string
     */
    public const BOT_TOKEN = '1618069377:AAGPFW-KGu-vCN0xUudrK6FRqWdD3AizrSc';

    /**
     * your bot username `without @`.
     *
     * @var string
     */
    public const BOT_USERNAME = 'ontheballbot';

    /**
     * Secret key required to access the webhook.
     *
     * @var string
     */
    public const SECRET = 'SUPPER_SECRET_TEXT';

    /**
     * the url of webhook file.
     *
     * @var string
     */
    public const WEBHOOK_URL = 'https://bots.perezcompany.com.ar/ontheball/TgBot.php';

    /**
     * the url of web app file.
     *
     * @var string
     */
    public const WEBAPP_URL = 'https://bots.perezcompany.com.ar/ontheball/web/index.html';

    /**
     * all paths for your custom commands.
     *
     * @var array
     */
    public const COMMAND_PATH = [__DIR__ . '/MyCommands'];

    /**
     * all IDs of admin users.
     *
     * @var array
     */
    public const ADMINS = [662767623, 7552023525];
    /**
     * your MySQL database credentials.
     *
     * @var array
     */
    public const SQL_DB = [
        'host'     =>  'localhost',
        'user'     => 'damian',
        'password' => 'Damian200',
        'database' => 'bots',
    ];
}
