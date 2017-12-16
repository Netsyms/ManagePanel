<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

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
    
    <button type="submit" class="btn btn-success" id="genrptbtn"><i class="fa fa-download"></i> <?php lang("generate report"); ?></button>
</form>