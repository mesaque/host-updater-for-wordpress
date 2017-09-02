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
	if ( ! $ssh_connection = get_connection( $client ) ):
		echo "Cant connect thru SSH\n";
		continue;
	endif;

	echo get_wp_version( $ssh_connection, $client );
	echo get_plugins_list( $ssh_connection, $client );
	echo get_themes_list( $ssh_connection, $client );

endforeach;