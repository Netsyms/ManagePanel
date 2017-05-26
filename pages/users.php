<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group" style="margin-bottom: 10px;">
    <a href="app.php?page=edituser" class="btn btn-success"><i class="fa fa-user-plus"></i> <?php lang("new user"); ?></a>
</div>
<table id="usertable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-envelope"></i> <?php lang('email'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-check-circle"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-server"></i> <?php lang('type'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $users = $database->select('accounts', [
            "[>]acctstatus" => ['acctstatus' => 'statusid'],
            "[>]accttypes" => ['accttype' => 'typeid']
        ], [
            'uid',
            'username',
            'realname',
            'email',
            'acctstatus',
            'statuscode',
            'accttype',
            'typecode'
        ]);
        foreach ($users as $u) {
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-blue btn-xs" href="app.php?page=edituser&id=<?php echo $u['uid']; ?>"><i class="fa fa-pencil-square-o"></i> <?php lang("edit"); ?></a>
                </td>
                <td><?php echo $u['realname']; ?></td>
                <td><?php echo $u['username']; ?></td>
                <td><?php echo ($u['email'] == "NOEMAIL@EXAMPLE.COM" ? "" : $u['email']); ?></td>
                <td><?php echo $u['statuscode']; ?></td>
                <td><?php echo $u['typecode']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user"></i> <?php lang('name'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-envelope"></i> <?php lang('email'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-check-circle"></i> <?php lang('status'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-server"></i> <?php lang('type'); ?></th>
    </tfoot>
</table>