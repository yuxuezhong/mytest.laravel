<?php

namespace Yxz\LaravelTools\Console;

use Illuminate\Console\Command;
use Yxz\LaravelTools\Support\RegisterApplication;

class Provider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'provider:show {alias?} {--http}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the Provider info.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $alias   = $this->argument('alias');
        $is_http = boolval($this->option('http'));



        $register_app = self::getApp($is_http);

        $providers = $register_app->getProviderDetails();
        //print_r($register_app->getProviderDetails());
        if ($alias) {
            self::showOne($providers, $alias);
        } else {
            self::showList($providers);
        }

        //$loaded_providers = $this->laravel->getLoadedProviders();
        //print_r(array_diff_key($loaded_providers,$providers));
    }

    private static function showList(?array $providers)
    {
        foreach ($providers as $provider => $binding) {
            echo 'Provider is: ' . $provider . PHP_EOL;
            echo 'Binding is : ' . implode($binding['binding'], ',') . PHP_EOL;
            echo 'Instances is : ' . implode($binding['instances'], ',') . PHP_EOL;
            echo '------------------------- ' . PHP_EOL;
        }
    }

    private static function showOne(?array $providers, string $alias)
    {
        foreach ($providers as $provider => $bindings) {
            foreach ($bindings['binding'] as $binding) {
                //echo $binding;
                //echo PHP_EOL;
                //echo $alias;
                //echo PHP_EOL;
                if (!(stripos($binding,$alias)===false)) {
                    echo 'Provider is: ' . $provider . PHP_EOL;
                    echo 'Binding is : ' . implode($bindings['binding'], ',') . PHP_EOL;
                    echo 'Instances is : ' . implode($bindings['instances'], ',') . PHP_EOL;

                    var_dump($bindings['trace']);
                    break 2;
                }
            }
        }
    }

    private function getApp(bool $is_http)
    {
        $register_app = new RegisterApplication($this->laravel->basePath());

        $register_app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $register_app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \App\Console\Kernel::class
        );

        $register_app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );
        if ($is_http) {
            $kernel = $register_app->make(\Illuminate\Contracts\Http\Kernel::class);
            //echo 'bbb';
            $response = $kernel->handle(
                $request = \Illuminate\Http\Request::capture()
            );
        } else {
            $kernel = $register_app->make(\Illuminate\Contracts\Console\Kernel::class);

            $kernel->bootstrap();
        }

        return $register_app;
    }
}
