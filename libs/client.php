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

function notify_client( $client, $ssh_connection, $type = 1, $body_message = '' )
{
	global $configParams;

	if( 1 == $type ):
		$update_list   = handle_updates_list( $client, $ssh_connection );
		$formated_html = format_body_mail( $update_list );

		mail_service( $client, "{$configParams['administration']['company_name']} - {$client['domain']} Need to be updated!", $formated_html );
		return;
	endif;

	mail_service( $client, "{$configParams['administration']['company_name']} - {$client['domain']} is successifully updated", $body_message );
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

	$list['action'] = 1;

	return $list;
}
function format_body_mail( $updated_list )
{
	$html =	'<table>';

	if( array_key_exists( 'plugins', $updated_list ) && $updated_list['plugins'] != null ):
		foreach ( $updated_list['plugins'] as $plugin ):
			if( @$plugin->update != 'available' && $updated_list['action'] == 1 ) continue;
			$html .= "<tr><th>Plugin:</th><th>{$plugin->name}</th></tr>";
		endforeach;
	endif;
	if( array_key_exists( 'themes', $updated_list ) && $updated_list['themes'] != null ):
		foreach ( $updated_list['themes'] as $theme ):
			if( @$theme->update != 'available'  && $updated_list['action'] == 1 ) continue;
			$html .= "<tr><th>Theme:</th><th>{$theme->name}</th></tr>";
		endforeach;
	endif;
	if( array_key_exists( 'wordpress',  $updated_list ) &&  $updated_list['wordpress'] != null ):

		$minors = array();
		$wp = $updated_list['wordpress'];
		if( $updated_list['action'] == 2 ) $wp = $updated_list['wordpress']['info'];
		foreach ( $wp as $wordpress ):
				$minors[] = $wordpress->version;
		endforeach;
		$from = implode(' to ', $minors );
		$html .= "<tr><th>WordPress:</th><th>{$from}</th></tr>";
	endif;
	
	$html .= '</table>';
	return $html;
}

function update_client( $client, $ssh_connection  )
{
	global $configParams;

	$status_list   = handle_updates_client( $client, $ssh_connection );
	$formated_html = format_body_mail( $status_list );

	return $formated_html;
}

function handle_updates_client( $client, $ssh_connection )
{
	$status = array();
	switch ( $client['job'] ):
		case 1:
			$status['plugins'] = update_plugins( $ssh_connection, $client );
			break;
		case 2:
			$status['wordpress'] = update_core( $ssh_connection, $client );
			break;
		case 3:
			$status['plugins']   = update_plugins( $ssh_connection, $client );
			$status['wordpress'] = update_core( $ssh_connection, $client );
			break;
		case 4:
			$status['themes'] = update_themes( $ssh_connection, $client );
			break;
		case 5:
			$status['plugins'] = update_plugins( $ssh_connection, $client );
			$status['themes']  = update_themes( $ssh_connection, $client );
			break;
		case 6:
			$status['wordpress'] = update_core( $ssh_connection, $client );
			$status['themes']    = update_themes( $ssh_connection, $client );
			break;
		default:
			$status['plugins']   = update_plugins( $ssh_connection, $client );
			$status['wordpress'] = update_core( $ssh_connection, $client );
			$status['themes']    = update_themes( $ssh_connection, $client );
			break;
	endswitch;

	$status['action'] = 2;

	return $status;
}

function update_plugins( $ssh_connection, $client, $format = 'json' )
{
	$status = $ssh_connection->exec("wp plugin update --path={$client['wordpress_root_path']} --all --exclude={$client['excludes']['plugins']} --format={$format} --dry-run ");

	if( 'json' == $format ):
		$status = json_decode( $status );
	endif;

	return $status;
}
function update_themes( $ssh_connection, $client, $format = 'json' )
{
	$status = $ssh_connection->exec("wp theme update --path={$client['wordpress_root_path']} --all --exclude={$client['excludes']['themes']} --format={$format} --dry-run");

	if( 'json' == $format ):
		$status = json_decode( $status );
	endif;

	return $status;
}
function update_core( $ssh_connection, $client, $format = 'json' )
{
	$info =  $ssh_connection->exec("wp core check-update --path={$client['wordpress_root_path']} --format={$format}" );
	$status = intval( $ssh_connection->exec("wp core update --path={$client['wordpress_root_path']} --quiet &> /dev/null; echo $? ") );
	$ssh_connection->exec("wp core update-db --path={$client['wordpress_root_path']} --quiet &> /dev/null ");

	if( 0 == $status ) return array('info' => json_decode( $info ),  'status' => true );

	return array('info' => $info,  'status' => false );
}