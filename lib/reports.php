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

use League\Csv\Writer;
use League\Csv\HTMLConverter;
use odsPhpGenerator\ods;
use odsPhpGenerator\odsTable;
use odsPhpGenerator\odsTableRow;
use odsPhpGenerator\odsTableColumn;
use odsPhpGenerator\odsTableCellString;
use odsPhpGenerator\odsStyleTableColumn;
use odsPhpGenerator\odsStyleTableCell;

// Allow access with a download code, for mobile app and stuff
$date = date("Y-m-d H:i:s");
if (isset($VARS['code']) && LOADED) {
    if (!$database2->has('report_access_codes', ["AND" => ['code' => $VARS['code'], 'expires[>]' => $date]])) {
        dieifnotloggedin();
    }
} else {
    dieifnotloggedin();
}

// Delete old DB entries
$database2->delete('report_access_codes', ['expires[<=]' => $date]);

if (LOADED) {
    if (isset($VARS['type']) && isset($VARS['format'])) {
        generateReport($VARS['type'], $VARS['format']);
        die();
    } else {
        lang("invalid parameters");
        die();
    }
}

function getUserReport() {
    global $database;
    $users = $database->select(
            "accounts", [
        "[>]acctstatus" => ["acctstatus" => "statusid"],
        "[>]accttypes" => ["accttype" => "typeid"]
            ], [
        "uid", "username", "realname", "email", "statuscode", "typecode", "authsecret"
            ]
    );
    $header = [lang("uid", false), lang("username", false), lang("name", false), lang("email", false), lang("status", false), lang("type", false), lang("2fa", false)];
    $out = [$header];
    for ($i = 0; $i < count($users); $i++) {
        $out[] = [
            $users[$i]["uid"],
            $users[$i]["username"],
            $users[$i]["realname"],
            $users[$i]["email"],
            $users[$i]["statuscode"],
            $users[$i]["typecode"],
            is_null($users[$i]["authsecret"]) ? "0" : "1"
        ];
    }
    return $out;
}

function getManagerReport() {
    global $database;
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
    $header = [lang("manager name", false), lang("manager username", false), lang("employee name", false), lang("employee username", false)];
    $out = [$header];
    for ($i = 0; $i < count($managers); $i++) {
        $out[] = [
            $managers[$i]["managername"],
            $managers[$i]["manageruser"],
            $managers[$i]["employeename"],
            $managers[$i]["employeeuser"]
        ];
    }
    return $out;
}

function getPermissionReport() {
    global $database;
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
    $header = [lang("uid", false), lang("username", false), lang("name", false), lang("permission", false), lang("permission id", false)];
    $out = [$header];
    for ($i = 0; $i < count($permissions); $i++) {
        $out[] = [
            $permissions[$i]["uid"],
            $permissions[$i]["username"],
            $permissions[$i]["realname"],
            $permissions[$i]["permcode"],
            $permissions[$i]["permid"],
        ];
    }
    return $out;
}

function getSecurityReport() {
    global $database;
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
    $header = [lang("logtime", false), lang("logtype", false), lang("ip address", false), lang("uid", false), lang("username", false), lang("name", false), lang("other data", false)];
    $out = [$header];
    for ($i = 0; $i < count($log); $i++) {
        $out[] = [
            $log[$i]["logtime"],
            $log[$i]["typename"],
            $log[$i]["ip"],
            $log[$i]["uid"],
            $log[$i]["username"],
            $log[$i]["realname"],
            $log[$i]["otherdata"]
        ];
    }
    return $out;
}

function getReportData($type) {
    switch ($type) {
        case "users":
            return getUserReport();
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
            return [["error"]];
    }
}

function dataToCSV($data, $name = "report") {
    $csv = Writer::createFromString('');
    $csv->insertAll($data);
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . "_" . date("Y-m-d_Hi") . ".csv" . '"');
    echo $csv;
    die();
}

function dataToODS($data, $name = "report") {
    $ods = new ods();
    $styleColumn = new odsStyleTableColumn();
    $styleColumn->setUseOptimalColumnWidth(true);
    $headerstyle = new odsStyleTableCell();
    $headerstyle->setFontWeight("bold");
    $table = new odsTable($name);

    for ($i = 0; $i < count($data[0]); $i++) {
        $table->addTableColumn(new odsTableColumn($styleColumn));
    }

    $rowid = 0;
    foreach ($data as $datarow) {
        $row = new odsTableRow();
        foreach ($datarow as $cell) {
            if ($rowid == 0) {
                $row->addCell(new odsTableCellString($cell, $headerstyle));
            } else {
                $row->addCell(new odsTableCellString($cell));
            }
        }
        $table->addRow($row);
        $rowid++;
    }
    $ods->addTable($table);
    $ods->downloadOdsFile($name . "_" . date("Y-m-d_Hi") . ".ods");
}

function dataToHTML($data, $name = "report") {
    global $SECURE_NONCE;
    // HTML exporter doesn't like null values
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]); $j++) {
            if (is_null($data[$i][$j])) {
                $data[$i][$j] = '';
            }
        }
    }
    header('Content-type: text/html');
    $converter = new HTMLConverter();
    $out = "<!DOCTYPE html>\n"
            . "<meta charset=\"utf-8\">\n"
            . "<meta name=\"viewport\" content=\"width=device-width\">\n"
            . "<title>" . $name . "_" . date("Y-m-d_Hi") . "</title>\n"
            . <<<STYLE
<style nonce="$SECURE_NONCE">
    .table-csv-data {
        border-collapse: collapse;
    }
    .table-csv-data tr:first-child {
        font-weight: bold;
    }
    .table-csv-data tr td {
        border: 1px solid black;
    }
</style>
STYLE
            . $converter->convert($data);
    echo $out;
}

function generateReport($type, $format) {
    $data = getReportData($type);
    switch ($format) {
        case "ods":
            dataToODS($data, $type);
            break;
        case "html":
            dataToHTML($data, $type);
            break;
        case "csv":
        default:
            echo dataToCSV($data, $type);
            break;
    }
}
