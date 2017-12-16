<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('authlog');
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 1:
        $order = ["logtime" => $sortby];
        break;
    case 2:
        $order = ["typename" => $sortby];
        break;
    case 3:
        $order = ["username" => $sortby];
        break;
    case 4:
        $order = ["ip" => $sortby];
        break;
    case 5:
        $order = ["otherdata" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "OR" => [
            "logtime[~]" => $VARS['search']['value'],
            "typename[~]" => $VARS['search']['value'],
            "username[~]" => $VARS['search']['value'],
            "ip[~]" => $VARS['search']['value'],
            "otherdata[~]" => $VARS['search']['value']
        ]
    ];
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["LIMIT" => [$VARS['start'], $VARS['length']]];
}
if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$log = $database->select('authlog', [
    "[>]accounts" => ['uid' => 'uid'],
    "[>]logtypes" => ['logtype' => 'logtype']
        ], [
    'logtime',
    'typename',
    'username',
    'ip',
    'otherdata'
        ], $where);


$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('authlog', [
        "[>]accounts" => ['uid' => 'uid'],
        "[>]logtypes" => ['logtype' => 'logtype']
            ], 'logid', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
$out['log'] = $log;

echo json_encode($out);
