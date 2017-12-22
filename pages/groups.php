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
<div class="panel panel-brown">
    <div class="panel-heading">
        <i class="fa fa-object-group"></i> <?php lang("group management"); ?>
    </div>
    <div class="row panel-body">
        <form role="form" action="action.php" method="POST" class="col-xs-12 col-sm-6">
            <label for="addgroupbox"><i class="fa fa-plus"></i> <?php lang("new group"); ?></label>
            <div class="input-group">
                <input type="text" name="group" placeholder="<?php lang("enter group name"); ?>" class="form-control" />
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("add"); ?></button>
                </div>
            </div>
            <input type="hidden" name="action" value="addgroup" />
            <input type="hidden" name="source" value="groups" />
        </form>

        <form role="form" action="action.php" method="POST" class="col-xs-12 col-sm-6">
            <label for="addgroupbox"><i class="fa fa-trash-o"></i> <?php lang("delete group"); ?></label>
            <div class="input-group">
                <select name="gid" class="form-control">
                    <?php
                    $groups = $database->select('groups', ['groupid (id)', 'groupname (name)']);
                    foreach ($groups as $g) {
                        echo '<option value="' . $g['id'] . '">' . $g['name'] . '</option>';
                    }
                    ?>
                </select>
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> <?php lang("delete"); ?></button>
                </div>
            </div>
            <input type="hidden" name="action" value="rmgroup" />
            <input type="hidden" name="source" value="groups" />
        </form>
    </div>
</div>
<hr />
<div class="panel panel-brown">
    <div class="panel-heading">
        <i class="fa fa-users"></i> <?php lang("group assignments"); ?>
    </div>
    <div class="panel-body">
        <?php if ($groupselected !== false) { ?>
            <form role="form" action="action.php" method="POST">
            <?php } ?>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="group-box"><i class="fa fa-object-group"></i> <?php lang("group"); ?></label><br />
                        <div class="row">
                            <div class="col-xs-8 col-sm-10 col-md-9 col-lg-10">
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
                            </div>
                            <div class="col-xs-4 col-sm-2 col-md-3 col-lg-2">
                                <?php if ($groupselected === false) { ?>
                                    <button class="btn btn-default" type="button" id="selectgroupbtn"><i class="fa fa-chevron-right"></i> <?php lang("next") ?></button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($groupselected !== false) {
                    ?>
                    <div class="col-xs-12 col-md-6">
                        <label for="people-box"><i class="fa fa-users"></i> <?php lang("users"); ?></label><br />
                        <div class="row">
                            <div class="col-xs-8 col-sm-10 col-md-9 col-lg-10">
                                <input type="text" id="people-box" class="form-control" placeholder="<?php lang("type to add a person") ?>" />
                            </div>
                            <div class="col-xs-4 col-sm-2 col-md-3 col-lg-2">
                                <button class="btn btn-default" type="button" id="addpersonbtn"><i class="fa fa-plus"></i> <?php lang("add") ?></button>
                            </div>
                        </div>
                        <div class="panel" id="peoplelist-panel">
                            <div class="list-group" id="peoplelist">
                                <?php
                                foreach ($users as $user) {
                                    ?>
                                    <div class="list-group-item" data-user="<?php echo $user; ?>">
                                        <?php echo $user; ?> <div class="btn btn-danger btn-sm pull-right rmperson"><i class="fa fa-trash-o"></i></div><input type="hidden" name="users[]" value="<?php echo $user; ?>" />
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

            <?php if ($groupselected !== false) { ?>
                <button type="submit" class="btn btn-success pull-right" id="save-btn"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            </form>
        <?php } ?>
    </div>
</div>