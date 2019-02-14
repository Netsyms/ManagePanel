<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=addapikey" class="btn btn-primary"><i class="fas fa-plus"></i> <?php $Strings->get("Add Key"); ?></a>
</div>
<table id="apikeytable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-key d-none d-md-inline"></i> <?php $Strings->get('Key'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-list d-none d-md-inline"></i> <?php $Strings->get('Type'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-sticky-note d-none d-md-inline"></i> <?php $Strings->get('Notes'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $keys = $database->select("apikeys", ["key", "notes", "type"]);
        foreach ($keys as $key) {
            if ($SETTINGS['hide_api_key_in_use'] && $key['key'] == $SETTINGS['accounthub']['key']) {
                continue;
            }
        ?>
        <tr>
            <td></td>
            <td><form action="action.php" method="POST">
                    <input type="hidden" name="action" value="revokeapikey" />
                    <input type="hidden" name="source" value="apikeys" />
                    <input type="hidden" name="key" value="<?php echo $key['key']; ?>" />
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> <?php $Strings->get("Revoke"); ?></button>
                </form></td>
            <td><?php echo $key['key']; ?></td>
            <td><?php echo $key['type']; ?></td>
            <td><?php echo htmlentities($key['notes']); ?></td>
        </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-key d-none d-md-inline"></i> <?php $Strings->get('Key'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-list d-none d-md-inline"></i> <?php $Strings->get('Type'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-sticky-note d-none d-md-inline"></i> <?php $Strings->get('Notes'); ?></th>
    </tfoot>
</table>