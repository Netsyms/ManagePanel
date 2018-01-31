<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group">
    <a href="app.php?page=edituser" class="btn btn-success"><i class="fa fa-user-plus"></i> <?php lang("new user"); ?></a>
</div>
<table id="usertable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge d-none d-md-inline"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-envelope d-none d-md-inline"></i> <?php lang('email'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-lock d-none d-md-inline"></i> <?php lang('2fa'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-check-circle d-none d-md-inline"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-server d-none d-md-inline"></i> <?php lang('type'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge d-none d-md-inline"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-envelope d-none d-md-inline"></i> <?php lang('email'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-lock d-none d-md-inline"></i> <?php lang('2fa'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-check-circle d-none d-md-inline"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-server d-none d-md-inline"></i> <?php lang('type'); ?></th>
    </tfoot>
</table>
<script nonce="<?php echo $SECURE_NONCE; ?>">
    /* Give JavaScript access to the lang string
     * it needs to inject the show deleted checkbox
     */
    var lang_show_deleted = "<?php lang("show deleted") ?>";
</script>