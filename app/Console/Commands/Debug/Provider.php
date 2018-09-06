<?php

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;
use App;

class Provider extends Command
{
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,

    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aaaprovider:show {alias?} {--http}';

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

        $loaded_providers = App::getLoadedProviders();

        $register_app = self::getApp($is_http);

        $providers = $register_app->getProviderDetails();
        //print_r($register_app->getProviderDetails());
        if ($alias) {
            self::showOne($providers, $alias);
        } else {
            self::showList($providers);
        }
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
        $register_app = new RegisterApplication(App::basePath());
        //$providers = App::getLoadedProviders();
        //
        //foreach ($providers as $name => $provider)
        //{
        //    $register_app->register($name);
        //}

        //$register_app->bootstrapWith($this->bootstrappers);
        //
        //$register_app->registerConfiguredProviders();
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
