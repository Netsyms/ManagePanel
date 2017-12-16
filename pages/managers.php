<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();


$assigned = [];
$employees = false;
$user = "";
if ($VARS['man'] && $database->has('accounts', ['username' => $VARS['man']])) {
    $user = $VARS['man'];
    require_once __DIR__ . "/../lib/userinfo.php";
    $uid = getUserByUsername($user)['uid'];
    $assigned = $database->select('managers', ["[>]accounts" => ["employeeid" => "uid"]], 'username', ['managerid' => $uid]);
    $employees = true;
}
?>

<?php if ($employees !== false) { ?>
    <form role="form" action="action.php" method="POST">
    <?php } ?>
    <div class="alert alert-brown"><?php lang("select a manager to view or edit employees"); ?></div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="manager-box"><i class="fa fa-id-card-o"></i> <?php lang("manager"); ?></label><br />
                <div class="row">
                    <div class="col-xs-8 col-sm-10 col-md-9 col-lg-10">
                        <input type="text"<?php if ($employees === false) { ?>id="manager-box"<?php } ?> class="form-control" value="<?php echo $user ?>" name="manager" placeholder="<?php lang("type to select a manager"); ?>" <?php if ($employees !== false) { echo "readonly"; }?>/>
                    </div>
                    <div class="col-xs-4 col-sm-2 col-md-3 col-lg-2">
                        <?php if ($employees === false) { ?>
                            <button class="btn btn-default" type="button" id="selectmanagerbtn"><i class="fa fa-chevron-right"></i> <?php lang("next") ?></button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($employees !== false) {
            ?>
            <div class="col-xs-12 col-md-6">
                <label for="people-box"><i class="fa fa-user"></i> <?php lang("employees"); ?></label><br />
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
                        foreach ($assigned as $user) {
                            ?>
                            <div class="list-group-item" data-user="<?php echo $user; ?>">
                                <?php echo $user; ?> <div class="btn btn-danger btn-sm pull-right rmperson"><i class="fa fa-trash-o"></i></div><input type="hidden" name="employees[]" value="<?php echo $user; ?>" />
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

<input type="hidden" name="action" value="editmanager" />
<input type="hidden" name="source" value="managers" />

<?php if ($employees !== false) { ?>
    <button type="submit" class="btn btn-success pull-right" id="save-btn"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
    </form>
<?php } ?>