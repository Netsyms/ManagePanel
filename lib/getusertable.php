<?php

require __DIR__ . '/../required.php';

dieifnotloggedin();

header("Content-Type: application/json");

$out = [];

$out['draw'] = intval($VARS['draw']);

$out['recordsTotal'] = $database->count('accounts');
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
    $wherenolimit = [
        "OR" => [
            "username[~]" => $VARS['search']['value'],
            "realname[~]" => $VARS['search']['value'],
            "email[~]" => $VARS['search']['value'],
            "statuscode[~]" => $VARS['search']['value'],
            "typecode[~]" => $VARS['search']['value']
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
    'typecode'
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
    $users[$i]["editbtn"] = '<a class="btn btn-blue btn-xs" href="app.php?page=edituser&id=' . $users[$i]['uid'] . '"><i class="fa fa-pencil-square-o"></i> ' . lang("edit", false) . '</a>';
}
$out['users'] = $users;

echo json_encode($out);
