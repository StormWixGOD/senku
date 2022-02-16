<?php 
use App\Config\Request;

// Ip search
const API_URL = 'https://api.ipdata.co/';
const API_INFO_URL = 'https://ipinfo.io/';

$ip = $cmd::GetContent(4);

if (empty($ip)) {
    $bot->SendMsg("<b>Ip search\nFormat:</b> <code>/ip ".rand(1, 255).'.'.rand(1, 255).'.'.rand(1, 255).'.'.rand(1,255).'</code>');

} elseif (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
    $bot->SendMsg("<b>❌ Please provide a valid IP address</b>");

} else {
    // Get ip info
    $url = API_URL . urlencode($ip) . '?api-key=' . $_ENV['IP_API_KEY'];
    $datas = json_decode(Request::Get($url)['response'], true);

    if (isset($datas['message'])) {
        $bot->SendMsg('<b>❌ ' . str_replace($ip, '<code>'.$ip.'</code>', $datas['message']) . '</b>');
        exit;
    }

    $url = API_INFO_URL . urlencode($ip) . '?token=' . $_ENV['IP_API_INFO_KEY'];
    $fdata = json_decode(Request::Get($url)['response'], true);

    $d = $datas;
    $th = $d['threat'];

    $txt = "<b>✅ Valid ip ➜ <i>%s</i> %s\nCountry:</b> <i>%s / %s</i>\n<b>Org:</b> <i>%s</i>\n<b>Type:</b> <i>%s</i>\n<b>Time/Zone:</b> <i>%s</i>\n<b>Zip-code</b> <i>%s</i>\n<b>Threat:\n - Known attacker:</b> <i>%s</i>\n <b>- Known abuser:</b> <i>%s</i>\n <b>- Anonymous:</b> <i>%s</i>\n <b>- Is threat:</b> <i>%s</i>\n <b>- Bogon:</b> <i>%s</i>\n <b>- Proxy:</b> <i>%s</i>\n <b>- Tor:</b> <i>%s</i>";
    $txt = sprintf($txt, $ip, $d['emoji_flag'], $d['country_name'], $d['continent_name'], @$d['asn']['name'], ucfirst(@$d['asn']['type']), @$fdata['timezone'], @$fdata['postal'], BoolString($th['is_known_attacker']), BoolString($th['is_known_abuser']), BoolString($th['is_anonymous']), BoolString($th['is_threat']), BoolString($th['is_bogon']), BoolString($th['is_proxy']), BoolString($th['is_tor']));
    $loc = explode(',', $fdata['loc']);

    $bot->SendMsg($txt);
    $bot->sendVenue([
        'chat_id' => $cmd::ChatId(),
        'latitude' => $loc[0],
		'longitude' => $loc[1],
        'title' => 'IP location ➜ '.$ip,
        'address' => $fdata['city'].' - '.$fdata['region'].' - '.$datas['country_name'].' - '.$datas['continent_code']
    ]);
}

function BoolString(bool $bool, array $replace = [false => 'False', true => '❌ True']) {
    return $replace[$bool];
}