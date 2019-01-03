<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();
?>
<div class="row justify-content-center">
    <div class="col-12 col-sm-6 col-sm-offset-3">
        <div class="card border-red text-center">
            <h3 class="card-header text-red">
                <?php $Strings->get("clear log") ?>
            </h3>
            <div class="card-body">
                <p><i class="fas fa-exclamation-triangle fa-10x"></i></p>
                <h4><?php $Strings->get("really clear log") ?></h4>
            </div>
            <div class="card-footer d-flex">
                <a href="app.php?page=authlog" class="btn btn-primary mr-auto"><i class="fa fa-arrow-left"></i> <?php $Strings->get('cancel'); ?></a>
                <a href="action.php?action=clearlog&source=authlog" class="btn btn-danger"><i class="fa fa-times"></i> <?php $Strings->get('delete'); ?></a>
            </div>
        </div>
    </div>
</div>