<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group" style="margin-bottom: 10px;">
    <a href="app.php?page=addmanager" class="btn btn-success"><i class="fa fa-user-plus"></i> <?php lang("new relationship"); ?></a>
</div>
<table id="managertable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-id-card-o"></i> <?php lang('manager'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user"></i> <?php lang('employee'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        /*$managers = $database->select('managers', [
            "[>]accounts (manager)" => ['managerid' => 'uid'],
            "[>]accounts (employee)" => ['employeeid' => 'uid']
        ], [
            'managerid',
            'employeeid',
            'manager.username (manageruser)',
            'employee.username (employeeuser)',
            'manager.realname (managername)',
            'employee.realname (employeename)',
        ]);
        foreach ($managers as $m) {
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-danger btn-xs" href="app.php?page=deletemanager&mid=<?php echo $m['managerid']; ?>&eid=<?php echo $m['employeeid']; ?>"><i class="fa fa-trash"></i> <?php lang("delete"); ?></a>
                </td>
                <td><?php echo $m['managername']; ?> (<?php echo $m['manageruser']; ?>)</td>
                <td><?php echo $m['employeename']; ?> (<?php echo $m['employeeuser']; ?>)</td>
            </tr>
            <?php
        }*/
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-id-card-o"></i> <?php lang('manager'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-user"></i> <?php lang('employee'); ?></th>
    </tfoot>
</table>