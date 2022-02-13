<?php 

namespace App\Config;

class Request {
    
    public static $ch;

    private static function Create(string $url, string $method = 'GET', ?array $headers=null, $post=null)
    {
        if (self::$ch) self::$ch = null;
        // Init curl
        self::$ch = curl_init($url);

        curl_setopt_array(self::$ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        // Add headers and postfield data
        if ($headers != null) curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $headers);
        if ($post != null) curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $post);
        // Exec curl
        $response = curl_exec(self::$ch);
        $info = curl_getinfo(self::$ch);
        // Logger
        if ($response === false) {
            error_log('[req] Fail to send request ' . $method . ' to: ' . $url . "\n\tError(" . curl_errno(self::$ch) . "): " . curl_error(self::$ch));
        }
        
        curl_close(self::$ch);
        self::$ch = null;
        // Return response
        return [
            'ok' => $response !== false,
            'info' => $info,
            'code' => $info['http_code'],
            'response' => $response,
        ];
    }

    public static function __callStatic($method, $settings)
    {
        return self::Create(@$settings[0], strtoupper($method), @$settings[1], @$settings[2]);
    }
}