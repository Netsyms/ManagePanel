<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$key = hash("SHA1", random_bytes(100));

$form = new FormBuilder($Strings->get("Add Key", false), "fas fa-plus");

$form->addInput("key", $key, "text", true, "key", null, $Strings->get("Key", false), "fas fa-key", 12, 20);

$form->addInput("type", "", "select", true, "type", ["NONE" => "NONE", "AUTH" => "AUTH", "READ" => "READ", "FULL" => "FULL"], $Strings->get("Type", false), "fas fa-list", 6);

$form->addInput("notes", "", "textarea", false, "notes", null, $Strings->get("Notes", false), "fas fa-sticky-note", 6);

$form->addButton($Strings->get("Add Key", false), "fas fa-save", null, "submit", null, null, "", "btn btn-success");

$form->addHiddenInput("action", "addapikey");
$form->addHiddenInput("source", "apikeys");

$form->generate();
