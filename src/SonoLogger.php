<?php

declare(strict_types=1);

namespace Vosouza\Sonomais;

require __DIR__ . '/../vendor/autoload.php';

use Katzgrau\KLogger\Logger as KLogger;

class SonoLogger{
    private static ?KLogger $logger = null;

    public static function initialize(): void
    {
        if (self::$logger === null) {
            self::$logger = new KLogger(__DIR__.'/../logs');;
        }
    }

    public static function log($message){
        self::initialize();
        self::$logger->debug($message);
    }
}