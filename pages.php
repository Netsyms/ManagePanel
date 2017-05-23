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
    "404" => [
        "title" => "404 error"
    ]
]);