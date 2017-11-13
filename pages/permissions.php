<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=addpermission" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("new permission"); ?></a>
</div>
<table id="permtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('user'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-key hidden-xs"></i> <?php lang('permission'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('user'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-key hidden-xs"></i> <?php lang('permission'); ?></th>
    </tfoot>
</table>