<?php

namespace Hopkins\MigrateReload;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;

class MigrateReloadCommand extends Command
{
    protected $name = 'migrate:reload';
    protected $description = 'Drop All Tables Systematically.';
    /**
     * @var Application
     */
    private $application;
    /**
     * @var Repository
     */
    private $config;

    public function __construct(Application $application, Repository $config){

        $this->application = $application;
        $this->config = $config;
    }

    public function handle()
    {
        if ($this->application->environment() == 'production') {
            $this->error('This is prod. If you meant to do this, use the --force option. If you didn\'t feel free to paypal me some cash as a thank you. mhopkins321@gmail.com');

            return;
        }
        #if (!\App::environment('production')) {
        $tables = \DB::select('SHOW TABLES');
        $tables_in_database = 'Tables_in_' . \Config::get('database.connections.mysql.database');
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            \Schema::drop($table->$tables_in_database);
            $this->info('<info>Dropped: </info>' . $table->$tables_in_database);
        }

        exec('php artisan migrate --force -vvv', $migrateOutput);
        $this->info(implode("\n", $migrateOutput));
        $this->info('Migrated');
        exec('php artisan db:seed --force -vvv', $seedOutput);
        $this->info(implode("\n", $seedOutput));
        $this->info('Seeded');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        #}
    }
}
