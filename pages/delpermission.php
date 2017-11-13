<?php
require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

if (is_empty($VARS['uid'])) {
    header('Location: app.php?page=permissions&msg=user_not_exists');
    die();
}
if (!$database->has('permissions', ['permid' => $VARS['pid']])) {
    header('Location: app.php?page=permissions&msg=permission_not_exists');
    die();
}
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php lang("delete permission") ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="center-text">
                    <p><i class="fa fa-exclamation-triangle fa-5x"></i></p>
                    <h4><?php lang("really delete permission") ?></h4>
                    <?php
                    $data = $database->select('assigned_permissions', [
                                "[>]accounts" => ['uid' => 'uid'],
                                "[>]permissions" => ['permid' => 'permid']
                                    ], [
                                'username',
                                'realname',
                                'permcode',
                                'perminfo'
                                    ], ["AND" => ['assigned_permissions.permid' => $VARS['pid'], 'assigned_permissions.uid' => $VARS['uid']]])[0];
                    ?>
                    <div class="list-group">
                        <div class="list-group-item">
                            <i class="fa fa-fw fa-user"></i> <?php echo $data['realname']; ?> (<?php echo $data['username']; ?>)
                        </div>
                        <div class="list-group-item">
                            <i class="fa fa-fw fa-key"></i> <?php echo $data['permcode']; ?> (<?php echo $data['perminfo']; ?>)
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="action.php?action=delpermission&source=permissions&uid=<?php echo htmlspecialchars($VARS['uid']); ?>&pid=<?php echo htmlspecialchars($VARS['pid']); ?>" class="btn btn-danger"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <a href="app.php?page=permissions" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> <?php lang('cancel'); ?></a>
            </div>
        </div>
    </div>
</div>