<?php

require_once __DIR__ . "/../required.php";

use League\Csv\Writer;
use odsPhpGenerator\ods;
use odsPhpGenerator\odsTable;
use odsPhpGenerator\odsTableRow;
use odsPhpGenerator\odsTableColumn;
use odsPhpGenerator\odsTableCellString;
use odsPhpGenerator\odsStyleTableColumn;
use odsPhpGenerator\odsStyleTableCell;

dieifnotloggedin();

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
    header('Content-Disposition: attachment; filename="' . $name . "_" . date("Y-m-d_Hm") . ".csv" . '"');
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
    $ods->downloadOdsFile($name . "_" . date("Y-m-d_Hm") . ".ods");
}

function generateReport($type, $format) {
    $data = getReportData($type);
    switch ($format) {
        case "ods":
            dataToODS($data, $type);
            break;
        case "csv":
        default:
            echo dataToCSV($data, $type);
            break;
    }
}
