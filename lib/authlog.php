<?php

require_once __DIR__ . "/../required.php";
require_once __DIR__ . "/iputils.php";

dieifnotloggedin();

function insertAuthLog($type, $uid = null, $data = "") {
    global $database;
    // find IP address
    $ip = getClientIP();
    $database->insert("authlog", ['logtime' => date("Y-m-d H:i:s"), 'logtype' => $type, 'uid' => $uid, 'ip' => $ip, 'otherdata' => $data]);
}