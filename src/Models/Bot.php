<?php 

namespace App\Models;

use App\Config\{Request, Utils};
use App\Models\Command as Cmd;

class Bot {
    const API_URL = 'https://api.telegram.org/bot';
    public string $token = '';
    public string $endpoint = '';

    private $res;
    private $result;
    private $opt = []; // Optional config payload

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
        $payload = array_merge($arguments[0] ?? [], $this->opt);
        Utils::DeleteKeyEmpty($payload);
        return $this->request($name, $payload);
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
            $this->content = json_encode(@$updates->result[0]);
            $this->update = ($decode) ? json_decode($this->content) : $this->content;
        }
        return $this->update;
    }

    /**
     * Add optional config payload to request
     */
    public function AddOpt(array $opt)
    {
        $this->opt = $opt;
    }

    /**
     * Send chat action (typing, upload photo, record video, upload video, record audio, upload audio, upload document, find location)
     * @link https://core.telegram.org/bots/api#sendchataction
     */
    public function SendAction(string $action, ?string $chat_id)
    {
        return $this->request('sendChatAction', [
            'chat_id' => $chat_id ?? Cmd::ChatId(),
            'action' => $action
        ]);
    }

    /**
     * Send message to user
     * @link https://core.telegram.org/bots/api#sendmessage
     */
    public function SendMsg(string $txt, ?string $chat_id=null, ?string $msg_id=null, $button = '', $parse_mode = 'HTML', $web_page_preview = false)
    {
        $payload = array_merge([
            'chat_id' => $chat_id ?? Cmd::ChatId(),
            'reply_to_message_id' => $msg_id ?? Cmd::MsgId(),
            'text' => $txt,
            'parse_mode' => $parse_mode,
            'reply_markup' => json_encode($button),
            'disable_web_page_preview' => $web_page_preview,
        ], $this->opt);

        $this->SendAction('typing', $payload['chat_id']);
        Utils::DeleteKeyEmpty($payload);

        return $this->request('sendMessage', $payload);
    }

    /**
     * Edit a message send from bot
     * @link https://core.telegram.org/bots/api#editmessagetext
     */
    public function EditMsg(string $txt, string $msg_id, ?string $chat_id=null, $button = '', $parse_mode = 'HTML')
    {
        $payload = array_merge([
            'chat_id' => $chat_id ?? Cmd::ChatId(),
            'message_id' => $msg_id,
            'text' => $txt,
            'parse_mode' => $parse_mode,
            'reply_markup' => json_encode($button),
        ], $this->opt);

        Utils::DeleteKeyEmpty($payload);

        return $this->request('editMessageText', $payload);
    }

    /**
     * Delete a message, including service messages
     * @link https://core.telegram.org/bots/api#deletemessage
     */
    public function DelMsg(string $chat_id, string $msg_id)
    {
        return $this->request('deleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $msg_id
        ]);
    }
}