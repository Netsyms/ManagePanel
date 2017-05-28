<?php
if (!is_empty($VARS['id'])) {
    if ($database->has('accounts', ['uid' => $VARS['id']])) {
        $userdata = $database->select('accounts', ['[>]accttypes' => ['accttype' => 'typeid']], [
                    'uid',
                    'username',
                    'realname',
                    'email'
                        ], [
                    'uid' => $VARS['id']
                ])[0];
    } else {
        // user id is invalid
        header('Location: app.php?page=users&msg=user_not_exists');
        die();
    }
} else {
    // user id is invalid
    header('Location: app.php?page=users&msg=user_not_exists');
    die();
}
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php lang("delete user") ?>
                </h3>
            </div>
            <div class="panel-body">
                <div style="text-align: center;">
                    <p><i class="fa fa-exclamation-triangle fa-5x"></i></p>
                    <h4><?php lang("really delete user") ?></h4>
                </div>
                <div class="list-group">
                    <div class="list-group-item">
                        <i class="fa fa-fw fa-user"></i> <?php echo $userdata['realname']; ?>
                    </div>
                    <div class="list-group-item">
                        <i class="fa fa-fw fa-id-badge"></i> <?php echo $userdata['username']; ?>
                    </div>
                    <?php
                    if (!is_empty($userdata['email'])) {
                        ?>
                        <div class="list-group-item">
                            <i class="fa fa-fw fa-envelope"></i> <?php echo $userdata['email']; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="panel-footer">
                <a href="action.php?action=deleteuser&source=users&id=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <a href="app.php?page=users" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> <?php lang('cancel'); ?></a>
            </div>
        </div>
    </div>
</div>