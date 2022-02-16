<?php # Only admin can use this command
# Delete cmds

if ($server->env['ADMIN'] != $cmd::UserId()) {
    $bot->SendMsg('You are not the owner of this bot.');
    exit;
}

$name = $cmd::GetContent(9);

if (!in_array($name, $cmd::Var('cmds'))) {
    $bot->SendMsg('Command <b>not exists</b>, try another name.');
    exit;
}

$locate = $cmd::Var('location')[$name];

if (unlink($locate)) {
    $bot->SendMsg('Command <b>deleted</b>.');
} else {
    $bot->SendMsg('<i>Error deleting command</i>.');
}