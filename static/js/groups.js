/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

function addPerson(p) {
    p = p.trim();
    if (p == "") {
        return false;
    }
    if ($("#peoplelist div[data-user=" + p + "]").length) {
        $("#peoplelist .list-group-item[data-user=" + p + "]").animate({
            backgroundColor: "#ff0000",
        }, 500, "linear", function () {
            $("#peoplelist .list-group-item[data-user=" + p + "]").animate({
                backgroundColor: "#ffffff",
            }, 500);
        });
        return false;
    }
    $('#peoplelist').append("<div class=\"list-group-item\" data-user=\"" + p + "\">" + p + "<div class=\"btn btn-danger btn-sm float-right rmperson\"><i class=\"fas fa-trash\"></i></div><input type=\"hidden\" name=\"users[]\" value=\"" + p + "\" /></div>");
    $("#people-box").val("");
}

function removePerson(p) {
    $("#peoplelist div[data-user=" + p + "]").remove();
}

$("#selectgroupbtn").click(function () {
    document.location.href = "app.php?page=groups&gid=" + $("#group-box").val();
});

$("#people-box").on("change", function (event) {
    addPerson($("#people-box").val());
});

$("#addpersonbtn").click(function () {
    addPerson($("#people-box").val());
});

$('#peoplelist').on("click", ".rmperson", function () {
    removePerson($(this).parent().data("user"));
});