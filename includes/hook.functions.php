<?php

// examples/comments https://gist.github.com/Mo45/cb0813cb8a6ebcd6524f6a36d4f8862c
// Thank you Mo45!

function getRateEmbed($IPAddress, $count) {
    $timestamp = date("c", strtotime("now"));
    return $json_data = json_encode([
        "tts" => false,
        "embeds" => [
            [
                "title" => "Rate limit exceeded!",
                "description" => "User exceeded the current redis limiter",
                "timestamp" => $timestamp,
                "color" => hexdec( "CC0000" ),
                "footer" => [
                    "text" => "osu!ranks",
                ],
                "fields" => [
                    [
                        "name" => "IP Address",
                        "value" => $IPAddress,
                        "inline" => true
                    ],
                    [
                        "name" => "Redis count",
                        "value" => $count,
                        "inline" => true
                    ]
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

function getUserEmbed($osujson) {
    require_once('countries.inc.php');
    $country = getCountryName($osujson->country);
    $timestamp = date("c", strtotime("now"));
    return $json_data = json_encode([
        "tts" => false,
        "embeds" => [
            [
                "title" => "New user request",
                "description" => "**Username**: [$osujson->username](https://osu.ppy.sh/u/$osujson->user_id)\n**User ID**: $osujson->user_id\n**Country**: $country\n**Joined**: $osujson->join_date",
                "timestamp" => $timestamp,
                "color" => hexdec( "53EA3E" ),
                "footer" => [
                    "text" => "osu!ranks",
                ],
                "thumbnail" => [
                "url" => 'http://s.ppy.sh/a/' . $osujson->user_id
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

function sendUserHook($osujson) {
    require_once('api/hook.inc.php');
    $hookurl = getUserHook();
    $json_data = getUserEmbed($osujson);
    $ch = curl_init( $hookurl );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec( $ch );
    // echo $response;
    curl_close( $ch );
}

function sendRateHook($IPAddress, $count) {
    require_once('api/hook.inc.php');
    $hookurl = getRateHook();
    $json_data = getRateEmbed($IPAddress, $count);
    $ch = curl_init( $hookurl );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec( $ch );
    // echo $response;
    curl_close( $ch );
}