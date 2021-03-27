<?php


function getUser($profile, $mode) {
    require_once('api/osu.inc.php');
    $osu_path = getPATH();
    $_OSUAPI = getAPI();
    $request = $osu_path . 'get_user?k=' . $_OSUAPI . '&u=' . $profile . '&m=' . $mode;
    $json = @file_get_contents($request);
    $result = json_decode($json);
    if (isset($result[0]->user_id)) {
        return $result;
    }
    else {
        return "error";
    }
}