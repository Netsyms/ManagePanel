<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$show_deleted = false;
if ($VARS['show_deleted'] == 1) {
    $show_deleted = true;
}

$out = [];

$out['draw'] = intval($VARS['draw']);

if ($show_deleted) {
    $out['recordsTotal'] = $database->count('accounts');
} else {
    $out['recordsTotal'] = $database->count('accounts', ['deleted' => 0]);
}
$filter = false;

// sort
$order = null;
$sortby = "DESC";
if ($VARS['order'][0]['dir'] == 'asc') {
    $sortby = "ASC";
}
switch ($VARS['order'][0]['column']) {
    case 2:
        $order = ["realname" => $sortby];
        break;
    case 3:
        $order = ["username" => $sortby];
        break;
    case 4:
        $order = ["email" => $sortby];
        break;
    case 5:
        $order = ["authsecret" => $sortby];
        break;
    case 6:
        $order = ["statuscode" => $sortby];
        break;
    case 7:
        $order = ["typecode" => $sortby];
        break;
}

// search
if (!is_empty($VARS['search']['value'])) {
    $filter = true;
    if ($show_deleted) {
        $wherenolimit = [
            "OR" => [
                "username[~]" => $VARS['search']['value'],
                "realname[~]" => $VARS['search']['value'],
                "email[~]" => $VARS['search']['value'],
                "statuscode[~]" => $VARS['search']['value'],
                "typecode[~]" => $VARS['search']['value']
            ]
        ];
    } else {
        $wherenolimit = [
            "AND" => [
                "OR" => [
                    "username[~]" => $VARS['search']['value'],
                    "realname[~]" => $VARS['search']['value'],
                    "email[~]" => $VARS['search']['value'],
                    "statuscode[~]" => $VARS['search']['value'],
                    "typecode[~]" => $VARS['search']['value']
                ],
                "deleted" => 0
            ]
        ];
    }
    $where = $wherenolimit;
    $where["LIMIT"] = [$VARS['start'], $VARS['length']];
} else {
    $where = ["LIMIT" => [$VARS['start'], $VARS['length']]];
    if (!$show_deleted) {
        $where["deleted"] = 0;
    }
}
if (!is_null($order)) {
    $where["ORDER"] = $order;
}


$users = $database->select('accounts', [
    "[>]acctstatus" => ['acctstatus' => 'statusid'],
    "[>]accttypes" => ['accttype' => 'typeid']
        ], [
    'uid',
    'username',
    'realname',
    'email',
    'authsecret (2fa)',
    'acctstatus',
    'statuscode',
    'accttype',
    'typecode',
    'deleted'
        ], $where);


$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('accounts', [
        "[>]acctstatus" => ['acctstatus' => 'statusid'],
        "[>]accttypes" => ['accttype' => 'typecode']
            ], 'uid', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
for ($i = 0; $i < count($users); $i++) {
    $users[$i]["2fa"] = (is_empty($users[$i]["2fa"]) ? false : true);
    $users[$i]["editbtn"] = '<a class="btn btn-blue btn-sm" href="app.php?page=edituser&id=' . $users[$i]['uid'] . '"><i class="far fa-edit"></i> ' . lang("edit", false) . '</a>';
}
$out['users'] = $users;

echo json_encode($out);
