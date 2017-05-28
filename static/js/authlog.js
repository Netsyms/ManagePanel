$('#logtable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-list fa-fw\"></i> " + data[1];
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