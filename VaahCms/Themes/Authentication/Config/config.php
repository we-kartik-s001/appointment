<?php

return [
    "name"=> "Authentication",
    "title"=> "Authentication Scaffolding",
    "slug"=> "authentication",
    "thumbnail"=> "https://img.site/p/300/160",
    "excerpt"=> "This theme will integrate the authentication scaffolding",
    "description"=> "This theme will integrate the authentication scaffolding",
    "download_link"=> "",
    "author_name"=> "authentication",
    "author_website"=> "https://vaah.dev",
    "version"=> "v0.0.1",
    "is_migratable"=> true,
    "is_sample_data_available"=> false,
    "db_table_prefix"=> "vh_authentication_",
    "providers"=> [
        "\\VaahCms\\Themes\\Authentication\\Providers\\AuthenticationServiceProvider"
    ],
    "aside-menu-order"=> null
];
