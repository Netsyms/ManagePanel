/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

function addPermission(permcode, permdesc) {
    permcode = permcode.trim().toUpperCase();
    if (permcode == "") {
        return false;
    }
    if ($("#permslist div[data-permcode=" + permcode + "]").length) {
        $("#permslist .list-group-item[data-permcode=" + permcode + "]").animate({
            backgroundColor: "#ff0000",
        }, 500, "linear", function () {
            $("#permslist .list-group-item[data-permcode=" + permcode + "]").animate({
                backgroundColor: "#ffffff",
            }, 500);
        });
        return false;
    }
    if (typeof permdesc == "undefined") {
        $.post("action.php", {
            action: 'autocomplete_permission',
            q: $("#perms-box").val()
        }, function (resp) {
            if (resp.length === 0) {
                return;
            }
            if (resp.length === 1) {
                permdesc = resp[0].info;
            } else {
                for (var i = 0; i < resp.length; i++) {
                    if (resp[i].name == permcode) {
                        permdesc = resp[i].info;
                        break;
                    }
                }
                if (typeof permdesc == "undefined") {
                    return;
                }
            }
            $('#permslist').append("<div class=\"list-group-item\" data-permcode=\"" + permcode + "\">" + permcode + "<div class=\"btn btn-danger btn-sm float-right rmperm\"><i class=\"fas fa-trash\"></i></div><input type=\"hidden\" name=\"permissions[]\" value=\"" + permcode + "\" /> <p class=\"small\">" + permdesc + "</p></div>");
            $("#perms-box").val("");
        }, "json");
    } else {
        $('#permslist').append("<div class=\"list-group-item\" data-permcode=\"" + permcode + "\">" + permcode + "<div class=\"btn btn-danger btn-sm float-right rmperm\"><i class=\"fas fa-trash\"></i></div><input type=\"hidden\" name=\"permissions[]\" value=\"" + permcode + "\" /> <p class=\"small\">" + permdesc + "</p></div>");
        $("#perms-box").val("");
    }
}

function removePermission(permcode) {
    $("#permslist div[data-permcode=" + permcode + "]").remove();
}

var options = {
    url: "action.php",
    ajaxSettings: {
        dataType: "json",
        method: "GET",
        data: {
            action: "autocomplete_user"
        }
    },
    preparePostData: function (data) {
        data.q = $("#user-box").val();
        return data;
    },
    getValue: function (element) {
        return element.username;
    },
    template: {
        type: "custom",
        method: function (value, item) {
            return item.name + " <i class=\"small\">" + item.username + "</i>";
        }
    },
    list: {
        onClickEvent: function () {
            var value = $("#user-box").getSelectedItemData().username;
            document.location.href = "app.php?page=permissions&user=" + value;
        }
    },
    requestDelay: 500,
    cssClasses: "form-control form-control-sm"
};

if ($("#user-box").get(0).tagName != "SELECT") {
    $("#user-box").easyAutocomplete(options);
}

$("#user-box").keyup(function (e) {
    if (e.keyCode == 13) {
        $("#selectuserbtn").click();
    }
});

$("#selectuserbtn").click(function () {
    document.location.href = "app.php?page=permissions&user=" + $("#user-box").val();
});

$("#perms-box").keyup(function (event) {
    if (event.keyCode == 13) {
        $("#addpermbtn").click();
        event.preventDefault();
        return false;
    }
});
$("#perms-box").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
});

$("#addpermbtn").click(function () {
    addPermission($("#perms-box").val());
});

$('#permslist').on("click", ".rmperm", function () {
    removePermission($(this).parent().data("permcode"));
});