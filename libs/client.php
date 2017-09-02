<?php

function get_clients()
{
	global $configParams;

	if( empty( $configParams['clients'] ) ) return false;

	return $configParams['clients'];
}

function get_wp_version( $ssh_connection, $client, $format = 'json' )
{
	return $ssh_connection->exec("wp core check-update --path={$client['wordpress_root_path']} --format={$format}");
}
function get_plugins_list( $ssh_connection, $client, $format = 'json' )
{
	return $ssh_connection->exec("wp plugin list --path={$client['wordpress_root_path']} --format={$format}");
}
function get_themes_list( $ssh_connection, $client, $format = 'json' )
{
	return $ssh_connection->exec("wp theme list --path={$client['wordpress_root_path']} --format={$format}");
}