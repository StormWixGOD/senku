<?php 

namespace App\Models;

use App\Config\Request;


class Bot {
    const API_URL = 'https://api.telegram.org/bot';
    public string $token = '';
    public string $endpoint = '';

    private $res;
    private $result;

    public $content;
    public $update;


    /**
     * Add bot token and set api endpoint
     */
    public function __construct(string $bot_token)
    {
        $this->token = $bot_token;
        $this->endpoint = self::API_URL . $this->token . '/';
    }

    /**
     * Send request to telegram api
     */
    private function request(string $method, ?array $datas=[], bool $decode = true):mixed
    {
        $url = $this->endpoint . $method;
        $this->res = Request::Post($url, null, $datas)['response'];
        $this->result = ($decode) ? json_decode($this->res) : $this->res;
        if (!$this->result->ok) {
            error_log('[bot] Method ' . $method . ' failed: ' . json_encode($datas));
            error_log('[bot] Description: ' . $this->result->description);
        }
        return $this->result;
    }

    /**
     * Send any method
     */
    public function __call($name, $arguments)
    {
        return $this->request($name, @$arguments[0]);
    }

    /**
     * Get input data from webhook
     */
    public function GetData(bool $decode = true)
    {
        $this->content = file_get_contents('php://input') or die('No body');
        $this->update  = ($decode) ? json_decode($this->content) : $this->content;

        return $this->update;
    }

    /**
     * Receive incoming updates using long polling 
     * @see https://core.telegram.org/bots/api#getupdates
     */
    public function GetUpdates(bool $decode = true, int $limit = 1)
    {
        $updates = $this->request('getUpdates', ['limit' => $limit]);
        if ($updates->ok) {
            $this->content = json_encode($updates->result[0]);
            $this->update = ($decode) ? json_decode($this->content) : $this->content;
        }
        return $this->update;
    }
}