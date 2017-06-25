var usertable = $('#usertable').DataTable({
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
        data: function (d) {
            if ($('#show_deleted_checkbox').is(':checked')) {
                d.show_deleted = 1;
            }
        },
        dataFilter: function (data) {
            var json = jQuery.parseJSON(data);
            json.data = [];
            json.users.forEach(function (row) {
                json.data.push([
                    "",
                    row.editbtn,
                    (row.deleted == 1 ? "<del style=\"color: red;\">" : "") + row.realname + (row.deleted == 1 ? "</del>" : ""),
                    (row.deleted == 1 ? "<span style=\"color: red;\">" : "") + row.username + (row.deleted == 1 ? "</span>" : ""),
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

$('#usertable_filter').append("<div class=\"checkbox\" style=\"display: inline-block\"><label><input type=\"checkbox\" id=\"show_deleted_checkbox\" onclick=\"usertable.ajax.reload()\"> " + lang_show_deleted + "</label></div>");