<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group">
    <a href="app.php?page=edituser" class="btn btn-success"><i class="fa fa-user-plus"></i> <?php $Strings->get("new user"); ?></a>
</div>
<table id="usertable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php $Strings->get('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge d-none d-md-inline"></i> <?php $Strings->get('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-envelope d-none d-md-inline"></i> <?php $Strings->get('email'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-lock d-none d-md-inline"></i> <?php $Strings->get('2fa'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-check-circle d-none d-md-inline"></i> <?php $Strings->get('status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $where = [];
        if (empty($_GET['show_deleted'])) {
            $where = [
                'deleted' => 0
            ];
        }
        $users = $database->select('accounts', [
            "[>]acctstatus" => ['acctstatus' => 'statusid'],
            "[>]accttypes" => ['accttype' => 'typeid']
                ], [
            'uid',
            'username',
            'realname',
            'email',
            'authsecret (2fa)',
            'acctstatus',
            'statuscode',
            'accttype',
            'typecode',
            'deleted'
                ], $where
        );

        foreach ($users as $user) {
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-primary btn-sm" href="app.php?page=edituser&id=<?php echo $user['uid']; ?>"><i class="far fa-edit"></i> <?php $Strings->get("Edit"); ?></a>
                </td>
                <?php if ($user['deleted']) { ?>
                    <td class="text-danger font-italic"><?php echo $user['realname']; ?></td>
                    <td class="text-danger font-italic"><?php echo $user['username']; ?></td>
                <?php } else { ?>
                    <td><?php echo $user['realname']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                <?php } ?>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['2fa'] == true ? "<i class='fas fa-check'></i>" : "<i class='fas fa-times'></i>"; ?></td>
                <td><?php echo $user['statuscode']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php $Strings->get('name'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-id-badge d-none d-md-inline"></i> <?php $Strings->get('username'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-envelope d-none d-md-inline"></i> <?php $Strings->get('email'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-lock d-none d-md-inline"></i> <?php $Strings->get('2fa'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-check-circle d-none d-md-inline"></i> <?php $Strings->get('status'); ?></th>
    </tfoot>
</table>