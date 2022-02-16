<?php 

namespace App\Models;

use App\Config\Utils;


class Command {

    private static $cmds = [];
    private static $location = [];
    private static $up;
    public static string $msg;
    public static string $chat_id;
    public static string $msg_id;

    /**
     * Set update data
     */
    public function __construct(?object $up)
    {
        self::$up = $up;
    }

    /**
     * Cargar los comandos según el nombre del archivo
     */
    public static function LoadLocal(string $dir, string $ext = 'php')
    {
        $files = glob($dir . '*.' . $ext);
        foreach ($files as $i) {
            $item = Utils::MultiExplode(['/', '\\'],$i);
            $cmd = trim(str_replace('.' . $ext, '', end($item)));
            self::$cmds[] = $cmd;
            self::$location[$cmd] = $i;
        }
        return self::$cmds;
    }

    /**
     * Cargar los comandos desde una db
     */
    public static function LoadExternal($sql, string $query = "SELECT * FROM cmds")
    {
        $res = $sql->GetAllRows($query);
        if (!$res['ok']) return self::$cmds;

        foreach ($res['rows'] as $cmd) {
            // Delete inactive comands
            if (!$cmd['status']) {
                unset($res[$cmd]);
            } else {
                self::$cmds[] = $cmd['cmd'];
                self::$location[$cmd['cmd']] = $cmd['route'];
            }
        }
        return self::$cmds;
    }

    /**
     * Get var
     *
     * @param string $var_name Variable name
     */
    public static function Var(string $var_name)
    {
        return self::$$var_name ?? null;
    }

    /**
     * Get command from message, return null if not exists
     */
    public static function ExtractCmd(string $txt)
    {
        $_ENV['BOT_CMD'] = explode(' ', $_ENV['BOT_CMD']);
        $txt = Utils::MultiExplode(
            [' ', '@', PHP_EOL],
            trim(strtolower(str_replace(['@', $_ENV['BOT_USERNAME']], '', $txt)))
        )[0];

        if (in_array($txt[0], $_ENV['BOT_CMD'])) {
            return substr($txt, 1);
        } return null;
    }

    public static function IsCmd(string $msg, string $cmd)
    {
        $mg = self::ExtractCmd($msg);
        return $mg === $cmd;
    }

    /**
     * Webhook update
     */
    private static function ExistMsg()
    {
        self::$msg = self::$up->message->text 
                    ?? self::$up->message->caption
                    ?? '';
        return (self::$msg != null);
    }

    /**
     * Require plugin file
     * @param App\Controllers\Start $bot Bot resource
     */
    public static function IncludeFile()
    {
        if (!self::ExistMsg()) return false;

        self::$chat_id = self::$up->message->chat->id;
        self::$msg_id  = self::$up->message->message_id;

        $cmd = self::ExtractCmd(self::$msg);

        if (isset(self::$location[$cmd])) {
            return self::$location[$cmd];
        } return null;
    }

    /**
     * Get chat id from update
     */
    public static function ChatId():?string
    {
        return self::Var('chat_id');
    }

    /**
     * Get message id from update
     */
    public static function MsgId():?string
    {
        return self::Var('msg_id');
    }

    /**
     * Get user id from update
     */
    public static function UserId():?string
    {
        return self::$up->message->from->id
              ?? self::$up->callback_query->from->id
              ?? self::$up->message->chat->id
              ?? self::$up->inline_query->from->id
              ?? null;
    }

    /**
     * Get command input
     */
    public static function GetContent(int $length = 0, ?string $msg = null)
    {
        $msg = $msg ?? self::$msg;
        return trim(substr($msg, $length));
    }
}