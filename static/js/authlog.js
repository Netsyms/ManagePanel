/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

$('#logtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fas fa-list fa-fw\"></i> " + data[1];
                }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
            }),
            type: "column"
        }
    },
    columnDefs: [
        {
            targets: 0,
            className: 'control',
            orderable: false
        }
    ],
    order: [
        [1, 'desc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getlogtable.php",
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.log.forEach(function (row) {
                json.data.push([
                    "",
                    row.logtime,
                    row.typename,
                    (row.username == null ? "---" : row.username),
                    row.ip,
                    row.otherdata
                ]);
            });
            return JSON.stringify(json);
        }
    }
});