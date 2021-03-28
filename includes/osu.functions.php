<?php


function getUser($profile, $mode) {
    require_once('api/osu.inc.php');
    require_once('api/redis.inc.php');
    require_once('api/rate.inc.php');
    $redis = getRedis();
    $osu_path = getPATH();
    $_OSUAPI = getAPI();
    $apiLimiter = new RateLimiter($redis, 30, 30);

    if ($apiLimiter->hit() === true) {
        $request = $osu_path . 'get_user?k=' . $_OSUAPI . '&u=' . $profile . '&m=' . $mode;
        $json = @file_get_contents($request);
        $result = json_decode($json);
        if (isset($result[0]->user_id)) {
            return $result;
        }
        else {
            return "notfound";
        }
    }
    else {
        header("location: /osuranks?error=ratelimited");
        exit();
        return "ratelimited";
    }
}