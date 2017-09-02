<?php

return [
	'mail_service' => [
		'smtp_host'     => '',
		'smtp_user'     => '',
		'smtp_password' => '',
		'smtp_port'     => '',
		'use_tls'       => true,
	],
	'administration' => [
		'administrator_name' => '',
		'administrator_mail' => '',
		'company_name'       => '',
	],
	'clients' => [
		[
			'domain'              => '',
			'action'              => '',
			'job'                 => '',
			'wordpress_root_path' => '',
			'administrator_name'  => '',
			'administrator_mail'  => '',
			'ssh'                 => [
				'host'        => '',
				'user'        => '',
				'password'    => '',
				'port'        => '',
				'private_key' => '',
			],
			'excludes' => [
				'themes'  => '',
				'plugins' => '',
			],
		],
	],
];