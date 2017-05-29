<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<form role="form" action="action.php" method="POST">
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-plus"></i> <?php lang("adding permission"); ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="user"><i class="fa fa-id-card-o"></i> <?php lang("user"); ?></label>
                        <input type="text" class="form-control" name="user" id="user" required="required" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="perm"><i class="fa fa-user"></i> <?php lang("permission"); ?></label>
                        <input type="text" class="form-control" name="perm" id="perm" required="required" />
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="action" value="addpermission" />
        <input type="hidden" name="source" value="permissions" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
        </div>
    </div>
</form>