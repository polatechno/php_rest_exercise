<?php

$db = require __DIR__ . '/db.php';

return [
    'id' => 'rest_api',
    'basePath' => realpath(__DIR__ . '/../'),
    'components' => [
        'db' => $db,
    ],
];