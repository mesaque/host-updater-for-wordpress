<?php

function get_clients()
{
	global $configParams;

	if( empty( $configParams['clients'] ) ) return false;

	return $configParams['clients'];
}

function get_wp_version( $ssh_connection, $client, $format = 'json' )
{
	$list = $ssh_connection->exec("wp core check-update --path={$client['wordpress_root_path']} --format={$format}");

	if( 'json' == $format ):
		$list = json_decode( $list );
	endif;

	return $list;
}
function get_plugins_list( $ssh_connection, $client, $format = 'json' )
{
	$list = $ssh_connection->exec("wp plugin list --path={$client['wordpress_root_path']} --format={$format}");

	if( 'json' == $format ):
		$list = json_decode( $list );
	endif;

	return $list;
}
function get_themes_list( $ssh_connection, $client, $format = 'json' )
{
	$list = $ssh_connection->exec("wp theme list --path={$client['wordpress_root_path']} --format={$format}");

	if( 'json' == $format ):
		$list = json_decode( $list );
	endif;

	return $list;
}

function notify_client( $client, $ssh_connection, $type = 1 )
{
	global $configParams;

	$update_list   = handle_updates_list( $client, $ssh_connection );
	$formated_html = format_body_mail( $update_list );

	if( 1 == $type ):
		mail_service( $client, "{$configParams['administration']['company_name']} - {$client['domain']} Need to be updated!", $formated_html);
		return;
	endif;

	mail_service( $client, "{$configParams['administration']['company_name']} - {$client['domain']} is successifully updated", $formated_html );
}

function handle_updates_list( $client, $ssh_connection )
{

	$list = array();

	switch ( $client['job'] ):
		case 1:
			$list['plugins'] = get_plugins_list( $ssh_connection, $client );
			break;
		case 2:
			$list['wordpress'] = get_wp_version( $ssh_connection, $client );
			break;
		case 3:
			$list['plugins']   = get_plugins_list( $ssh_connection, $client );
			$list['wordpress'] = get_wp_version( $ssh_connection, $client );
			break;
		case 4:
			$list['themes'] = get_themes_list( $ssh_connection, $client );
			break;
		case 5:
			$list['plugins'] = get_plugins_list( $ssh_connection, $client );
			$list['themes']  = get_themes_list( $ssh_connection, $client );
			break;
		case 6:
			$list['wordpress'] = get_wp_version( $ssh_connection, $client );
			$list['themes']    = get_themes_list( $ssh_connection, $client );
			break;
		default:
			$list['plugins']   = get_plugins_list( $ssh_connection, $client );
			$list['wordpress'] = get_wp_version( $ssh_connection, $client );
			$list['themes']    = get_themes_list( $ssh_connection, $client );
			break;
	endswitch;


	return $list;
}
function format_body_mail( $updated_list )
{
	$html =	'<table>';
	if( array_key_exists( 'plugins', $updated_list ) ):

		foreach ( $updated_list['plugins'] as $plugin ):
			if( $plugin->update != 'available' ) continue;
			$html .= "<tr><th>Plugin:</th><th>{$plugin->name}</th></tr>";
		endforeach;
	endif;
	if( array_key_exists( 'themes', $updated_list ) ):
		foreach ( $updated_list['themes'] as $theme ):
			if( $theme->update != 'available' ) continue;
			$html .= "<tr><th>Theme:</th><th>{$theme->name}</th></tr>";
		endforeach;
	endif;
	if( array_key_exists( 'wordpress',  $updated_list ) ):
		foreach ( $updated_list['wordpress'] as $wordpress ):
		$html .= "<tr><th>WordPress:</th><th>{$wordpress->version}</th></tr>";
		endforeach;
	endif;
	
	$html .= '</table>';
	return $html;
}