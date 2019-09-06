<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class executeOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will execute all commands used in Order Web API';

    /**
     * Create a new command instance.
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
        $this->call('optimize:clear');

        $this->info('Starting Migrations & Data Seeding -------> ');
        $this->call('migrate');
        $this->call('db:seed', ['--class' => 'DistanceTableSeeder']);
    }
}
