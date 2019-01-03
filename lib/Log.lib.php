<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class Log {

    /**
     *
     * @global $database
     * @param int/LogType $type Either an integer (as defined by the constants in class LogType) or a LogType object.
     * @param int/User $user Either a UID number or a User object.
     * @param string $data Extra data to include in the log, in addition to the timestamp, log type, user, and IP address.
     */
    public static function insert($type, $user, string $data = "") {
        global $database;
        // find IP address
        $ip = IPUtils::getClientIP();
        if (gettype($type) == "object" && is_a($type, "LogType")) {
            $type = $type->getType();
        }

        if (is_a($user, "User")) {
            $uid = $user->getUID();
        } else if (gettype($user) == "integer") {
            $uid = $user;
        } else {
            $uid = null;
        }

        $database->insert("authlog", ['logtime' => date("Y-m-d H:i:s"), 'logtype' => $type, 'uid' => $uid, 'ip' => $ip, 'otherdata' => $data]);
    }

}

class LogType {

    const LOGIN_OK = 1;
    const LOGIN_FAILED = 2;
    const PASSWORD_CHANGED = 3;
    const API_LOGIN_OK = 4;
    const API_LOGIN_FAILED = 5;
    const BAD_2FA = 6;
    const API_BAD_2FA = 7;
    const BAD_CAPTCHA = 8;
    const ADDED_2FA = 9;
    const REMOVED_2FA = 10;
    const LOGOUT = 11;
    const API_AUTH_OK = 12;
    const API_AUTH_FAILED = 13;
    const API_BAD_KEY = 14;
    const LOG_CLEARED = 15;
    const USER_REMOVED = 16;
    const USER_ADDED = 17;
    const USER_EDITED = 18;
    const MOBILE_LOGIN_OK = 19;
    const MOBILE_LOGIN_FAILED = 20;
    const MOBILE_BAD_KEY = 21;

    private $type;

    function __construct(int $type) {
        $this->type = $type;
    }

    public function getType(): int {
        return $type;
    }
}
