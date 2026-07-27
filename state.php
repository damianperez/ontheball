<?php
/**
 * ==========================================================
 * Telegram WebApp Debug Studio
 * state.php
 * ----------------------------------------------------------
 * Administración del estado global
 * ==========================================================
 */
declare(strict_types=1);
require_once "config.php";
class State
{
    private static array $state = [];
    public static function load(): void
    {
        if (!file_exists(STATE_FILE)) {
            self::$state = [
                "updated" => "",
                "last_post" => [],
                "last_senddata" => [],
                "last_update" => [],
                "events" => []
            ];
            return;
        }
        self::$state = json_decode(
            file_get_contents(STATE_FILE),
            true
        ) ?? [];
    }
    public static function save(): void
    {
        self::$state["updated"] =
            date("Y-m-d H:i:s");
        file_put_contents(
            STATE_FILE,
            json_encode(
                self::$state,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_UNICODE
            )
        );
    }
    public static function set(string $key, mixed $value): void
    {
        self::$state[$key] = $value;
    }
    public static function get(): array
    {
        return self::$state;
    }
    public static function event(
        string $type,
        mixed $data = null
    ): void {
        if (!isset(self::$state["events"])) {
            self::$state["events"] = [];
        }
        self::$state["events"][] = [
            "time" => date("H:i:s"),
            "type" => $type,
            "data" => $data
        ];
        if (count(self::$state["events"]) > 200) {
            self::$state["events"] = array_slice(
                self::$state["events"],
                -200
            );
        }
    }
}