<?php
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

<form role="form" action="action.php" method="POST">
    <div class="alert alert-brown"><?php lang("select a manager to view or edit employees"); ?></div>
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="form-group">
                <label for="manager-box"><i class="fa fa-id-card-o"></i> <?php lang("manager"); ?></label><br />
                <input type="text" id="manager-box" class="form-control" value="<?php echo $user ?>" name="manager" placeholder="<?php lang("type to select a manager"); ?>" />
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
                <div class="panel" style="max-height: 700px; overflow-y: scroll;">
                    <div class="list-group" id="peoplelist">
                        <?php
                        foreach ($assigned as $user) {
                            ?>
                            <div class="list-group-item" data-user="<?php echo $user; ?>">
                                <?php echo $user; ?> <div onclick="removePerson('<?php echo $user; ?>')" class="btn btn-danger btn-sm pull-right"><i class="fa fa-trash-o"></i></div><input type="hidden" name="employees[]" value="<?php echo $user; ?>" />
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
<?php } ?>
</form>