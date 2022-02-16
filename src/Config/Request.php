<?php 

namespace App\Config;

class Request {
    
    public static $ch;
    public static $url;

    private static $default = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ];

    /**
     * Init a new curl session and delete last curl session
     */
    public static function Init(string $url):void
    {
        if (self::$ch) self::$ch = null;
        self::$url = $url;
        self::$ch = curl_init(self::$url);
        self::AddOpt(self::$default);
    }

    /**
     * Add options to curl session
     */
    public static function AddOpt(array $opt):void
    {
        curl_setopt_array(self::$ch, $opt);
    }
    
    public static function Run()
    {
        $response = curl_exec(self::$ch);
        $info = curl_getinfo(self::$ch);
        if ($response === false) {
            error_log('[req] Fail to send request to url: ' . self::$url);
            error_log('[req] Error (' . curl_errno(self::$ch).'): ' . curl_error(self::$ch));
        }
        curl_close(self::$ch);
        return [
            'ok' => $response !== false,
            'info' => $info,
            'code' => $info['http_code'],
            'response' => $response,
        ];
    }
    /**
     * Create and exucute a curl session
     */
    private static function Create(string $url, string $method = 'GET', ?array $headers=null, $post=null): array
    {
        self::Init($url);
        self::AddOpt([CURLOPT_CUSTOMREQUEST => $method]);

        if ($headers) self::AddOpt([CURLOPT_HTTPHEADER => $headers]);
        if ($post) self::AddOpt([CURLOPT_POSTFIELDS => $post]);
        // Exec curl
        return self::Run();
    }

    public static function __callStatic($method, $settings)
    {
        return self::Create(@$settings[0], strtoupper($method), @$settings[1], @$settings[2]);
    }

    /**
     * Download file and save to local 
     * @param string $file_name Path to save file
     */
    public static function Download(string $url, ?string $file_name = null)
    {
        $file_name = $file_name ?? basename($url) ?? uniqid() . 'file.tmp';
        $fp = fopen($file_name, 'wb');

        self::Init($url);
        self::AddOpt([CURLOPT_FILE => $fp, CURLOPT_HEADER => 0]);

        $response = curl_exec(self::$ch);
        curl_close(self::$ch);
        fclose($fp);
        return $response;
    }
}