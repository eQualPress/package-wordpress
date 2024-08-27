<?php

list( $params, $providers ) = eQual::announce( [
	'description' => 'Returns the installation configuration adapted for eQualPress.',
	'access'      => [
		'visibility' => 'public'
	],
	'response'    => [
		'content-type'  => 'application/json',
		'charset'       => 'UTF-8',
		'accept-origin' => '*'
	],
	'providers'   => [ 'context' ]
] );

list( $context ) = [ $providers['context'] ];

$config = eQual::run( 'get', 'envinfo' );

//$config = json_decode( $config, true );
//
$config['backend_url'] = $config['backend_url'] . '/equal.php';

$context->httpResponse()
        ->status( 201 )
        ->body( $config )
        ->send();
