CREATE TABLE tx_mdmastodon_domain_model_configuration (
	title varchar(255) NOT NULL DEFAULT '',
	api_url varchar(255) NOT NULL DEFAULT '',
	api_token varchar(255) NOT NULL DEFAULT '',
	api_method varchar(255) NOT NULL DEFAULT '',
    only_media int(11) NOT NULL DEFAULT '0',
    account_id bigint(20) NOT NULL DEFAULT '0',
    exclude_replies int(11) NOT NULL DEFAULT '0',
    exclude_reblogs int(11) NOT NULL DEFAULT '0',
    only_pinned int(11) NOT NULL DEFAULT '0',
    hashtag varchar(255) NOT NULL DEFAULT '',
    list_id varchar(255) NOT NULL DEFAULT '',
	update_frequency varchar(255) NOT NULL DEFAULT '',
	import_date int(11) NOT NULL DEFAULT '0',
	data longtext NOT NULL DEFAULT ''
);
