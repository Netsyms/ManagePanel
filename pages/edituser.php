<?php
require_once __DIR__ . '/../required.php';
require_once __DIR__ . "/../lib/login.php";
require_once __DIR__ . "/../lib/userinfo.php";

redirectifnotloggedin();

$userdata = [
    'uid' => '',
    'username' => '',
    'realname' => '',
    'email' => '',
    'acctstatus' => '',
    'typecode' => 'LOCAL'
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
                    'acctstatus',
                    'typecode'
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
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($editing) {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang2("editing user", ['user' => "<span id=\"name_title\">" . htmlspecialchars($userdata['realname']) . "</span>"]); ?>
                    <?php
                } else {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang("adding user"); ?>
                    <?php
                }
                ?>
            </h3>
        </div>
        <div class="panel-body">
            <?php
            if (!$localacct) {
                ?>
                <div class="alert alert-warning">
                    <?php lang("non-local account warning"); ?>
                </div>
                <?php
            }
            ?>
            <div class="form-group">
                <label for="name"><i class="fa fa-user"></i> <?php lang("name"); ?></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="<?php lang("placeholder name"); ?>" required="required" value="<?php echo htmlspecialchars($userdata['realname']); ?>" />
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="username"><i class="fa fa-id-badge"></i> <?php lang("username"); ?></label>
                        <input type="text" <?php if (!$localacct) echo "disabled"; ?> class="form-control" name="username" id="username" placeholder="<?php lang("placeholder username"); ?>" required="required" value="<?php echo htmlspecialchars($userdata['username']); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="email"><i class="fa fa-envelope"></i> <?php lang("email"); ?></label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="<?php lang("placeholder email address"); ?>" value="<?php echo htmlspecialchars($userdata['email']); ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="pass"><i class="fa fa-lock"></i> <?php lang("new password"); ?></label>
                        <input type="text" <?php if (!$localacct) echo "disabled"; ?> autocomplete="new-password" class="form-control" name="pass" id="pass" placeholder="<?php lang("placeholder password"); ?>" />
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="status"><i class="fa fa-check-circle"></i> <?php lang("status"); ?></label>
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

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deleteuser&source=users&userid=<?php echo htmlspecialchars($VARS['id']); ?>" style="margin-top: 8px;" class="btn btn-danger btn-xs pull-right"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>