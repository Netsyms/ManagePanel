<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('managers');
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 2:
        $order = ["managername" => $sortby];
        break;
    case 3:
        $order = ["employeename" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "OR" => [
            "manager.username[~]" => $VARS['search']['value'],
            "employee.username[~]" => $VARS['search']['value'],
            "manager.realname[~]" => $VARS['search']['value'],
            "employee.realname[~]" => $VARS['search']['value']
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
        ], $where);


$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('managers', [
        "[>]accounts (manager)" => ['managerid' => 'uid'],
        "[>]accounts (employee)" => ['employeeid' => 'uid']
            ], 'managerid', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
for ($i = 0; $i < count($managers); $i++) {
    $managers[$i]["delbtn"] = '<a class="btn btn-danger btn-xs" href="app.php?page=delmanager&mid=' . $managers[$i]['managerid'] . '&eid=' . $managers[$i]['employeeid'] . '"><i class="fa fa-trash"></i> ' . lang("delete", false) . '</a>';
}
$out['managers'] = $managers;

echo json_encode($out);
