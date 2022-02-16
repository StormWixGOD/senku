<?php # Only admin can use this command

if ($server->env['ADMIN'] != $cmd::UserId()) {
    $bot->SendMsg('You are not the owner of this bot.');
    exit;
}

if (!file_exists($senku->path_log) || is_dir($senku->path_log)) {
    $bot->SendMsg('Not found log file.');
    exit;
}

$del = (bool) $cmd::GetContent(9);

# Send log file
$bot->AddOpt(['disable_notification' => true, 'protect_content' => true, 'allow_sending_without_reply' => true]);
$bot->Document($senku->path_log, '<i>Request time:</i> ' . date('D, j M Y \a\t h A'));

if ($del){
    # Delete log file
    unlink($senku->path_log);
}