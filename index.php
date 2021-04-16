<?php
    $notfound = false;
    $ratelimited = false;
    $unknownerror = false;
    require_once("includes/osu.lib.php");
    $osuSTD = $osuTAIKO = $osuCTB = $osuMANIA = json_decode('
    {
        "user_id": "0",
        "username": "Username",
        "join_date": "0000-00-00",
        "count300": "0",
        "count100": "0",
        "count50": "0",
        "playcount": "0",
        "ranked_score": "0",
        "total_score": "0",
        "pp_rank": "0",
        "level": "0.000",
        "pp_raw": "0",
        "accuracy": "0",
        "country": "Unknown",
        "total_seconds_played": "0",
        "pp_country_rank": "0"
    }
    '); // make default json values
    $pfp = 'img/avatar-guest@2x.png';

    if (isset($_GET["error"])) {
        if ($_GET["error"] == "notfound") {
            $notfound = true;
        }
        else if ($_GET["error"] == "ratelimited") {
            $ratelimited = true;
        }
        else if ($_GET["error"] == "unknown") {
            $unknownerror = true;
        }
    }
    else {
        if (isset($_GET["user"])) {
            $osuCHECK = getUser($_GET["user"], 0);
            $osuSTD = $osuCHECK[0];
            if (isset($osuSTD->user_id)) {
                $osuTAIKO = getUser($_GET["user"], 1)[0];
                $osuCTB = getUser($_GET["user"], 2)[0];
                $osuMANIA = getUser($_GET["user"], 3)[0];
                $pfp = getUserPFP($osuSTD->user_id);
            }
            else if ($osuCHECK === "ratelimited") {
                header("location: /osuranks?error=ratelimited");
                exit();
            }
            else if ($osuCHECK === "notfound") {
                header("location: /osuranks?error=notfound");
                exit();
            }
            else {
                header("location: /osuranks?error=unknown");
                exit();
            }
        }
    }
?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" type="text/css" href="css/osu.css?v1.0">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Manrope">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
            if ($osuSTD->user_id === "0") {
                echo("osu!ranks");
            }
            else {
                echo("$osuSTD->username | osu!ranks");
            }
        ?>
    </title>

    <!-- Titles -->
    <?php
        if ($osuSTD->user_id === "0") {
            $META_TITLE = "osu!ranks";
            #$META_IMG = "https://3zachm.dev/img/ranks.png";
            $META_DESC = "View all and combined stats for an osu!profile.";
        }
        else {
            $META_TITLE = "$osuSTD->username | osu!ranks";
            #$META_IMG = "https://3zachm.dev/img/ranks.png";
            $META_DESC = "osu!ranks » $osuSTD->username";
        }
        echo('
            <meta name="title" content="'.$META_TITLE.'">
            <meta property="og:title" content="'.$META_TITLE.'">
            <meta property="twitter:image" content="'.$META_IMG.'">
            <!-- <meta property="og:image" content="'.$META_IMG.'"> -->
            <meta name="description" content="'.$META_DESC.'">
            <meta property="og:description" content="'.$META_DESC.'">
            <meta property="twitter:description" content="'.$META_DESC.'">
        ');
    ?>

    <!-- OpenGraph/Discord -->
    <meta property="og:url" content="https://3zachm.dev/osuranks">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#b875d7" data-react-helmet="true" >

    <!-- Twitter -->
    <meta property="twitter:url" content="https://3zachm.dev/osuranks">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="osu!ranks">
</head>
<body class="body">
    <div class="header">
        <h1 class="title">osu!ranks</h1>
        <?php
            if ($notfound) { echo("<p class='subtitle__error'>Username not found!"); }
            else if ($ratelimited) { echo("<p class='subtitle__error'>You're being rate limited! (~7 requests/30 seconds)"); }
            else if ($unknownerror) { echo("<p class='subtitle__error'>An unknown error occurred!"); }
            else { echo("<p class='subtitle'>It sort of works"); }
        ?>
        </p>
    </div>
    <div class="search">
        <form action="includes/osustats.inc.php" method="post">
            <input type="text" name="uid" placeholder="<?php echo($osuSTD->username) ?>">
            <button type="submit" name="submit">pp</button>
        </form>
    </div>
    <div class="overlay">
        <div class="container">
            <div class="card">
                <div class="thing" id="user">
                    <img src=<?php echo($pfp) ?> class="pfp" id="userpfp">
                    <p class="username" id="u1">
                    <?php echo('<a href="https://osu.ppy.sh/u/' . $osuSTD->user_id . '" target="_self">'  . $osuSTD->username . '</a>') ?>
                    </p>
                    <div class="infoline">
                        <p class="infotext">Joined:</p>
                        <p class="info" id="join">
                            <?php echo(substr($osuSTD->join_date, 0, 10)) ?>
                        </p>
                    </div>
                    <div class ="infoline">
                        <p class="infotext">Country:</p>
                        <p class="info" id="country">
                            <?php echo(getCountryName($osuSTD->country)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Average Acc:</p>
                        <p class="info" id="avgAcc">
                            <?php
                                $totalAcc = round(($osuSTD->accuracy + $osuTAIKO->accuracy + $osuCTB->accuracy + $osuMANIA->accuracy)/4, 2);
                                echo($totalAcc . "%");
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playcount:</p>
                        <p class="info" id="totalplayC">
                            <?php
                                $totalPC = $osuSTD->playcount + $osuTAIKO->playcount + $osuCTB->playcount + $osuMANIA->playcount;
                                echo(number_format($totalPC));
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Ranked Score:</p>
                        <p class="info" id="totalrankedS">
                            <?php
                                $totalRScore = $osuSTD->ranked_score + $osuTAIKO->ranked_score + $osuCTB->ranked_score + $osuMANIA->ranked_score;
                                echo(number_format($totalRScore));
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Score:</p>
                        <p class="info" id="totaltotalS">
                            <?php
                                $totalScore = $osuSTD->total_score + $osuTAIKO->total_score + $osuCTB->total_score + $osuMANIA->total_score;
                                echo(number_format($totalScore));
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Hits:</p>
                        <p class="info" id="totalHits">
                            <?php
                                $totalHits =    $osuSTD->count50 + $osuSTD->count100 + $osuSTD->count300 +
                                                $osuTAIKO->count50 + $osuTAIKO->count100 + $osuTAIKO->count300 +
                                                $osuCTB->count50 + $osuCTB->count100 + $osuCTB->count300 +
                                                $osuMANIA->count50 + $osuMANIA->count100 + $osuMANIA->count300;
                                echo(number_format($totalHits));
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playtime:</p>
                        <p class="info" id="totalplayT">
                            <?php
                                $totalPTime = $osuSTD->total_seconds_played + $osuTAIKO->total_seconds_played + $osuCTB->total_seconds_played + $osuMANIA->total_seconds_played;
                                $parsedTime = round(($totalPTime)/3600, 0);
                                echo(number_format($parsedTime) . " hours");
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="thing" id="std">
                    <img class="ctrimg" src="img/std-icon.png">
                    <p class="rank" id="r0">
                        <?php echo("#" . number_format($osuSTD->pp_rank)) ?>
                    </p>
                    <p class="crank" id="cr0">
                        <?php
                            echo('<img class="flag" src="flags/' . $osuSTD->country . '.png"> ');
                            echo("#" . number_format($osuSTD->pp_country_rank));
                        ?>
                    </p>
                    <div class="infoline">
                        <p class="infotext">lvl:</p>
                        <p class="info" id="lv0">
                            <?php
                                if (is_null($osuSTD->level)) {
                                    $stdLVLint = 0;
                                    $stdLVLdec = 000;
                                }
                                else {
                                    list($stdLVLint, $stdLVLdec) = explode('.', $osuSTD->level);
                                }
                                echo('<progress id="lvl" max="100" value="'. substr(round($stdLVLdec, -1), 0, 2) .'"></progress> ' );
                                printf("%.2f", $osuSTD->level)
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">pp:</p>
                        <p class="info" id="p0">
                            <?php echo(number_format($osuSTD->pp_raw, 1) . "pp") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Accuracy:</p>
                        <p class="info" id="a0">
                            <?php echo(number_format($osuSTD->accuracy, 2) . "%") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playcount:</p>
                        <p class="info" id="pc0">
                            <?php echo(number_format($osuSTD->playcount)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Ranked Score:</p>
                        <p class="info" id="rs0">
                            <?php echo(number_format($osuSTD->ranked_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Score:</p>
                        <p class="info" id="ts0">
                            <?php echo(number_format($osuSTD->total_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Hits:</p>
                        <p class="info" id="th0">
                            <?php echo(number_format($osuSTD->count50 + $osuSTD->count100 + $osuSTD->count300)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playtime:</p>
                        <p class="info" id="pt0">
                            <?php echo(number_format(round(($osuSTD->total_seconds_played)/3600, 0)) . " hours") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="thing" id="taiko">
                    <img class="ctrimg" src="img/taiko-icon.png">
                    <p class="rank" id="r1">
                        <?php echo("#" . number_format($osuTAIKO->pp_rank)) ?>
                    </p>
                    <p class="crank" id="cr1">
                        <?php
                            echo('<img class="flag" src="flags/' . $osuTAIKO->country . '.png"> ');
                            echo("#" . number_format($osuTAIKO->pp_country_rank));
                        ?>
                    </p>
                    <div class="infoline">
                        <p class="infotext">lvl:</p>
                        <p class="info" id="lv1">
                            <?php
                                if (is_null($osuTAIKO->level)) {
                                    $taikoLVLint = 0;
                                    $taikoLVLdec = 000;
                                }
                                else {
                                    list($taikoLVLint, $taikoLVLdec) = explode('.', $osuTAIKO->level);
                                }
                                echo('<progress id="lvl" max="100" value="'. substr(round($taikoLVLdec, -1), 0, 2) .'"></progress> ' );
                                printf("%.2f", $osuTAIKO->level)
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">pp:</p>
                        <p class="info" id="p1">
                            <?php echo(number_format($osuTAIKO->pp_raw, 1) . "pp") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Accuracy:</p>
                        <p class="info" id="a1">
                            <?php echo(number_format($osuTAIKO->accuracy, 2) . "%") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playcount:</p>
                        <p class="info" id="pc1">
                            <?php echo(number_format($osuTAIKO->playcount)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Ranked Score:</p>
                        <p class="info" id="rs1">
                            <?php echo(number_format($osuTAIKO->ranked_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Score:</p>
                        <p class="info" id="ts1">
                            <?php echo(number_format($osuTAIKO->total_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Hits:</p>
                        <p class="info" id="th1">
                            <?php echo(number_format($osuTAIKO->count50 + $osuTAIKO->count100 + $osuTAIKO->count300)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playtime:</p>
                        <p class="info" id="pt1">
                            <?php echo(number_format(round(($osuTAIKO->total_seconds_played)/3600, 0)) . " hours") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="thing" id="ctb">
                    <img class="ctrimg" src="img/ctb-icon.png">
                    <p class="rank" id="r2">
                        <?php echo("#" . number_format($osuCTB->pp_rank)) ?>
                    </p>
                    <p class="crank" id="cr2">
                        <?php
                            echo('<img class="flag" src="flags/' . $osuCTB->country . '.png"> ');
                            echo("#" . number_format($osuCTB->pp_country_rank));
                        ?>
                    </p>
                    <div class="infoline">
                        <p class="infotext">lvl:</p>
                        <p class="info" id="lv2">
                            <?php
                                if (is_null($osuCTB->level)) {
                                    $ctbLVLint = 0;
                                    $ctbLVLdec = 000;
                                }
                                else {
                                    list($ctbLVLint, $ctbLVLdec) = explode('.', $osuCTB->level);
                                }
                                echo('<progress id="lvl" max="100" value="'. substr(round($ctbLVLdec, -1), 0, 2) .'"></progress> ' );
                                printf("%.2f", $osuCTB->level)
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">pp:</p>
                        <p class="info" id="p2">
                            <?php echo(number_format($osuCTB->pp_raw, 1) . "pp") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Accuracy:</p>
                        <p class="info" id="a2">
                            <?php echo(number_format($osuCTB->accuracy, 2) . "%") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playcount:</p>
                        <p class="info" id="pc2">
                            <?php echo(number_format($osuCTB->playcount)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Ranked Score:</p>
                        <p class="info" id="rs2">
                            <?php echo(number_format($osuCTB->ranked_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Score:</p>
                        <p class="info" id="ts2">
                            <?php echo(number_format($osuCTB->total_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Hits:</p>
                        <p class="info" id="th2">
                            <?php echo(number_format($osuCTB->count50 + $osuCTB->count100 + $osuCTB->count300)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playtime:</p>
                        <p class="info" id="pt2">
                            <?php echo(number_format(round(($osuCTB->total_seconds_played)/3600, 0)) . " hours") ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="thing" id="mania">
                    <img class="ctrimg" src="img/mania-icon.png">
                    <p class="rank" id="r3">
                        <?php echo("#" . number_format($osuMANIA->pp_rank)) ?>
                    </p>
                    <p class="crank" id="cr3">
                        <?php
                            echo('<img class="flag" src="flags/' . $osuMANIA->country . '.png"> ');
                            echo("#" . number_format($osuMANIA->pp_country_rank));
                        ?>
                    </p>
                    <div class="infoline">
                        <p class="infotext">lvl:</p>
                        <p class="info" id="lv3">
                            <?php
                                if (is_null($osuMANIA->level)) {
                                    $maniaLVLint = 0;
                                    $maniaLVLdec = 000;
                                }
                                else {
                                    list($maniaLVLint, $maniaLVLdec) = explode('.', $osuMANIA->level);
                                }
                                echo('<progress id="lvl" max="100" value="'. substr(round($maniaLVLdec, -1), 0, 2) .'"></progress> ' );
                                printf("%.2f", $osuMANIA->level)
                            ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">pp:</p>
                        <p class="info" id="p3">
                            <?php echo(number_format($osuMANIA->pp_raw, 1) . "pp") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Accuracy:</p>
                        <p class="info" id="a3">
                            <?php echo(number_format($osuMANIA->accuracy, 2) . "%") ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playcount:</p>
                        <p class="info" id="pc3">
                            <?php echo(number_format($osuMANIA->playcount)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Ranked Score:</p>
                        <p class="info" id="rs3">
                            <?php echo(number_format($osuMANIA->ranked_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Score:</p>
                        <p class="info" id="ts3">
                            <?php echo(number_format($osuMANIA->total_score)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Total Hits:</p>
                        <p class="info" id="th3">
                            <?php echo(number_format($osuMANIA->count50 + $osuMANIA->count100 + $osuMANIA->count300)) ?>
                        </p>
                    </div>
                    <div class="infoline">
                        <p class="infotext">Playtime:</p>
                        <p class="info" id="pt3">
                            <?php echo(number_format(round(($osuMANIA->total_seconds_played)/3600, 0)) . " hours") ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>3zachm - <a href="../" target="_self">Home</a></p>
        </div>
    </div>
</body>