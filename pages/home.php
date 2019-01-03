<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="card-deck">
    <div class="card text-dark bg-light-blue">
        <div class="card-body">
            <h4 class="card-title"><?php $Strings->get("total users") ?></h4>
            <h1><i class="fas fa-fw fa-users"></i> <?php echo $database->count('accounts'); ?></h1>
        </div>
        <div class="card-footer">
            <a href="app.php?page=users" class="text-dark"><i class="fa fa-arrow-right fa-fw"></i> <?php $Strings->get('view users'); ?></a>
        </div>
    </div>
    <div class="card text-dark bg-amber">
        <div class="card-body">
            <h4 class="card-title"><?php $Strings->get("locked accounts") ?></h4>
            <h1><i class="fas fa-fw fa-user-times"></i> <?php echo $database->count('accounts', ['OR' => ['acctstatus #LOCKED_OR_DISABLED' => 2, 'acctstatus #CHANGE_PASSWORD' => 3]]); ?></h1>
        </div>
        <div class="card-footer">
            <a href="app.php?page=users" class="text-dark"><i class="fa fa-arrow-right fa-fw"></i> <?php $Strings->get('view users'); ?></a>
        </div>
    </div>
    <div class="card text-dark bg-light-green">
        <div class="card-body">
            <h4 class="card-title"><?php $Strings->get("security log entries") ?></h4>
            <h1><i class="fas fa-fw fa-list"></i> <?php echo $database->count('authlog'); ?></h1>
        </div>
        <div class="card-footer">
            <a href="app.php?page=authlog" class="text-dark"><i class="fa fa-arrow-right fa-fw"></i> <?php $Strings->get('view security log'); ?></a>
        </div>
    </div>
</div>
