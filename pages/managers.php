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

<div class="card border-brown">
    <?php if ($employees !== false) { ?>
        <form role="form" action="action.php" method="POST">
        <?php } ?>
        <h4 class="card-header text-brown"><?php lang("select a manager to view or edit employees"); ?></h4>
        <div class="card-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="manager-box"><i class="fas fa-id-card"></i> <?php lang("manager"); ?></label><br />
                        <div class="input-group">
                            <input type="text"<?php if ($employees === false) { ?> id="manager-box"<?php } ?> class="form-control" value="<?php echo $user ?>" name="manager" placeholder="<?php lang("type to select a manager"); ?>" <?php
                            if ($employees !== false) {
                                echo "readonly";
                            }
                            ?>/>
                            <div class="input-group-append">
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
                        <div class="form-group">
                            <label for="people-box"><i class="fa fa-user"></i> <?php lang("employees"); ?></label><br />
                            <div class="input-group">
                                <input type="text" id="people-box" class="form-control" placeholder="<?php lang("type to add a person") ?>" />
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" id="addpersonbtn"><i class="fa fa-plus"></i> <?php lang("add") ?></button>
                                </div>
                            </div>
                        </div>

                        <div class="card" id="peoplelist-panel">
                            <div class="list-group" id="peoplelist">
                                <?php
                                foreach ($assigned as $user) {
                                    ?>
                                    <div class="list-group-item" data-user="<?php echo $user; ?>">
                                        <?php echo $user; ?> <div class="btn btn-danger btn-sm float-right rmperson"><i class="fas fa-trash"></i></div><input type="hidden" name="employees[]" value="<?php echo $user; ?>" />
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
            <div class="card-footer d-flex">
                <button type="submit" class="btn btn-success ml-auto" id="save-btn"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            </div>
        </form>
    <?php } ?>
</div>