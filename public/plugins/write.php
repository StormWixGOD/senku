<?php 

// Create a images with text
// Path: public/plugins/write.php

$txt = $cmd::GetContent(7);
if (empty($txt)) $txt = 'Put your text here';

$url = 'https://apis.xditya.me/write?text=' . urlencode($txt); // Api by: https://t.me/BotzHub

$res = $bot->sendPhoto([
    'chat_id' => $cmd::ChatId(),
    'photo' => $url,
    'caption' => 'Write: <code>' . substr(urldecode($txt), 0, 1000) . '</code>',
    'reply_to_message_id' => $cmd::MsgId(),
    'parse_mode' => 'HTML'
]);

if (!$res->ok) {
    $bot->sendMessage('Error: ' . $res->description);
}