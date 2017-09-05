<?php


function mail_service( $client, $subject, $message )
{
	global $configParams;

	$tls = '';
	if( $configParams['mail_service']['use_tls'] ):
		$tls = 'tls';
	endif;

	$transport = (new Swift_SmtpTransport( $configParams['mail_service']['smtp_host'], $configParams['mail_service']['smtp_port'], $tls ) )
	  ->setUsername( $configParams['mail_service']['smtp_user'] )
	  ->setPassword( $configParams['mail_service']['smtp_password'] )
	;

	$mailer = new Swift_Mailer($transport);

	$message = (new Swift_Message( $subject ))
	  ->setFrom([ $configParams['mail_service']['from_mail'] => $configParams['mail_service']['from_username'] ])
	  ->setTo( [ $client['administrator_mail'] => $client['administrator_name'] ] )
	  ->setCc( [ $configParams['administration']['administrator_mail'] => $configParams['administration']['administrator_name'] ] )
	  ->setContentType( "text/html; charset=UTF-8" )
	  ->setBody( $message )
	  ;

	try{
		// Send the message
		$result = $mailer->send($message);
	}catch( Swift_TransportException $e ){
		return false;
	}

	return true;
}