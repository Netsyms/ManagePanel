<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=clearlog" class="btn btn-warning"><i class="fa fa-times"></i> <?php lang("clear log"); ?></a>
</div>
<table id="logtable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fas fa-fw fa-calendar hidden-xs"></i> <?php lang('logtime'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-server hidden-xs"></i> <?php lang('logtype'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge hidden-xs"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-globe hidden-xs"></i> <?php lang('ip address'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-info-circle hidden-xs"></i> <?php lang('other data'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fas fa-fw fa-calendar hidden-xs"></i> <?php lang('logtime'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-server hidden-xs"></i> <?php lang('logtype'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge hidden-xs"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-globe hidden-xs"></i> <?php lang('ip address'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-info-circle hidden-xs"></i> <?php lang('other data'); ?></th>
    </tfoot>
</table>
<br />
<div class="row justify-content-center">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card">
            <h4 class="card-header">
                <?php lang('event type reference'); ?>
            </h4>
            <div class="list-group">
                <?php
                $types = $database->select('logtypes', 'typename');
                foreach ($types as $type) {
                    ?>
                    <div class="list-group-item">
                        <?php echo $type; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>