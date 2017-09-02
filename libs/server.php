<?php

use \phpseclib\Net\SSH2;
use \phpseclib\Crypt\RSA;

function get_connection( $client )
{

	$ssh = new SSH2($client['ssh']['host']);

	if( empty($client['ssh']['password']) ):
		$key = new RSA();
		if( ! empty( empty($client['ssh']['private_key_password']) ) ):
			$key->setPassword( $client['ssh']['private_key_password'] );
		endif;

		$key->loadKey(file_get_contents( $client['ssh']['private_key'] ));

		if ( !$ssh->login( $client['ssh']['user'], $key ) ):
			    return false;
		endif;

		return $ssh;
	endif;

	if (!$ssh->login( $client['ssh']['user'] , $client['ssh']['password'] ) ):    	
    	return false;
	endif;

	return $ssh;
}