<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";

if ($VARS['action'] !== "signout") {
    dieifnotloggedin();
}

/**
 * Redirects back to the page ID in $_POST/$_GET['source'] with the given message ID.
 * The message will be displayed by the app.
 * @param string $msg message ID (see lang/messages.php)
 * @param string $arg If set, replaces "{arg}" in the message string when displayed to the user.
 * @param [key=>val] $additional Put the given key-value array in the URL
 */
function returnToSender($msg, $arg = "", $additional = []) {
    global $VARS;
    $add = "";
    if ($additional != []) {
        foreach ($additional as $key => $val) {
            $add .= "&" . urlencode($key) . "=" . urlencode($val);
        }
    }
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . $add . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . $add . "&msg=$msg&arg=$arg");
    }
    die();
}

switch ($VARS['action']) {
    case "edituser":
        if (empty($VARS['id'])) {
            $insert = true;
        } else {
            if ($database->has('accounts', ['uid' => $VARS['id']])) {
                $insert = false;
            } else {
                returnToSender("invalid_userid");
            }
        }
        if (empty($VARS['name']) || empty($VARS['username']) || empty($VARS['status'])) {
            returnToSender('invalid_parameters');
        }

        if (!$database->has('acctstatus', ['statusid' => $VARS['status']])) {
            returnToSender("invalid_parameters");
        }

        $data = [
            'realname' => $VARS['name'],
            'username' => $VARS['username'],
            'email' => $VARS['email'],
            'acctstatus' => $VARS['status'],
            'deleted' => 0
        ];

        if (!empty($VARS['pass'])) {
            $data['password'] = password_hash($VARS['pass'], PASSWORD_BCRYPT);
        }

        if ($insert) {
            $data['phone1'] = "";
            $data['phone2'] = "";
            $data['accttype'] = 1;
            $database->insert('accounts', $data);
            Log::insert(LogType::USER_ADDED, $_SESSION['uid'], $data['username'] . ", " . $data['realname'] . ", " . $data['email'] . ", " . $data['acctstatus']);
        } else {
            $olddata = $database->select('accounts', '*', ['uid' => $VARS['id']])[0];
            $database->update('accounts', $data, ['uid' => $VARS['id']]);
            Log::insert(LogType::USER_EDITED, $_SESSION['uid'], "OLD: " . $olddata['username'] . ", " . $olddata['realname'] . ", " . $olddata['email'] . ", " . $olddata['acctstatus'] . "; NEW: " . $data['username'] . ", " . $data['realname'] . ", " . $data['email'] . ", " . $data['acctstatus']);
        }

        returnToSender("user_saved");
    case "deleteuser":
        if ($database->has('accounts', ['uid' => $VARS['id']]) !== TRUE) {
            returnToSender("invalid_userid");
        }
        $olddata = $database->select('accounts', '*', ['uid' => $VARS['id']])[0];
        $database->delete('accounts', ['uid' => $VARS['id']]);
        if (!is_null($database->error()[1])) {
            // If we can't delete the account (because it's referenced elsewhere),
            // we will flag it as deleted and set the status to LOCKED_OR_DISABLED.
            $database->update('accounts', ['acctstatus' => 2, 'deleted' => 1], ['uid' => $VARS['id']]);
        }
        Log::insert(LogType::USER_REMOVED, $_SESSION['uid'], $olddata['username'] . ", " . $olddata['realname'] . ", " . $olddata['email'] . ", " . $olddata['acctstatus']);
        returnToSender("user_deleted");
    case "rmtotp":
        if ($database->has('accounts', ['uid' => $VARS['id']]) !== TRUE) {
            returnToSender("invalid_userid");
        }
        $u = $database->get('accounts', 'username', ['uid' => $VARS['id']]);
        $database->update('accounts', ["authsecret" => null], ['uid' => $VARS['id']]);
        Log::insert(LogType::REMOVED_2FA, $_SESSION['uid'], $u);
        returnToSender("2fa_removed");
    case "clearlog":
        $rows = $database->count('authlog');
        $database->delete('authlog', []);
        Log::insert(LogType::LOG_CLEARED, $_SESSION['uid'], $Strings->build("removed n entries", ['n' => $rows], false));
        returnToSender("log_cleared");
    case "editmanager":
        if (!$database->has('accounts', ['username' => $VARS['manager']])) {
            returnToSender("invalid_manager");
        }
        $manager = User::byUsername($VARS['manager'])->getUID();
        $already_assigned = $database->select('managers', 'employeeid', ['managerid' => $manager]);

        foreach ($VARS['employees'] as $u) {
            $emp = User::byUsername($u);
            if (!$emp->exists()) {
                returnToSender("user_not_exists", htmlentities($emp->getUsername()));
            }
            $database->insert('managers', ['employeeid' => $emp->getUID(), 'managerid' => $manager]);
            $already_assigned = array_diff($already_assigned, [$emp->getUID()]); // Remove user from old list
        }
        foreach ($already_assigned as $uid) {
            $database->delete('managers', ["AND" => ['employeeid' => $uid, 'managerid' => $manager]]);
        }
        returnToSender("manager_assigned", "", ["man" => $VARS['manager']]);
        break;
    case "addmanager":
        if (!$database->has('accounts', ['username' => $VARS['manager']])) {
            returnToSender("invalid_userid");
        }
        if (!$database->has('accounts', ['username' => $VARS['employee']])) {
            returnToSender("invalid_userid");
        }
        $manageruid = $database->select('accounts', 'uid', ['username' => $VARS['manager']])[0];
        $employeeuid = $database->select('accounts', 'uid', ['username' => $VARS['employee']])[0];
        $database->insert('managers', ['managerid' => $manageruid, 'employeeid' => $employeeuid]);
        returnToSender("relationship_added");
    case "delmanager":
        if (!$database->has('managers', ['managerid' => $VARS['mid']])) {
            returnToSender("invalid_userid");
        }
        if (!$database->has('managers', ['employeeid' => $VARS['eid']])) {
            returnToSender("invalid_userid");
        }
        $database->delete('managers', ['AND' => ['managerid' => $VARS['mid'], 'employeeid' => $VARS['eid']]]);
        returnToSender("relationship_deleted");
    case "editperms":
        if (!$database->has('accounts', ['username' => $VARS['user']])) {
            returnToSender("invalid_userid");
        }
        $uid = $database->select('accounts', 'uid', ['username' => $VARS['user']])[0];
        $already_assigned = $database->select('assigned_permissions', 'permid', ['uid' => $uid]);
        $permids = [];
        foreach ($VARS['permissions'] as $perm) {
            if (!$database->has('permissions', ['permcode' => $perm])) {
                returnToSender("permission_not_exists", htmlentities($perm));
            }

            $permid = $database->get('permissions', 'permid', ['permcode' => $perm]);
            $permids[] = $permid;
            $already_assigned = array_diff($already_assigned, [$permid]); // Remove permission from old list
        }
        foreach ($already_assigned as $permid) {
            $database->delete('assigned_permissions', ["AND" => ['uid' => $uid, 'permid' => $permid]]);
        }
        foreach ($permids as $permid) {
            $database->insert('assigned_permissions', ['uid' => $uid, 'permid' => $permid]);
        }
        returnToSender("permissions_assigned", "", ["user" => $VARS['user']]);
    case "addpermission":
        if (!$database->has('accounts', ['username' => $VARS['user']])) {
            returnToSender("invalid_userid");
        }
        if (!$database->has('permissions', ['permcode' => $VARS['perm']])) {
            returnToSender("permission_not_exists", htmlentities($VARS['perm']));
        }
        $uid = $database->select('accounts', 'uid', ['username' => $VARS['user']])[0];
        $pid = $database->select('permissions', 'permid', ['permcode' => $VARS['perm']])[0];
        $database->insert('assigned_permissions', ['uid' => $uid, 'permid' => $pid]);
        returnToSender("permission_added");
    case "delpermission":
        if (!$database->has('accounts', ['uid' => $VARS['uid']])) {
            returnToSender("invalid_userid");
        }
        if (!$database->has('permissions', ['permid' => $VARS['pid']])) {
            returnToSender("permission_not_exists", htmlentities($VARS['pid']));
        }
        $database->delete('assigned_permissions', ['AND' => ['uid' => $VARS['uid'], 'permid' => $VARS['pid']]]);
        returnToSender("permission_deleted");
    case "autocomplete_user":
        header("Content-Type: application/json");
        if (empty($VARS['q']) || strlen($VARS['q']) < 3) {
            exit(json_encode([]));
        }
        $data = $database->select('accounts', ['uid', 'username', 'realname (name)'], ["OR" => ['username[~]' => $VARS['q'], 'realname[~]' => $VARS['q']], "LIMIT" => 10]);
        exit(json_encode($data));
    case "autocomplete_permission":
        header("Content-Type: application/json");
        if (empty($VARS['q'])) {
            exit(json_encode([]));
        }
        $data = $database->select('permissions', ['permcode (name)', 'perminfo (info)'], ["OR" => ['permcode[~]' => $VARS['q'], 'perminfo[~]' => $VARS['q']], "LIMIT" => 10]);
        exit(json_encode($data));
    case "assigngroup":
        if (!$database->has('groups', ['groupid' => $VARS['gid']])) {
            returnToSender("invalid_group");
        }
        $gid = $VARS['gid'];
        $already_assigned = $database->select('assigned_groups', 'uid', ['groupid' => $gid]);

        foreach ($VARS['users'] as $u) {
            $user = User::byUsername($u);
            if (!$user->exists()) {
                returnToSender("user_not_exists", htmlentities($user->getUsername()));
            }
            $database->insert('assigned_groups', ['groupid' => $gid, 'uid' => $user->getUID()]);
            $already_assigned = array_diff($already_assigned, [$user->getUID()]); // Remove user from old list
        }
        foreach ($already_assigned as $uid) {
            $database->delete('assigned_groups', ["AND" => ['uid' => $uid, 'groupid' => $gid]]);
        }
        returnToSender("group_assigned", "", ["gid" => $gid]);
        break;
    case "addgroup":
        $group = htmlspecialchars(strip_tags($VARS['group']), ENT_HTML5);
        if ($database->has('groups', ['groupname' => $group])) {
            returnToSender("group_exists");
        }
        $database->insert('groups', ['groupname' => $group]);
        returnToSender("group_added");
    case "rmgroup":
        if (!$database->has('groups', ['groupid' => $VARS['gid']])) {
            returnToSender("invalid_group");
        }
        $database->delete('assigned_groups', ['groupid' => $VARS['gid']]);
        $database->delete('groups', ['groupid' => $VARS['gid']]);
        returnToSender("group_deleted");
    case "export":
        require_once __DIR__ . "/lib/reports.php";
        generateReport($VARS['type'], $VARS['format']);
        break;
    case "revokeapikey":
        if (empty($VARS['key'])) {
            returnToSender("invalid_parameters");
        }
        if ($VARS['key'] == $SETTINGS['accounthub']['key']) {
            returnToSender("cannot_revoke_key_in_use");
        }
        $database->delete("apikeys", ['key' => $VARS['key'], "LIMIT" => 1]);
        returnToSender("api_key_revoked");
        break;
    case "addapikey":
        if (empty($VARS['key']) || empty($VARS['type'])) {
            returnToSender("invalid_parameters");
        }
        $keytypes = ["NONE", "AUTH", "READ", "FULL"];
        if (!in_array($VARS['type'], $keytypes)) {
            returnToSender("invalid_parameters");
        }
        if ($database->has("apikeys", ["key" => $VARS['key']])) {
            returnToSender("key_already_exists");
        }
        $database->insert("apikeys", ["key" => $VARS['key'], "notes" => $VARS['notes'], "type" => $VARS['type']]);
        returnToSender("api_key_added");
        break;
    case "signout":
        session_destroy();
        header('Location: index.php?logout=1');
        die("Logged out.");
    default:
        die("Invalid action");
}
