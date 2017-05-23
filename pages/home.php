<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-light-blue">
            <div class="panel-heading"><div class="panel-title"><?php lang("total users") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-users"></i> <?php echo $database->count('accounts'); ?></h1>
            </div>
            <div class="panel-footer">
                <a style="color: black;" href="app.php?page=users"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view users'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-light-green">
            <div class="panel-heading"><div class="panel-title"><?php lang("normal accounts") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-check"></i> <?php echo $database->count('accounts', ['acctstatus' => 1]); ?></h1>
            </div>
            <div class="panel-footer">
                <a style="color: black;" href="app.php?page=users"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view users'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-amber">
            <div class="panel-heading"><div class="panel-title"><?php lang("locked accounts") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-user-times"></i> <?php echo $database->count('accounts', ['OR' => ['acctstatus' => 2, 'acctstatus' => 3]]); ?></h1>
            </div>
            <div class="panel-footer">
                <a style="color: black;" href="app.php?page=users"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view users'); ?></a>
            </div>
        </div>
    </div>
</div>