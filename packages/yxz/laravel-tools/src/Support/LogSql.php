<?php
/**
 * Created by PhpStorm.
 * User: yuxuezhong
 * Date: 26/03/2018
 * Time: 11:48 AM
 */

namespace Yxz\LaravelTools\Support;

use Illuminate\Database\Events\QueryExecuted;

class LogSql
{
    /** @var array */
    protected $config;

    /**
     *
     * @param array $config
     */
    public function __construct(array $config
    )
    {
        $this->config = $config;
    }

    private static function formatSql($sql)
    {
        foreach ($sql->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else {
                if (is_string($binding)) {
                    $sql->bindings[$i] = "'$binding'";
                }
            }
        }
        $query = str_replace(['%', '?'], ['%%', '%s'], $sql->sql);
        $query = vsprintf($query, $sql->bindings);

        return $query;
    }

    public function printSql(QueryExecuted $sql)
    {
        if (!$this->config['filter']) {
            return;
        }
        $query = self::formatSql($sql);

        $log_classes = $this->config['filter'];

        $log_classes = str_replace('\\', '\\\\', $log_classes);
        $log_classes = str_replace('*', '\w*', $log_classes);

        //if(BLogger::$is_write)
        //{

        $need_class       = false;
        $actual_log_class = '';

        $debug_trances = debug_backtrace(2, 20);

        foreach ($debug_trances as $debug_trance) {
            foreach ($log_classes as $log_class) {
                $match_infos = explode(':', $log_class);

                if (preg_match('/' . $match_infos[0] . '/', trim($debug_trance['class'], '\\'))) {
                    if (isset($match_infos[1])) {
                        if ($match_infos[1] === $debug_trance['function']) {
                            $actual_log_class = $debug_trance;
                            $need_class       = true;
                            break 2;
                        }
                    } else {
                        $actual_log_class = $debug_trance;
                        $need_class       = true;
                        break 2;
                    }
                }
            }
        }

        if ($need_class) {
            if ($this->config['log_file_path']) {
                $log = BLogger::getLogger('info', $this->config['log_file_path']);
                $log->info('class is : ' . $actual_log_class['class']);
                $log->info(',sql is :' . $query);
                $log->info('-------------------------------------------------');
            } else {
                //var_dump($classes);
                print_r($actual_log_class);
                echo ',sql is :' . $query . PHP_EOL;
                echo '--------------' . PHP_EOL;
            }
        }
    }
}
