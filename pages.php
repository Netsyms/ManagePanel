<?php

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "home"
    ],
    "users" => [
        "title" => "users",
        "navbar" => true,
        "icon" => "users",
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
    "authlog" => [
        "title" => "security log",
        "navbar" => true,
        "icon" => "list",
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
        "icon" => "id-card-o",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/managers.js"
        ],
    ],
    "addmanager" => [
        "title" => "new relationship",
        "navbar" => false,
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/addmanager.js"
        ]
    ],
    "delmanager" => [
        "title" => "delete manager",
        "navbar" => false
    ],
    "permissions" => [
        "title" => "permissions",
        "navbar" => true,
        "icon" => "key",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/permissions.js"
        ],
    ],
    "addpermission" => [
        "title" => "new permission",
        "navbar" => false,
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/addpermission.js"
        ]
    ],
    "delpermission" => [
        "title" => "delete permission",
        "navbar" => false
    ],
    "404" => [
        "title" => "404 error"
    ]
]);