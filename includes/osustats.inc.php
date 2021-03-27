<?php

if (isset($_POST["submit"])) {
    $username = $_POST["uid"];
    header("location: /osuranks?user=" . $username);
    exit();
}
else {
    header("location: /osuranks?error=notfound");
    exit();
}