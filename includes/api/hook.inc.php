<?php

require_once(__DIR__ . '/../../../../secure/.env');

function getRateHook() {
    global $_RATEWEB;
    return $_RATEWEB;
}