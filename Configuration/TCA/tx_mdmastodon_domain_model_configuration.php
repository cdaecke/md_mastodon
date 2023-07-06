<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,data',
        'iconfile' => 'EXT:md_mastodon/Resources/Public/Icons/ApiConfiguration.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'title, api_token, api_url, api_method, --palette--;;accountsPalette, --palette--;;hashtagPalette, --palette--;;listPalette, --linebreak--, only_media, update_frequency, import_date, data, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'palettes' => [
        'accountsPalette' => [
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang.xlf:tx_mdmastodon_domain_model_configuration.accountsPalette.label',
            'showitem' => 'account_id, --linebreak--, exclude_replies, exclude_reblogs, only_pinned',
        ],
        'hashtagPalette' => [
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang.xlf:tx_mdmastodon_domain_model_configuration.hashtagPalette.label',
            'showitem' => 'hashtag',
        ],
        'listPalette' => [
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang.xlf:tx_mdmastodon_domain_model_configuration.listPalette.label',
            'showitem' => 'list_id',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_mdmastodon_domain_model_configuration',
                'foreign_table_where' => 'AND {#tx_mdmastodon_domain_model_configuration}.{#pid}=###CURRENT_PID### AND {#tx_mdmastodon_domain_model_configuration}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],

        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.title',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.title.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'api_token' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_token',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_token.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'api_url' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_url',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_url.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],
        'api_method' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_method',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.api_method.description',
            'onChange' => 'reload',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'eval' => 'trim,required',
                'items' => [
                    ['', ''],
                    ['Accounts', 'accounts'],
                    ['Hashtag timeline', 'hashtag_timeline'],
                    ['Home timeline', 'home_timeline'],
                    ['List timeline', 'list_timeline'],
                    ['Public timeline', 'public_timeline'],
                ],
            ],
        ],
        'update_frequency' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.update_frequency',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.update_frequency.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'import_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.import_date',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.import_date.description',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 20,
                'eval' => 'datetime',
                'readOnly' => true,
                'default' => 0
            ],
        ],
        'data' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.data',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.data.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'readOnly' => true,
                'default' => ''
            ]
        ],
        'only_media' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_media',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_media.description',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_media.0',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                    ],
                ],
            ],
        ],

        'account_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.account_id',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.account_id.description',
            'displayCond' => 'FIELD:api_method:=:accounts',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => '0'
            ]
        ],
        'exclude_replies' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_replies',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_replies.description',
            'displayCond' => 'FIELD:api_method:=:accounts',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_replies.0',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                    ],
                ],
            ],
        ],
        'exclude_reblogs' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_reblogs',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_reblogs.description',
            'displayCond' => 'FIELD:api_method:=:accounts',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.exclude_reblogs.0',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                    ],
                ],
            ],
        ],
        'only_pinned' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_pinned',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_pinned.description',
            'displayCond' => 'FIELD:api_method:=:accounts',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.only_pinned.0',
                        'labelChecked' => 'Enabled',
                        'labelUnchecked' => 'Disabled',
                    ],
                ],
            ],
        ],

        'hashtag' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.hashtag',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.hashtag.description',
            'displayCond' => 'FIELD:api_method:=:hashtag_timeline',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],

        'list_id' => [
            'exclude' => true,
            'label' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.list_id',
            'description' => 'LLL:EXT:md_mastodon/Resources/Private/Language/locallang_db.xlf:tx_mdmastodon_domain_model_configuration.list_id.description',
            'displayCond' => 'FIELD:api_method:=:list_timeline',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ]
        ],

    ],
];
