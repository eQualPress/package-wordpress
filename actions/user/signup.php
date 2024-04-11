<?php

list( $params, $providers ) = eQual::announce( [
	'description' => "Attempt to register a new user.",
	'params'      => [
		'wordpress_user_id' => [
			'description' => 'The ID of the WordPress user.',
			'type'        => 'integer',
			'required'    => true
		],
		'email'             => [
			'description' => 'Email address of the user.',
			'type'        => 'string',
			'usage'       => 'email',
			'required'    => true
		],
		'username'          => [
			'description' => 'Nickname of the user.',
			'type'        => 'string',
			'required'    => true
		],
		'password'          => [
			'description' => 'The user chosen password.',
			'type'        => 'string',
			'usage'       => 'password/nist',
			'required'    => true
		],
		'firstname'         => [
			'description' => 'User\'s firstname.',
			'type'        => 'string',
			'default'     => ''
		],
		'lastname'          => [
			'description' => 'User\'s lastname.',
			'type'        => 'string',
			'default'     => ''
		],
		'language'          => [
			'description' => 'User\'s preferred language.',
			'type'        => 'string',
			'default'     => constant( 'DEFAULT_LANG' )
		],
		'send_confirm'      => [
			'description' => 'Flag telling if we need to send a confirmation email.',
			'type'        => 'boolean',
			'default'     => true
		],
		'resend'            => [
			'description' => 'Previously sent message identifier to resend (must match credentials).',
			'type'        => 'integer',
			'default'     => 0
		]
	],
	'constants'   => [
		'USER_ACCOUNT_REGISTRATION',
		'DEFAULT_LANG',
		'EMAIL_SMTP_HOST',
		'EMAIL_SMTP_ACCOUNT_DISPLAYNAME'
	],
	'access'      => [
		'visibility' => 'public'
	],
	'response'    => [
		'content-type'  => 'application/json',
		'charset'       => 'utf-8',
		'accept-origin' => '*'
	],
	'providers'   => [ 'context', 'orm', 'auth' ]
] );

/**
 * @var equal\php\Context $context
 * @var equal\orm\ObjectManager $om
 * @var equal\auth\AuthenticationManager $auth
 */
list( $context, $om, $auth ) = [ $providers['context'], $providers['orm'], $providers['auth'] ];

// check if the user is already registered
$user = \wordpress\User::search( [ 'login', '=', $params['email'] ] )->read( [ 'id' ] )->first( true );

if ( ! $user ) {
	eQual::run( 'do', 'user_signup', $params );

	$user   = \wordpress\User::search( [ 'login', '=', $params['email'] ] );
	$userId = $user->first( true )['id'];
	\wordpress\User::id( $userId )->update( [
		'wordpress_user_id' => $params['wordpress_user_id'],
		'validated'         => true,
		'password'          => $params['password']
	] );

	\wordpress\User::onupdatePassword( $om, [ $userId ], [ 'password' => $params['password'] ], constant( 'DEFAULT_LANG' ) );
}

$context->httpResponse()
        ->setBody( [ 'message' => 'User successfully registered.' ] )
        ->setContentType( 'application/json' )
        ->statusCode( 201 )
        ->send();