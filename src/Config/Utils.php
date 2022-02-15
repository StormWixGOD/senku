<?php 

namespace App\Config;


class Utils {
    
    /**
     * Delete array key if is empty or null
     */
    public static function DeleteKeyEmpty(array &$arr)
    {
        if (!is_array($arr) && !is_object($arr)) return;

        foreach ($arr as $key => $item) {
            if (empty($arr[$key]) || $arr[$key] == null || $arr[$key] == '""' || $arr[$key] == "''") {
                unset($arr[$key]);
            }
        }
    }

    /**
     * MultiExplode
     */
    public static function MultiExplode(array $explodes, string $str):array
    {
        $str = str_replace($explodes, $explodes[0], $str);
        return explode($explodes[0], $str);
    }
}
