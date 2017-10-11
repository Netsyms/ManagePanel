<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<form action="action.php" method="POST" target="_BLANK">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <label for="type"><?php lang("report type"); ?></label>
            <select name="type" class="form-control" required>
                <option selected><?php lang("choose an option") ?></option>
                <option value="users"><?php lang("users") ?></option>
                <option value="managers"><?php lang("managers") ?></option>
                <option value="permissions"><?php lang("permissions") ?></option>
                <option value="security"><?php lang("security log") ?></option>
            </select>
        </div>
        <div class="col-xs-12 col-sm-6">
            <label for="type"><?php lang("format"); ?></label>
            <select name="format" class="form-control" required>
                <option value="csv"><?php lang("csv file") ?></option>
                <option value="ods"><?php lang("ods file") ?></option>
            </select>
        </div>
    </div>
    <br />
    <input type="hidden" name="action" value="export" />
    <input type="hidden" name="source" value="export" />
    
    <button type="submit" class="btn btn-success"><i class="fa fa-download"></i> <?php lang("generate report"); ?></button>
</form>