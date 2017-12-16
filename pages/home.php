<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-light-blue">
            <div class="panel-heading"><div class="panel-title"><?php lang("total users") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-users"></i> <?php echo $database->count('accounts'); ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=users" class="black-text"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view users'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-amber">
            <div class="panel-heading"><div class="panel-title"><?php lang("locked accounts") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-user-times"></i> <?php echo $database->count('accounts', ['OR' => ['acctstatus #LOCKED_OR_DISABLED' => 2, 'acctstatus #CHANGE_PASSWORD' => 3]]); ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=users" class="black-text"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view users'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-light-green">
            <div class="panel-heading"><div class="panel-title"><?php lang("security log entries") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-list"></i> <?php echo $database->count('authlog'); ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=authlog" class="black-text"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view security log'); ?></a>
            </div>
        </div>
    </div>
</div>