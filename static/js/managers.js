$('#managertable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-id-card-o fa-fw\"></i> " + data[2];
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
        },
        {
            targets: 1,
            orderable: false
        }
    ],
    order: [
        [2, 'asc']
    ],
    serverSide: true,
    ajax: {
        url: "lib/getmanagetable.php",
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.managers.forEach(function (row) {
                json.data.push([
                    "",
                    row.delbtn,
                    row.managername + " (" + row.manageruser + ")",
                    row.employeename + " (" + row.employeeuser + ")"
                ]);
            });
            return JSON.stringify(json);
        }
    }
});