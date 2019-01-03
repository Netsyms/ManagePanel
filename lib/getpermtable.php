<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('assigned_permissions');
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
        $order = ["permcode" => $sortby];
        break;
}

// search
if (!empty($VARS['search']['value'])) {
    $filter = true;
    $wherenolimit = [
        "OR" => [
            "username[~]" => $VARS['search']['value'],
            "realname[~]" => $VARS['search']['value'],
            "permcode[~]" => $VARS['search']['value']
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


$data = $database->select('assigned_permissions', [
    "[>]accounts" => ['uid' => 'uid'],
    "[>]permissions" => ['permid' => 'permid']
        ], [
    'username',
    'realname',
    'assigned_permissions.uid',
    'permissions.permid',
    'permcode'
        ], $where);


$out['status'] = "OK";
if ($filter) {
    $recordsFiltered = $database->count('assigned_permissions', [
        "[>]accounts" => ['uid' => 'uid'],
        "[>]permissions" => ['permid' => 'permid']
            ], 'assigned_permissions.uid', $wherenolimit);
} else {
    $recordsFiltered = $out['recordsTotal'];
}
$out['recordsFiltered'] = $recordsFiltered;
for ($i = 0; $i < count($data); $i++) {
    $data[$i]["delbtn"] = '<a class="btn btn-danger btn-xs" href="app.php?page=delpermission&uid=' . $data[$i]['uid'] . '&pid=' . $data[$i]['permid'] . '"><i class="fa fa-trash"></i> ' . $Strings->get("delete", false) . '</a>';
}
$out['perms'] = $data;

echo json_encode($out);
