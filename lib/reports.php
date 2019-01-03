<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


// Detect if loaded by the user or by PHP
if (count(get_included_files()) == 1) {
    define("LOADED", true);
} else {
    define("LOADED", false);
}

require_once __DIR__ . "/../required.php";

dieifnotloggedin();

if (LOADED) {
    if (isset($VARS['type']) && isset($VARS['format'])) {
        generateReport($VARS['type'], $VARS['format']);
        die();
    } else {
        $Strings->get("invalid parameters");
        die();
    }
}

function getUserReport(): Report {
    global $database, $Strings;
    $users = $database->select(
            "accounts", [
        "[>]acctstatus" => ["acctstatus" => "statusid"],
        "[>]accttypes" => ["accttype" => "typeid"]
            ], [
        "uid", "username", "realname", "email", "statuscode", "typecode", "authsecret"
            ]
    );

    $report = new Report($Strings->get("Users", false));

    $report->setHeader([
        $Strings->get("uid", false),
        $Strings->get("username", false),
        $Strings->get("name", false),
        $Strings->get("email", false),
        $Strings->get("status", false),
        $Strings->get("type", false),
        $Strings->get("2fa", false)
        ]);

    for ($i = 0; $i < count($users); $i++) {
        $report->addDataRow([
            $users[$i]["uid"],
            $users[$i]["username"],
            $users[$i]["realname"],
            $users[$i]["email"],
            $users[$i]["statuscode"],
            $users[$i]["typecode"],
            is_null($users[$i]["authsecret"]) ? "0" : "1"
        ]);
    }
    return $report;
}

function getGroupReport() {
    global $database, $Strings;
    $groups = $database->select('assigned_groups', [
        "[>]groups" => ['groupid'],
        "[>]accounts" => ['uid']
            ], [
        'username',
        'realname',
        'accounts.uid',
        'groupname',
        'groupid'
    ]);
    $header = [$Strings->get("group id", false), $Strings->get("group name", false), $Strings->get("uid", false), $Strings->get("username", false), $Strings->get("name", false)];
    $data = [];
    for ($i = 0; $i < count($groups); $i++) {
        $data[] = [
            $groups[$i]["groupid"],
            $groups[$i]["groupname"],
            $groups[$i]["uid"],
            $groups[$i]["username"],
            $groups[$i]["realname"]
        ];
    }
    return new Report($Strings->get("Groups", false), $header, $data);
}

function getManagerReport() {
    global $database, $Strings;
    $managers = $database->select('managers', [
        "[>]accounts (manager)" => ['managerid' => 'uid'],
        "[>]accounts (employee)" => ['employeeid' => 'uid']
            ], [
        'managerid',
        'employeeid',
        'manager.username (manageruser)',
        'employee.username (employeeuser)',
        'manager.realname (managername)',
        'employee.realname (employeename)',
    ]);
    $header = [$Strings->get("manager name", false), $Strings->get("manager username", false), $Strings->get("employee name", false), $Strings->get("employee username", false)];
    $data = [];
    for ($i = 0; $i < count($managers); $i++) {
        $data[] = [
            $managers[$i]["managername"],
            $managers[$i]["manageruser"],
            $managers[$i]["employeename"],
            $managers[$i]["employeeuser"]
        ];
    }
    return new Report($Strings->get("Managers", false), $header, $data);
}

function getPermissionReport() {
    global $database, $Strings;
    $permissions = $database->select('assigned_permissions', [
        "[>]accounts" => ['uid' => 'uid'],
        "[>]permissions" => ['permid' => 'permid']
            ], [
        'username',
        'realname',
        'assigned_permissions.uid',
        'permissions.permid',
        'permcode'
    ]);
    $header = [$Strings->get("uid", false), $Strings->get("username", false), $Strings->get("name", false), $Strings->get("permission", false), $Strings->get("permission id", false)];
    $data = [];
    for ($i = 0; $i < count($permissions); $i++) {
        $data[] = [
            $permissions[$i]["uid"],
            $permissions[$i]["username"],
            $permissions[$i]["realname"],
            $permissions[$i]["permcode"],
            $permissions[$i]["permid"],
        ];
    }
    return new Report($Strings->get("Permissions", false), $header, $data);
}

function getSecurityReport() {
    global $database, $Strings;
    $log = $database->select('authlog', [
        "[>]logtypes" => ['logtype'],
        "[>]accounts" => ['uid']
            ], [
        'logtime',
        'typename',
        'uid',
        'username',
        'realname',
        'ip',
        'otherdata'
    ]);
    $header = [$Strings->get("logtime", false), $Strings->get("logtype", false), $Strings->get("ip address", false), $Strings->get("uid", false), $Strings->get("username", false), $Strings->get("name", false), $Strings->get("other data", false)];
    $data = [];
    for ($i = 0; $i < count($log); $i++) {
        $data[] = [
            $log[$i]["logtime"],
            $log[$i]["typename"],
            $log[$i]["ip"],
            $log[$i]["uid"],
            $log[$i]["username"],
            $log[$i]["realname"],
            $log[$i]["otherdata"]
        ];
    }
    return new Report($Strings->get("Security", false), $header, $data);
}

function getReport($type): Report {
    switch ($type) {
        case "users":
            return getUserReport();
            break;
        case "groups":
            return getGroupReport();
            break;
        case "managers":
            return getManagerReport();
            break;
        case "permissions":
            return getPermissionReport();
            break;
        case "security":
            return getSecurityReport();
            break;
        default:
            return new Report("error", ["ERROR"], ["Invalid report type."]);
    }
}

function generateReport($type, $format) {
    $report = getReport($type);
    $report->output($format);
}
