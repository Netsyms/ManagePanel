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
<div class="card border-brown">
    <?php if ($permissions !== false) { ?>
        <form role="form" action="action.php" method="POST">
        <?php } ?>
        <h4 class="card-header text-brown"><?php lang("select a user to view or edit permissions"); ?></h4>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="user-box"><i class="fas fa-id-card"></i> <?php lang("user"); ?></label><br />
                        <div class="input-group">
                            <input type="text"<?php if ($permissions === false) { ?>id="user-box"<?php } ?> class="form-control" value="<?php echo $user ?>" name="user" placeholder="<?php lang("type to select a user"); ?>" <?php
                            if ($permissions !== false) {
                                echo "readonly";
                            }
                            ?>/>
                            <div class="input-group-append">
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
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="perms-box"><i class="fas fa-key"></i> <?php lang("permissions"); ?></label><br />
                            <div class="input-group">
                                <?php
                                if ($database->count('permissions') > 30) {
                                    ?>
                                    <input type="text" id="perms-box" class="form-control" placeholder="<?php lang("type to add a permission") ?>" />
                                    <?php
                                } else {
                                    ?>
                                    <select id="perms-box" class="form-control">
                                        <option><?php lang("Choose a permission") ?></option>
                                        <?php
                                        $allpermissions = $database->select('permissions', ['permid', 'permcode', 'perminfo']);
                                        foreach ($allpermissions as $p) {
                                            if (!in_array($p, $perms)) {
                                                echo "<option value=\"$p[permcode]\">$p[permcode]: $p[perminfo]</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <?php
                                }
                                ?>
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" id="addpermbtn"><i class="fa fa-plus"></i> <?php lang("add") ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="card" id="permslist-panel">
                            <div class="list-group" id="permslist">
                                <?php
                                foreach ($perms as $perm) {
                                    ?>
                                    <div class="list-group-item" data-permcode="<?php echo $perm['permcode']; ?>">
                                        <?php echo $perm['permcode']; ?> <div class="btn btn-danger btn-sm float-right rmperm"><i class="fas fa-trash"></i></div><input type="hidden" name="permissions[]" value="<?php echo $perm['permcode']; ?>" />
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
            <div class="card-footer d-flex">
                <button type="submit" class="btn btn-success ml-auto" id="save-btn"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            </div>
        </form>
    <?php } ?>
</div>