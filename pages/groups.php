<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


$groupselected = false;
$user = "";
$users = [];
if ($VARS['gid'] && $database->has('groups', ['groupid' => $VARS['gid']])) {
    $gid = $VARS['gid'];
    $users = $database->select('assigned_groups', ["[>]accounts" => ["uid" => "uid"]], 'username', ['groupid' => $gid]);
    $groupselected = true;
}
?>
<div class="row">
    <div class="col-12 col-xl-6">
        <div class="card border-brown">
            <h4 class="card-header text-brown">
                <i class="fas fa-object-group"></i> <?php lang("group management"); ?>
            </h4>
            <div class="card-body">
                <div class="row">
                    <form role="form" action="action.php" method="POST" class="col-12 col-sm-6">
                        <label for="addgroupbox"><i class="fas fa-plus"></i> <?php lang("new group"); ?></label>
                        <div class="input-group">
                            <input type="text" name="group" placeholder="<?php lang("enter group name"); ?>" class="form-control" />
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> <?php lang("add"); ?></button>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="addgroup" />
                        <input type="hidden" name="source" value="groups" />
                    </form>

                    <form role="form" action="action.php" method="POST" class="col-12 col-sm-6">
                        <label for="addgroupbox"><i class="fas fa-trash"></i> <?php lang("delete group"); ?></label>
                        <div class="input-group">
                            <select name="gid" class="form-control">
                                <?php
                                $groups = $database->select('groups', ['groupid (id)', 'groupname (name)']);
                                foreach ($groups as $g) {
                                    echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang("delete"); ?></button>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="rmgroup" />
                        <input type="hidden" name="source" value="groups" />
                    </form>
                </div>
            </div>
        </div>
        <br />
    </div>

    <div class="col-12 col-xl-6">
        <div class="card border-brown">
            <h4 class="card-header text-brown">
                <i class="fas fa-users"></i> <?php lang("group assignments"); ?>
            </h4>
            <?php if ($groupselected !== false) { ?>
                <form role="form" action="action.php" method="POST">
                <?php } ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="group-box"><i class="fas fa-object-group"></i> <?php lang("group"); ?></label>
                                <div class="input-group">
                                    <select <?php if ($groupselected === false) { ?>id="group-box"<?php } ?> class="form-control" value="<?php echo $gid ?>" name="gid" <?php echo ($groupselected !== false ? "readonly" : ""); ?>>
                                        <?php
                                        $groups = $database->select('groups', ['groupid (id)', 'groupname (name)']);
                                        foreach ($groups as $g) {
                                            if ($groupselected && $g['id'] == $gid) {
                                                echo '<option value="' . $g['id'] . '" selected>' . $g['name'] . '</option>';
                                            } else {
                                                echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <?php if ($groupselected === false) { ?>
                                            <button class="btn btn-default" type="button" id="selectgroupbtn"><i class="fas fa-chevron-right"></i> <?php lang("next") ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($groupselected !== false) {
                            ?>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="people-box"><i class="fas fa-users"></i> <?php lang("users"); ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="people-box" placeholder="<?php lang("type to add a person") ?>" />
                                        <div class="input-group-append">
                                            <button class="btn btn-default" type="button" id="addpersonbtn"><i class="fas fa-plus"></i> <?php lang("add") ?></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card" id="peoplelist-panel">
                                    <div class="list-group" id="peoplelist">
                                        <?php
                                        foreach ($users as $user) {
                                            ?>
                                            <div class="list-group-item" data-user="<?php echo $user; ?>">
                                                <?php echo $user; ?> <div class="btn btn-danger btn-sm float-right rmperson"><i class="fas fa-trash"></i></div><input type="hidden" name="users[]" value="<?php echo $user; ?>" />
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

                    <input type="hidden" name="action" value="assigngroup" />
                    <input type="hidden" name="source" value="groups" />
                </div>
                <?php if ($groupselected !== false) { ?>
                    <div class="card-footer d-flex">
                        <button type="submit" class="btn btn-success ml-auto" id="save-btn"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>