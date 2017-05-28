<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group" style="margin-bottom: 10px;">
    <a href="app.php?page=clearlog" class="btn btn-warning"><i class="fa fa-times"></i> <?php lang("clear log"); ?></a>
</div>
<table id="logtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-calendar"></i> <?php lang('logtime'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-server"></i> <?php lang('logtype'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-globe"></i> <?php lang('ip address'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-info-circle"></i> <?php lang('other data'); ?></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><i class="fa fa-fw fa-calendar"></i> <?php lang('logtime'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-server"></i> <?php lang('logtype'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-id-badge"></i> <?php lang('username'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-globe"></i> <?php lang('ip address'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-info-circle"></i> <?php lang('other data'); ?></th>
    </tfoot>
</table>
<br />
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
        <div class="panel panel-blue">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?php lang('event type reference'); ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <?php
                    $types = $database->select('logtypes', 'typename');
                    foreach ($types as $type) {
                        ?>
                        <div class="list-group-item">
                            <?php echo $type; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>