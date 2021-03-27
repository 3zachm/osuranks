<?php

require_once(__DIR__ . '/../../../../secure/.env');
$osu_path = 'http://osu.ppy.sh/api/';

// 401 Check
if (false === ($json = @file_get_contents('http://osu.ppy.sh/api/get_user?k='.$_OSUAPI))) {
    $data = json_decode('{"error":"Please provide a valid API key."}');
    echo $data->error;
    exit();
}
else {
    $data = json_decode($json);
}

function getAPI() {
    global $_OSUAPI;
    return $_OSUAPI;
}

function getPATH() {
    global $osu_path;
    return $osu_path;
}

//echo '<pre>'; print_r($data); echo '</pre>';
//echo ($data[0]->user_id);