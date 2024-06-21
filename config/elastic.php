<?php

return [
    'client' => [
        'hosts' => [
            env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200'),
        ],
    ],
];
