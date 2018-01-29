<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

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
<div class="row justify-content-center">
    <div class="col-12 col-sm-6 col-sm-offset-3">
        <div class="card border-red text-center">
            <h3 class="card-header text-red">
                <?php lang("delete user") ?>
            </h3>
            <div class="card-body">
                <p><i class="fas fa-exclamation-triangle fa-10x"></i></p>
                <h4><?php lang("really delete user") ?></h4>
                <div class="list-group">
                    <div class="list-group-item">
                        <i class="fas fa-fw fa-user"></i> <?php echo $userdata['realname']; ?>
                    </div>
                    <div class="list-group-item">
                        <i class="fas fa-fw fa-id-badge"></i> <?php echo $userdata['username']; ?>
                    </div>
                    <?php
                    if (!is_empty($userdata['email'])) {
                        ?>
                        <div class="list-group-item">
                            <i class="fas fa-fw fa-envelope"></i> <?php echo $userdata['email']; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="app.php?page=users" class="btn btn-primary mr-auto"><i class="fas fa-arrow-left"></i> <?php lang('cancel'); ?></a>
                <a href="action.php?action=deleteuser&source=users&id=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang('delete'); ?></a>
            </div>
        </div>
    </div>
</div>