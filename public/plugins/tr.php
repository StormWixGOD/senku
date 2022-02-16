<?php

use App\Config\Translate;
use App\Config\Utils;

$txt = $cmd::GetContent(4);
$message = $up->message;

if (empty($txt) && !isset($message->reply_to_message->text)) {
    $bot->SendMsg("<b>λ <i>Translate Messages</i>\nFormat:</b> <code>/tr lang_code Text</code>");
    exit;
}

$lang_co = Utils::MultiExplode([' ', PHP_EOL], $txt)[0];
$lang_code = explode('|', $lang_co);

$lang_input = $lang_code[0] ?? 'auto';
$lang_output = $lang_code[1] ?? 'es';

if (!isset($lang_code[1])) {
    $lang_input = 'auto';
    $lang_output = $lang_code[0];
}

$lang_output = (empty($lang_output)) ? 'es' : $lang_output;
$textTr = $message->reply_to_message->caption ?? $message->reply_to_message->text ?? trim(substr($txt, strlen($lang_co))); // Reply message o message simple

$tr = Translate::tr($textTr, $lang_input, $lang_output);

if ($tr->error) {
    $bot->SendMsg('<b>⚠️ <i>' . $tr->msg . '</i></b>');

} else {
    $replyId = $message->reply_to_message->message_id ?? $cmd::MsgId();
    $txt = '<b>Translate (<i>' . $tr->input->lang . ' to ' . $tr->output->lang . "</i>)</b>\n\n" . $tr->output->text;
    $bot->SendMsg($txt, null, $replyId);
}