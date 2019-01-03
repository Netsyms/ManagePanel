<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/../required.php";

redirectifnotloggedin();

if (!empty($VARS['id']) && preg_match("/[0-9]+/", $VARS['id'])) {
    $user = new User($VARS['id']);
    if (!$user->exists()) {
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
                <?php $Strings->get("delete user") ?>
            </h3>
            <div class="card-body">
                <p><i class="fas fa-exclamation-triangle fa-10x"></i></p>
                <h4><?php $Strings->get("really delete user") ?></h4>
                <div class="list-group">
                    <div class="list-group-item">
                        <i class="fas fa-fw fa-user"></i> <?php echo $user->getName(); ?>
                    </div>
                    <div class="list-group-item">
                        <i class="fas fa-fw fa-id-badge"></i> <?php echo $user->getUsername(); ?>
                    </div>
                    <?php
                    if (!empty($user->getEmail())) {
                        ?>
                        <div class="list-group-item">
                            <i class="fas fa-fw fa-envelope"></i> <?php echo $user->getEmail(); ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="card-footer d-flex">
                <a href="app.php?page=users" class="btn btn-primary mr-auto"><i class="fas fa-arrow-left"></i> <?php $Strings->get('cancel'); ?></a>
                <a href="action.php?action=deleteuser&source=users&id=<?php echo $user->getUID(); ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php $Strings->get('delete'); ?></a>
            </div>
        </div>
    </div>
</div>