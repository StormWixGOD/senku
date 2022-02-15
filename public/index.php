<?php
require __DIR__ . '/../vendor/autoload.php';

define('APP_PATH', dirname(__DIR__));

use App\Controllers\Start;
use App\Models\Command as Cmd;

$senku = new Start(APP_PATH, false, true, false);
$db = $senku->db;
$bot = $senku->bot;
$up = $senku->up;
$server = $senku->server;

$cmd = new Cmd($up);
$cmd::LoadLocal(APP_PATH . '/public/plugins/');
$command = Cmd::IncludeFile();
$command ? require $command : die;


/*
| Load the commands (according to the file name) locally
| Cmd:: LoadLocal(APP_PATH . '/public/plugins/');
|
| Load commands based on a database
| Cmd:: LoadExternal($senku->db);  // Load commands from database

| Returns the file path of the command according to the text, returns false or null if no message is found
| $cmd = Cmd::IncludeFile($senku);
|
| Include the file of the command
| $cmd ? require $cmd: null;
*/