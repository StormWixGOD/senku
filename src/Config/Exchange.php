<?php

namespace App\Config;

use App\Config\Request;

/**
 * EXCHANGE CRYPTO
 * Exchange any cryptocurrency
 * 
 * @author Mateodioev <https://github.com/mateodioev>
 * @copyright 2022 Mateo
 * @license Apache License, Version 2.0
 */
class Exchange
{

    /**
     * Api para obtener el precio de las criptomonedas
     */
    private const API_CRYPTO = 'https://production.api.coindesk.com/v2/tb/price/ticker?assets=';

    /**
     * Api para comvertir un valor en USD a btc
     */
    private const API_EXCHANGE = 'https://blockchain.info/tobtc?currency=USD&value=';

    /**
     * Calcular las comisiones de una transacción en paypal
     */
    final public static function Paypal(string | int | float $input, float $percent = 1.054, float $extra = 0.35): float
    {
        return $input * $percent + $extra;
    }

    /**
     * Obtener el precio de una criptomoneda
     */
    final public static function Crypto(string $crypto): array
    {
        $crypto = strtoupper($crypto);
        $data = json_decode(Request::Get(self::API_CRYPTO . $crypto)['response'], true);

        if ($data['statusCode'] != 200) {
            # Invalid crpyto
            return [
                'ok' => false,
                'msg' => 'Invalid crypto currency',
            ];
        } else {
            $data = $data['data'][$crypto];
            return [
                'ok' => true,
                'name' => $data['name'],
                'price' => round($data['ohlc']['c'], 3),
                'change' => round($data['change']['percent'], 3),
            ];
        }

    }

    /**
     * Cambiar un valor dado en USD a BTC
     */
    final public static function Change(float | int $amount): string
    {
        return Request::Get(self::API_EXCHANGE . $amount)['response'];
    }
}
