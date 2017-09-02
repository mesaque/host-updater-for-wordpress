<?php

return [
	'mail_service' => [
		'smtp_host'     => '',
		'smtp_user'     => '',
		'smtp_password' => '',
		'smtp_port'     => '',
		'from'          => '',
		'use_tls'       => true,
	],
	'administration' => [
		'administrator_name' => '',
		'administrator_mail' => '',
		'company_name'       => '',
	],
	'clients' => [
		[
			'enabled'             => true,
			'domain'              => '',
			'action'              => '',
			'job'                 => '',
			'wordpress_root_path' => '',
			'administrator_name'  => '',
			'administrator_mail'  => '',
			'ssh'                 => [
				'host'                 => '',
				'user'                 => '',
				'password'             => '',
				'port'                 => '',
				'private_key'          => '',
				'private_key_password' => '',
			],
			'excludes' => [
				'themes'  => '',
				'plugins' => '',
			],
		],
	],
];