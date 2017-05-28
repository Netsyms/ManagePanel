<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";
require_once __DIR__ . "/lib/login.php";
require_once __DIR__ . "/lib/authlog.php";

dieifnotloggedin();

if (account_has_permission($_SESSION['username'], "ADMIN") == FALSE) {
    die("You don't have permission to be here.");
}

/**
 * Redirects back to the page ID in $_POST/$_GET['source'] with the given message ID.
 * The message will be displayed by the app.
 * @param string $msg message ID (see lang/messages.php)
 * @param string $arg If set, replaces "{arg}" in the message string when displayed to the user.
 */
function returnToSender($msg, $arg = "") {
    global $VARS;
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=$msg&arg=$arg");
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
            'acctstatus' => $VARS['status']
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
        insertAuthLog(16, $_SESSION['uid'], $olddata['username'] . ", " . $olddata['realname'] . ", " . $olddata['email'] . ", " . $olddata['acctstatus']);
        returnToSender("user_deleted");
    case "clearlog":
        $rows = $database->count('authlog');
        $database->delete('authlog');
        insertAuthLog(15, $_SESSION['uid'], lang2("removed n entries", ['n' => $rows], false));
        returnToSender("log_cleared");
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
    case "autocomplete_user":
        header("Content-Type: application/json");
        if (is_empty($VARS['q']) || strlen($VARS['q']) < 3) {
            exit(json_encode([]));
        }
        $data = $database->select('accounts', ['uid', 'username', 'realname (name)'], ["OR" => ['username[~]' => $VARS['q'], 'realname[~]' => $VARS['q']], "LIMIT" => 10]);
        exit(json_encode($data));
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
    default:
        die("Invalid action");
}