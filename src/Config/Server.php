<?php

namespace App\Config;

/**
 * SERVER
 * Capture server datas
 * 
 * @author Mateodioev <https://github.com/mateodioev>
 */
class Server {

    public array $data;
    public array $get;
    public array $post;
    public array $request;
    public array $files;
    public array $env;

    public function __construct()
    {
        $this->data = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->files = $_FILES;
        $this->env = $_ENV;
    }

    /**
     * Set time zone
     */
    public function SetTimeZone(string $time_zone): void
    {
        date_default_timezone_set($time_zone);
    }

    /**
     * Get took
     */
    public function Took(bool $float = false, int $round = 3)
    {
        $ms = time();
        $s_time = (int) $this->data['REQUEST_TIME'];
        if ($float) {
            $ms = (float) microtime(true);
            $s_time = (float) $this->data['REQUEST_TIME_FLOAT'];
        }
        return round($ms - $s_time, $round);
    }

}