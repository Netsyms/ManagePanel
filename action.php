<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";
require_once __DIR__ . "/lib/login.php";
require_once __DIR__ . "/lib/authlog.php";

if ($VARS['action'] !== "signout") {
    dieifnotloggedin();
}

if (account_has_permission($_SESSION['username'], "ADMIN") == FALSE) {
    die("You don't have permission to be here.");
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
        if (is_empty($VARS['id'])) {
            $insert = true;
        } else {
            if ($database->has('accounts', ['uid' => $VARS['id']])) {
                $insert = false;
            } else {
                returnToSender("invalid_userid");
            }
        }
        if (is_empty($VARS['name']) || is_empty($VARS['username']) || is_empty($VARS['status'])) {
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

        if (!is_empty($VARS['pass'])) {
            $data['password'] = password_hash($VARS['pass'], PASSWORD_BCRYPT);
        }

        if ($insert) {
            $data['phone1'] = "";
            $data['phone2'] = "";
            $data['accttype'] = 1;
            $database->insert('accounts', $data);
            insertAuthLog(17, $_SESSION['uid'], $data['username'] . ", " . $data['realname'] . ", " . $data['email'] . ", " . $data['acctstatus']);
        } else {
            $olddata = $database->select('accounts', '*', ['uid' => $VARS['id']])[0];
            $database->update('accounts', $data, ['uid' => $VARS['id']]);
            insertAuthLog(18, $_SESSION['uid'], "OLD: " . $olddata['username'] . ", " . $olddata['realname'] . ", " . $olddata['email'] . ", " . $olddata['acctstatus'] . "; NEW: " . $data['username'] . ", " . $data['realname'] . ", " . $data['email'] . ", " . $data['acctstatus']);
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
        insertAuthLog(16, $_SESSION['uid'], $olddata['username'] . ", " . $olddata['realname'] . ", " . $olddata['email'] . ", " . $olddata['acctstatus']);
        returnToSender("user_deleted");
    case "rmtotp":
        if ($database->has('accounts', ['uid' => $VARS['id']]) !== TRUE) {
            returnToSender("invalid_userid");
        }
        $u = $database->get('accounts', 'username', ['uid' => $VARS['id']]);
        $database->update('accounts', ["authsecret" => null], ['uid' => $VARS['id']]);
        insertAuthLog(10, $_SESSION['uid'], $u);
        returnToSender("2fa_removed");
    case "clearlog":
        $rows = $database->count('authlog');
        $database->delete('authlog', []);
        insertAuthLog(15, $_SESSION['uid'], lang2("removed n entries", ['n' => $rows], false));
        returnToSender("log_cleared");
    case "editmanager":
        require_once __DIR__ . "/lib/userinfo.php";
        if (!$database->has('accounts', ['username' => $VARS['manager']])) {
            returnToSender("invalid_manager");
        }
        $manager = getUserByUsername($VARS['manager'])['uid'];
        $already_assigned = $database->select('managers', 'employeeid', ['managerid' => $manager]);

        foreach ($VARS['employees'] as $u) {
            if (!user_exists($u)) {
                returnToSender("user_not_exists", htmlentities($u));
            }
            $uid = getUserByUsername($u)['uid'];
            $database->insert('managers', ['employeeid' => $uid, 'managerid' => $manager]);
            $already_assigned = array_diff($already_assigned, [$uid]); // Remove user from old list
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
        if (is_empty($VARS['q']) || strlen($VARS['q']) < 3) {
            exit(json_encode([]));
        }
        $data = $database->select('accounts', ['uid', 'username', 'realname (name)'], ["OR" => ['username[~]' => $VARS['q'], 'realname[~]' => $VARS['q']], "LIMIT" => 10]);
        exit(json_encode($data));
    case "autocomplete_permission":
        header("Content-Type: application/json");
        if (is_empty($VARS['q'])) {
            exit(json_encode([]));
        }
        $data = $database->select('permissions', ['permcode (name)', 'perminfo (info)'], ["OR" => ['permcode[~]' => $VARS['q'], 'perminfo[~]' => $VARS['q']], "LIMIT" => 10]);
        exit(json_encode($data));
    case "export":
        require_once __DIR__ . "/lib/reports.php";
        generateReport($VARS['type'], $VARS['format']);
        break;
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
    default:
        die("Invalid action");
}