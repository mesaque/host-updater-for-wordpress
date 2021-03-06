<?php
require __DIR__ . '/vendor/autoload.php';

 /**
  * This cli is intend to make a some administration host tasks that consume time periodically.
  *  
  * @param you need set up the config file before start to use.
  *
  * @author Mesaque Silva
  * @version 1.0
  * @license GPL2
  */

ini_set("memory_limit",-1);
set_time_limit(0);
error_reporting(E_ALL);
define( 'ABSPATH', dirname(__FILE__) . '/' );

$configFile = ABSPATH . 'config.php';
if ( ! file_exists( $configFile ) ):
	echo "You need at least a config file to run.\n";
	exit(1);
endif;
$configParams = include $configFile;

foreach( glob("libs/*.php" ) as $filename ):
    include $filename;
endforeach;

$client_list = get_clients();
if( false === $client_list ):
	echo "There is no client to iterate\n";
	exit(0);
endif;

foreach( $client_list as $client ):

	if( false === $client['enabled'] ) continue;

	if ( ! $ssh_connection = get_connection( $client ) ):
		echo "Can't connect thru SSH\n";
		continue;
	endif;

	switch ( $client['action'] ):
		case 1:
			// Just Notify
			notify_client( $client, $ssh_connection, 1 );
			break;
		case 2:
			// Just update
			update_client( $client, $ssh_connection );
			break;
		
		default:
			// Update and Notify
			$updated_list = update_client( $client, $ssh_connection );
			notify_client( $client, $ssh_connection, 2, $updated_list );
			break;
	endswitch;

endforeach;