<?php
return [
    'registeredHomepage' => '/site/index',
    'guestHomepage' => '/site/login',
    'startPagesForRoles' => [
        '?' => '/site/login',
        '@' => '/site/index'
    ],
];