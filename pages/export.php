<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<form action="lib/reports.php" method="GET" target="_BLANK">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <label for="type"><?php lang("report type"); ?></label>
            <select name="type" class="form-control" required>
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
                <option value="html"><?php lang("html file") ?></option>
            </select>
        </div>
    </div>
    <br />
    <?php
    $code = uniqid(rand(10000000,99999999), true);
    $database2->insert('report_access_codes', ['code' => $code, 'expires' => date("Y-m-d H:i:s", strtotime("+5 minutes"))]);
    ?>
    <input type="hidden" name="code" value="<?php echo $code; ?>" />
    
    <button type="submit" class="btn btn-success" onclick="setTimeout(function () {window.location.reload();}, 1000)"><i class="fa fa-download"></i> <?php lang("generate report"); ?></button>
</form>