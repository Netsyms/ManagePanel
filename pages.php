<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "fas fa-home"
    ],
    "users" => [
        "title" => "users",
        "navbar" => true,
        "icon" => "fas fa-users",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/users.js"
        ],
    ],
    "edituser" => [
        "title" => "edit user",
        "navbar" => false,
        "scripts" => [
            "static/js/edituser.js"
        ]
    ],
    "deluser" => [
        "title" => "delete user",
        "navbar" => false
    ],
    "groups" => [
        "title" => "groups",
        "navbar" => true,
        "icon" => "fas fa-object-group",
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/groups.js"
        ],
    ],
    "authlog" => [
        "title" => "security log",
        "navbar" => true,
        "icon" => "fas fa-list",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/authlog.js"
        ],
    ],
    "clearlog" => [
        "title" => "clear log",
        "navbar" => false
    ],
    "managers" => [
        "title" => "managers",
        "navbar" => true,
        "icon" => "fas fa-id-card",
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/managers.js"
        ]
    ],
    "permissions" => [
        "title" => "permissions",
        "navbar" => true,
        "icon" => "fas fa-key",
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/permissions.js"
        ],
    ],
    "export" => [
        "title" => "report export",
        "navbar" => true,
        "icon" => "fas fa-download",
        "scripts" => [
            "static/js/export.js"
        ]
    ],
    "404" => [
        "title" => "404 error"
    ]
]);
