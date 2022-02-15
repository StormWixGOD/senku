<?php 

namespace App\Controllers;

use App\Models\Bot;
use App\Config\{Logger, Server};
use App\Db\{Connection, Query};


class Start {
    
    public $server;
    public $bot;
    public $up;
    public $db;

    private string $path;

    /**
     * @param string $app_path The path of the application
     * @param boolean $use_db Whether to use the database
     * @param boolean $use_webhook Whether to use the telegram webhook
     * @param boolean $use_updates Whether to use the telegram GetUpdates
     */
    public function __construct(string  $app_path, bool $use_db = true, bool $use_webhook = true, bool $use_updates = false)
    {
        $dotenv = \Dotenv\Dotenv::createImmutable($app_path);
        $dotenv->load();
        $this->path = $app_path;
        $this->server = new Server();
        $this->bot = new Bot($_ENV['BOT_TOKEN']);
        $this->Server();
        
        if ($use_webhook) $this->up = $this->bot->GetData();
        if ($use_updates) $this->up = $this->bot->GetUpdates();
        if ($use_db) $this->Db();
    }

    /**
     * Server config
     */
    private function Server()
    {
        Logger::Activate($this->path . '/src/logs/php-error.log', $_ENV['MAX_EXECUTION_TIME']);
        $this->server->SetTimeZone($_ENV['TIME_ZONE']);
    }

    /**
     * App mysql database
     */
    private function Db() {
        Connection::Prepare($_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->db = new Query;
    }
}