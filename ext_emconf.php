<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mastodon social networking API',
    'description' => 'Show data from Mastodon social network via API.',
    'category' => 'plugin',
    'author' => 'Christoph Daecke',
    'author_email' => 'typo3@mediadreams.org',
    'state' => 'alpha',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'scheduler' => '11.5.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
