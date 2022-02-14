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

    public function __construct(string  $app_path, bool $use_db = true, bool $use_webhook = true)
    {
        $dotenv = \Dotenv\Dotenv::createImmutable($app_path);
        $dotenv->load();
        $this->path = $app_path;
        $this->server = new Server();
        $this->bot = new Bot($_ENV['BOT_TOKEN']);
        $this->Server();
        
        if ($use_webhook) $this->up = $this->bot->GetData();
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