<?php

declare(strict_types=1);

namespace Yxz\LaravelTools\Support;

use Illuminate\Log\Writer;
use Monolog\Logger;

final class BLogger
{
    public const LOG_ERROR = 'error';
    public const LOG_INFO  = 'info';

    private static $loggers = [];

    public static function getLogger(?string $type = self::LOG_ERROR, ?string $path = ''): Writer
    {
        if (empty(self::$loggers[$type])) {
            if (!$path) {
                $path = '/tmp/logs/log';
            }

            self::$loggers[$type] = new Writer(new Logger($type));
            self::$loggers[$type]->useDailyFiles($path . $type);
        }

        $log = self::$loggers[$type];

        return $log;
    }
}
