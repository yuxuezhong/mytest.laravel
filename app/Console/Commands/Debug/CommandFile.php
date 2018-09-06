<?php

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;
use App;


class CommandFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aaacommand:file {alias?}';

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
            return $this->getApplication()->call('list',[],$this->output);

        }
       //var_dump(array_keys($this->getApplication()->all())) ;

        $command = $this->getApplication()->find($alias);
        if(!$alias)
        {
            echo 'Command is not found.';
            return;
        }

        var_dump(new \ReflectionClass($command));
    }


}
