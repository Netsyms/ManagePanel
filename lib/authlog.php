<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


require_once __DIR__ . "/../required.php";
require_once __DIR__ . "/iputils.php";

dieifnotloggedin();

function insertAuthLog($type, $uid = null, $data = "") {
    global $database;
    // find IP address
    $ip = getClientIP();
    $database->insert("authlog", ['logtime' => date("Y-m-d H:i:s"), 'logtype' => $type, 'uid' => $uid, 'ip' => $ip, 'otherdata' => $data]);
}