<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$userdata = [
    'uid' => '',
    'username' => '',
    'realname' => '',
    'email' => '',
    'authsecret' => '',
    'acctstatus' => '',
    'typecode' => 'LOCAL',
    'deleted' => 0
];

$editing = false;
$user = new User(-1);

if (!empty($VARS['id']) && preg_match("/[0-9]+/", $VARS['id'])) {
    $user = new User($VARS['id']);
    if ($user->exists()) {
        $editing = true;
    } else {
        // user id is invalid, redirect to a page that won't cause an error when pressing Save
        header('Location: app.php?page=edituser');
    }
}

$form = new FormBuilder("", "far fa-edit");

if ($editing) {
    $form->setTitle($Strings->build("editing user", ['user' => "<span id=\"name_title\">" . htmlspecialchars($user->getName()) . "</span>"], false));
} else {
    $form->setTitle($Strings->get("adding user", false));
}

$form->addInput("name", (empty($user->getName()) ? "" : $user->getName()), "text", true, "name", null, $Strings->get("name", false), "fas fa-user");
$form->addInput("username", (empty($user->getUsername()) ? "" : $user->getUsername()), "text", true, "username", null, $Strings->get("username", false), "fas fa-id-badge");
$form->addInput("email", (empty($user->getEmail()) ? "" : $user->getEmail()), "email", false, "email", null, $Strings->get("email", false), "fas fa-envelope");
$form->addInput("pass", "", "text", false, "pass", null, $Strings->get("new password", false), "fas fa-lock");
$form->addInput("status", $user->getStatus()->get(), "select", true, "status", [
    AccountStatus::NORMAL => "NORMAL",
    AccountStatus::LOCKED_OR_DISABLED => "LOCKED_OR_DISABLED",
    AccountStatus::CHANGE_PASSWORD => "CHANGE_PASSWORD",
    AccountStatus::TERMINATED => "TERMINATED",
    AccountStatus::ALERT_ON_ACCESS => "ALERT_ON_ACCESS"
        ], $Strings->get("status", false), "fas fa-check-circle");

if ($editing) {
    $form->addHiddenInput("id", $user->getUID());
}
$form->addHiddenInput("action", "edituser");
$form->addHiddenInput("source", "users");

$form->addButton($Strings->get("save", false), "fas fa-save", null, "submit", null, null, "", "btn btn-success mr-auto");
if ($editing) {
    if (!empty($userdata['authsecret'])) {
        $form->addButton($Strings->get("remove 2fa", false), "fas fa-unlock", "action.php?action=rmtotp&source=users&id=" . $user->getUID(), "", null, null, "", "btn btn-warning btn-sm");
    }
    $form->addButton($Strings->get("delete", false), "fas fa-times", "app.php?page=deluser&id=" . $user->getUID(), "", null, null, "", "btn btn-danger");
}

$form->generate();
