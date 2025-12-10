<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mastodon social networking API',
    'description' => 'Show data from Mastodon social network via API.',
    'category' => 'plugin',
    'author' => 'Christoph Daecke',
    'author_email' => 'typo3@mediadreams.org',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'scheduler' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
