<?php

require_once(__DIR__ . '/../../../../secure/.env');
require_once('redis.inc.php');
require_once('rate.inc.php');

$osu_path = 'http://osu.ppy.sh/api/';
$limiter401 = new GlobalRateLimiter($redis, '401', 1, 10);

if ($limiter401->hit() === true) {
    // 401 Check
    if (false === ($json = @file_get_contents('http://osu.ppy.sh/api/get_user?k='.$_OSUAPI))) {
        $data = json_decode('{"error":"Please provide a valid API key."}');
        echo $data->error;
        exit();
    }
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