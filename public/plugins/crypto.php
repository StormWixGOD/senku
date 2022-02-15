<?php 

use App\Config\Exchange;

$type = $cmd::GetContent(8);

if (empty($type)) {
    $bot->SendMsg("<b>Price of the main <i>cryptocurrencies</i>\nFormat:</b> <code>/crypto name</code>");
    exit;
}

$crypto = Exchange::Crypto($type);

if (!$crypto['ok']) {
    $bot->SendMsg("<i>" . $crypto['msg'] . " (".$type.")</i>");
} else {
    $bot->SendMsg("<b>" . $crypto['name'] . "</b>\n<i>Price:</i> <code>$" . $crypto['price'] . "</code>\n<i>Change:</i> <code>" . $crypto['change'] . "</code>");
}