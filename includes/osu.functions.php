<?php


function getUser($profile, $mode) {
    require_once('api/osu.inc.php');
    require_once('api/redis.inc.php');
    require_once('api/rate.inc.php');
    $redis = getRedis();
    $osu_path = getPATH();
    $_OSUAPI = getAPI();
    $apiLimiter = new RateLimiter($redis, 30, 30);
    $redis_key = $profile . '_' . $mode;
    $period = 900;

    # check cache
    if ($redis->exists($redis_key)) {
        $json = $redis->get($redis_key);
        return json_decode($json);
    }
    else if ($apiLimiter->hit(true) === true) {
        $request = $osu_path . 'get_user?k=' . $_OSUAPI . '&u=' . $profile . '&m=' . $mode;
        $json = @file_get_contents($request);
        $result = json_decode($json);
        if (isset($result[0]->user_id)) {
            $redis->set($redis_key, $json);
            $redis->expire($redis_key, $period);
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

function getUserPFP($profile) {
    $pfp = 'http://s.ppy.sh/a/' . $profile;
    return $pfp;
}