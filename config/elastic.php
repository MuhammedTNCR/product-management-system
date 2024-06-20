<?php

return [
    'client' => [
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'localhost:9200'),
        ],
    ],
];
