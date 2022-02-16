<?php # Only admin can use this command
# Download and add commands

use App\Config\Request;
use App\Config\Utils;


if ($server->env['ADMIN'] != $cmd::UserId()) {
    $bot->SendMsg('You are not the owner of this bot.');
    exit;
}

$name = $cmd::GetContent(9);

if (!isset($up->message->reply_to_message->document) || empty($name)) {
    $bot->SendMsg('Please <b>reply to a file</b> and <b>enter a new command</b> name.');

} elseif (in_array($name, $cmd::Var('cmds'))) {
    $bot->SendMsg('Command <b>already exists</b>, use another name.');

} elseif ($up->message->reply_to_message->document->mime_type != 'application/x-php') {
    $bot->SendMsg('<i>Only accept files with extension <b>.php</b></i>');

} else {
    // Download file
    $file = $bot->getFile(['file_id' => $up->message->reply_to_message->document->file_id]);

    $size = $file->result->file_size;
    $file_url = 'https://api.telegram.org/file/bot' . $bot->token . '/' . $file->result->file_path;
    $file_save = APP_PATH . '/public/plugins/' . $name . '.php';

    $ida = $bot->SendMsg('Downloading...')->result->message_id;
    Request::Download($file_url, $file_save);

    $bot->EditMsg('Checking sintax file...', $ida);
    $parse = shell_exec('php -l ' . $file_save);

    if (strpos(' '.$parse, 'Parse error: syntax error')) {
        $bot->EditMsg('<i>'.Utils::QuitHtml($parse).'</i>', $ida);
        unlink($file_save); // Delete file
        exit;
    }
    
    $bot->EditMsg('<i>No syntax errors detected, <b>save command</b> (/'.$name.')</i>', $ida);
}
