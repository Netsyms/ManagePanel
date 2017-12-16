<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


$perms = [];
$permissions = false;
$user = "";
if ($VARS['user'] && $database->has('accounts', ['username' => $VARS['user']])) {
    $user = $VARS['user'];
    require_once __DIR__ . "/../lib/userinfo.php";
    $uid = getUserByUsername($user)['uid'];
    $perms = $database->select('assigned_permissions', ["[>]permissions" => ["permid" => "permid"]], ['permissions.permid', 'permcode', 'perminfo'], ['uid' => $uid]);
    $permissions = true;
}
?>

<?php if ($permissions !== false) { ?>
    <form role="form" action="action.php" method="POST">
    <?php } ?>
    <div class="alert alert-brown"><?php lang("select a user to view or edit permissions"); ?></div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="user-box"><i class="fa fa-id-card-o"></i> <?php lang("user"); ?></label><br />
                <div class="row">
                    <div class="col-xs-8 col-sm-10 col-md-9 col-lg-10">
                        <input type="text"<?php if ($permissions === false) { ?>id="user-box"<?php } ?> class="form-control" value="<?php echo $user ?>" name="user" placeholder="<?php lang("type to select a user"); ?>" <?php if ($permissions !== false) { echo "readonly"; }?>/>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-3 col-lg-2">
                        <?php if ($permissions === false) { ?>
                            <button class="btn btn-default" type="button" id="selectuserbtn"><i class="fa fa-chevron-right"></i> <?php lang("next") ?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($permissions !== false) {
            ?>
            <div class="col-xs-12 col-md-6">
                <label for="perms-box"><i class="fa fa-user"></i> <?php lang("permissions"); ?></label><br />
                <div class="row">
                    <div class="col-xs-8 col-sm-10 col-md-9 col-lg-10">
                        <input type="text" id="perms-box" class="form-control" placeholder="<?php lang("type to add a permission") ?>" />
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-3 col-lg-2">
                        <button class="btn btn-default" type="button" id="addpermbtn"><i class="fa fa-plus"></i> <?php lang("add") ?></button>
                    </div>
                </div>
                <div class="panel" id="permslist-panel">
                    <div class="list-group" id="permslist">
                        <?php
                        foreach ($perms as $perm) {
                            ?>
                            <div class="list-group-item" data-permcode="<?php echo $perm['permcode']; ?>">
                                <?php echo $perm['permcode']; ?> <div class="btn btn-danger btn-sm pull-right rmperm"><i class="fa fa-trash-o"></i></div><input type="hidden" name="permissions[]" value="<?php echo $perm['permcode']; ?>" />
                                <p class="small"><?php echo $perm['perminfo']; ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<input type="hidden" name="action" value="editperms" />
<input type="hidden" name="source" value="permissions" />

<?php if ($permissions !== false) { ?>
    <button type="submit" class="btn btn-success pull-right" id="save-btn"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
    </form>
<?php } ?>