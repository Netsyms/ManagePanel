<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<form role="form" action="action.php" method="POST">
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-plus"></i> <?php lang("adding relationship"); ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="manager"><i class="fa fa-id-card-o"></i> <?php lang("manager"); ?></label>
                        <input type="text" class="form-control" name="manager" id="manager" required="required" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="employee"><i class="fa fa-user"></i> <?php lang("employee"); ?></label>
                        <input type="text" class="form-control" name="employee" id="employee" required="required" />
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="action" value="addmanager" />
        <input type="hidden" name="source" value="managers" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
        </div>
    </div>
</form>