<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=edituser" class="btn btn-success"><i class="fa fa-user-plus"></i> <?php lang("new user"); ?></a>
</div>
<table id="usertable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge hidden-xs"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-envelope hidden-xs"></i> <?php lang('email'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-lock hidden-xs"></i> <?php lang('2fa'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-check-circle hidden-xs"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-server hidden-xs"></i> <?php lang('type'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge hidden-xs"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-envelope hidden-xs"></i> <?php lang('email'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-lock hidden-xs"></i> <?php lang('2fa'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-check-circle hidden-xs"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-server hidden-xs"></i> <?php lang('type'); ?></th>
    </tfoot>
</table>
<script nonce="<?php echo $SECURE_NONCE; ?>">
    /* Give JavaScript access to the lang string
     * it needs to inject the show deleted checkbox
     */
    var lang_show_deleted = "<?php lang("show deleted") ?>";
</script>