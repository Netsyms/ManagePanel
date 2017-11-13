<?php
require_once __DIR__ . "/../required.php";

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php lang("clear log") ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="center-text">
                    <p><i class="fa fa-exclamation-triangle fa-5x"></i></p>
                    <h4><?php lang("really clear log") ?></h4>
                </div>
            </div>
            <div class="panel-footer">
                <a href="action.php?action=clearlog&source=authlog" class="btn btn-danger"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <a href="app.php?page=authlog" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> <?php lang('cancel'); ?></a>
            </div>
        </div>
    </div>
</div>