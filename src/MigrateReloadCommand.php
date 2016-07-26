<?php

namespace Hopkins\MigrateReload;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

/**
 * @property Application db
 * @property Application app
 * @property Application schema
 */
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

    /**
     * MigrateReloadCommand constructor.
     * @param Application $application
     */
    public function __construct(Application $application){
        parent::__construct();
        $this->app = $application;
        $this->config = $application['config'];
        /** @var \Illuminate\Database\MySqlConnection db */
        $this->db = $application['db'];

    }

    public function fire()
    {
        /** @var \Illuminate\Database\MySqlConnection $db */
        $db = $this->db;
        if ($this->app->environment() == 'production') {
            $this->error('This is prod. If you meant to do this, use the --force option. If you didn\'t feel free to paypal me some cash as a thank you. mhopkins321@gmail.com');

            return;
        }
        #if (!\App::environment('production')) {

        $tables = $db->select('SHOW TABLES');
        $tables_in_database = 'Tables_in_' . \Config::get('database.connections.mysql.database');
        $db->statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            $db->statement('DROP TABLE '.$table->$tables_in_database);
            $this->info('<info>Dropped: </info>' . $table->$tables_in_database);
        }

        exec('composer dump');
        exec('php artisan clear-compiled');
        exec('php artisan migrate', $migrateOutput);
        $this->info(implode("\n", $migrateOutput));
        $this->info('Migrated');
        exec('php artisan db:seed', $seedOutput);
        $this->info(implode("\n", $seedOutput));
        $this->info('Seeded');

        $db->statement('SET FOREIGN_KEY_CHECKS=1;');
        #}
    }
}
