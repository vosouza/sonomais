<?php

declare(strict_types=1);

namespace Vosouza\Sonomais;

class SessionRegistry
{
    public static String $appVersion = "1.0.4";
    private static ?array $session = null;
    private static String $isLoggedIn = "isloggedin";
    private static String $userId = "userid";


    public static function initialize(): void
    {
        if (self::$session === null) {
            session_start();
            self::$session = &$_SESSION;
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$session[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        self::$session[$key] = $value;
    }

    public static function destroy(): void
    {
        session_destroy();
        self::$session = null;
    }

    public static function isLoggedIn(): bool{
        return self::$session[self::$isLoggedIn] ?? false;
    }

    public static function setLogginIn(bool $value): void{
        self::$session[self::$isLoggedIn] = $value;
    }

    public static function getUserId(): bool{
        return self::$session[self::$userId] ?? false;
    }

    public static function setUserId($value): void{
        self::$session[self::$userId] = $value;
    }

    public static function getBaseUrl(): string{
        if ($_SERVER['SERVER_NAME'] === 'localhost'){
            return 'http://localhost:8080/';
        }else if($_SERVER['SERVER_NAME'] === 'sonomais.com') {
            return  'sonomais.com/';
        } else {
            return 'https://darkgreen-moose-358229.hostingersite.com/';
        }
    }
}