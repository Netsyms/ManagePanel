<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";
require_once __DIR__ . "/lib/login.php";

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
        } else {
            $database->update('accounts', $data, ['uid' => $VARS['id']]);
        }

        returnToSender("user_saved");
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
    default:
        die("Invalid action");
}