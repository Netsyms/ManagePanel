<?php
require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

if (is_empty($VARS['mid']) || is_empty($VARS['eid'])) {
    header('Location: app.php?page=managers&msg=user_not_exists');
    die();
}
if (!$database->has('managers', ['managerid' => $VARS['mid']])) {
    header('Location: app.php?page=managers&msg=user_not_exists');
    die();
}
if (!$database->has('managers', ['employeeid' => $VARS['eid']])) {
    header('Location: app.php?page=managers&msg=user_not_exists');
    die();
}
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php lang("delete relationship") ?>
                </h3>
            </div>
            <div class="panel-body">
                <div style="text-align: center;">
                    <p><i class="fa fa-exclamation-triangle fa-5x"></i></p>
                    <h4><?php lang("really delete relationship") ?></h4>
                    <?php
                    $data = $database->select('managers', [
                                "[>]accounts (manager)" => ['managerid' => 'uid'],
                                "[>]accounts (employee)" => ['employeeid' => 'uid']
                                    ], [
                                'manager.username (manageruser)',
                                'employee.username (employeeuser)',
                                'manager.realname (managername)',
                                'employee.realname (employeename)'
                                    ], ['AND' => ['managerid' => $VARS['mid'], 'employeeid' => $VARS['eid']]])[0];
                    ?>
                    <div class="list-group">
                        <div class="list-group-item">
                            <i class="fa fa-fw fa-id-card-o"></i> <?php echo $data['managername']; ?> (<?php echo $data['manageruser']; ?>)
                        </div>
                        <div class="list-group-item">
                            <i class="fa fa-fw fa-user"></i> <?php echo $data['employeename']; ?> (<?php echo $data['employeeuser']; ?>)
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="action.php?action=delmanager&source=managers&mid=<?php echo htmlspecialchars($VARS['mid']); ?>&eid=<?php echo htmlspecialchars($VARS['eid']); ?>" class="btn btn-danger"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <a href="app.php?page=authlog" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> <?php lang('cancel'); ?></a>
            </div>
        </div>
    </div>
</div>