<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mastodon social networking API',
    'description' => 'Show data from Mastodon social network via API.',
    'category' => 'plugin',
    'author' => 'Christoph Daecke',
    'author_email' => 'typo3@mediadreams.org',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
            'backend' => '13.4.0-14.3.99',
            'extbase' => '13.4.0-14.3.99',
            'fluid' => '13.4.0-14.3.99',
            'frontend' => '13.4.0-14.3.99',
            'install' => '13.4.0-14.3.99',
            'scheduler' => '13.4.0-14.3.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
