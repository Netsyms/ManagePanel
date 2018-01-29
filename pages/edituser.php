<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';
require_once __DIR__ . "/../lib/login.php";
require_once __DIR__ . "/../lib/userinfo.php";

redirectifnotloggedin();

$userdata = [
    'uid' => '',
    'username' => '',
    'realname' => '',
    'email' => '',
    'authsecret' => '',
    'acctstatus' => '',
    'typecode' => 'LOCAL',
    'deleted' => 0
];

$editing = false;

if (!is_empty($VARS['id'])) {
    if ($database->has('accounts', ['uid' => $VARS['id']])) {
        $editing = true;
        $userdata = $database->select('accounts', ['[>]accttypes' => ['accttype' => 'typeid']], [
                    'uid',
                    'username',
                    'realname',
                    'email',
                    'authsecret',
                    'acctstatus',
                    'typecode',
                    'deleted'
                        ], [
                    'uid' => $VARS['id']
                ])[0];
    } else {
        // user id is invalid, redirect to a page that won't cause an error when pressing Save
        header('Location: app.php?page=edituser');
    }
}

if ($userdata['typecode'] != "LOCAL") {
    $localacct = false;
} else {
    $localacct = true;
}
?>

<form role="form" action="action.php" method="POST">
    <div class="card border-blue">
        <h3 class="card-header text-blue">
            <?php
            if ($editing) {
                ?>
                <i class="far fa-edit"></i> <?php lang2("editing user", ['user' => "<span id=\"name_title\">" . htmlspecialchars($userdata['realname']) . "</span>"]); ?>
                <?php
            } else {
                ?>
                <i class="far fa-edit"></i> <?php lang("adding user"); ?>
                <?php
            }
            ?>
        </h3>
        <div class="card-body">
            <?php
            if (!$localacct) {
                ?>
                <div class="alert alert-warning">
                    <?php lang("non-local account warning"); ?>
                </div>
                <?php
            }
            if ($userdata['deleted'] == 1) {
                ?>
                <div class="alert alert-info">
                    <?php lang("editing deleted account"); ?>
                </div>
                <?php
            }
            ?>
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> <?php lang("name"); ?></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="<?php lang("placeholder name"); ?>" required="required" value="<?php echo htmlspecialchars($userdata['realname']); ?>" />
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-id-badge"></i> <?php lang("username"); ?></label>
                        <input type="text" <?php if (!$localacct) echo "readonly=\"readonly\""; ?> class="form-control" name="username" id="username" placeholder="<?php lang("placeholder username"); ?>" required="required" value="<?php echo htmlspecialchars($userdata['username']); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> <?php lang("email"); ?></label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="<?php lang("placeholder email address"); ?>" value="<?php echo htmlspecialchars($userdata['email']); ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="pass"><i class="fas fa-lock"></i> <?php lang("new password"); ?></label>
                        <input type="text" <?php if (!$localacct) echo "readonly=\"readonly\""; ?> autocomplete="new-password" class="form-control" name="pass" id="pass" placeholder="<?php lang("placeholder password"); ?>" />
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="status"><i class="fas fa-check-circle"></i> <?php lang("status"); ?></label>
                        <select class="form-control" name="status" id="status" required="required">
                            <?php
                            $statuses = $database->select('acctstatus', ['statusid (id)', 'statuscode (code)'], ["ORDER" => "statusid"]);
                            foreach ($statuses as $s) {
                                echo "<option";
                                if ($s['id'] == $userdata['acctstatus']) {
                                    echo " selected";
                                }
                                echo " value=\"" . $s['id'] . "\">" . $s['code'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <input type="hidden" name="id" value="<?php echo htmlspecialchars($VARS['id']); ?>" />
        <input type="hidden" name="action" value="edituser" />
        <input type="hidden" name="source" value="users" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                if (!is_empty($userdata['authsecret'])) {
                    ?>
                    <a href="action.php?action=rmtotp&source=users&id=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-unlock"></i> <?php lang('remove 2fa'); ?></a> &nbsp; &nbsp;
                    <?php
                }
                ?>
                <a href="app.php?page=deluser&id=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger"><i class="fas fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>