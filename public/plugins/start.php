<?php 

$txt = '<b>Avaliable commands</b>' . PHP_EOL;

foreach ($cmd::Var('cmds') as $item) {
    $txt .= '<code>/' . $item . '</code>' . PHP_EOL;
}
$bot->SendMsg($txt);
