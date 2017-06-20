$('#usertable').DataTable({
    responsive: {
        details: {
            display: $.fn.dataTable.Responsive.display.modal({
                header: function (row) {
                    var data = row.data();
                    return "<i class=\"fa fa-user fa-fw\"></i> " + data[2];
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
        url: "lib/getusertable.php",
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.users.forEach(function (row) {
                json.data.push([
                    "",
                    row.editbtn,
                    row.realname,
                    row.username,
                    row.email,
                    (row['2fa'] == true ? "<i class='fa fa-check'></i>" : "<i class='fa fa-times'></i>"),
                    row.statuscode,
                    row.typecode
                ]);
            });
            return JSON.stringify(json);
        }
    }
});