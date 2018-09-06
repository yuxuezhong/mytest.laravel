<?php

namespace Yxz\LaravelTools\Console;

use Illuminate\Console\Command;

class CommandFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:file {alias?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the Command file.';

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

        if(!$alias)
        {
            return $this->call('list',[],$this->output);

        }

        $command = $this->getApplication()->find($alias);
        if(!$alias)
        {
            echo 'Command is not found.';
            return;
        }

        echo get_class($command).PHP_EOL;

    }


}
