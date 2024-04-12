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

$user = \wordpress\User::search( [ 'login', '=', $params['email'] ] )->read( [ 'id' ] );

$message = 'User already registered.';

if ( ! $user->first( true ) ) {
//eQual::run( 'do', 'user_signup', $params );

	\wordpress\User::create( [
		'wordpress_user_id' => $params['wordpress_user_id'],
		'login'             => $params['email'],
		'password'          => password_hash( $params['password'], PASSWORD_BCRYPT ),
		'firstname'         => $params['firstname'],
		'lastname'          => $params['lastname'],
		'username'          => $params['username'] ?? mb_split( '@', $params['email'] )[0],
		'language'          => constant( 'DEFAULT_LANG' ),
		'send_confirm'      => false,
		'validated'         => true,
	] );

	$user = \wordpress\User::search( [ 'login', '=', $params['email'] ] )
	                       ->read( [ 'id', 'password' ] )
	                       ->first( true );


	$usersIdsGroup = \core\Group::search( [ 'name', '=', 'users' ] )->read( [ 'users_ids' ] )->get( true );
	$usersIds      = $usersIdsGroup['users_ids'];
	$usersIds[]    = $user['id'];
	\core\Group::search( [ 'name', '=', 'users' ] )->update( [ 'users_ids' => $usersIds ] );

	$message = 'User successfully registered.';
}

$context->httpResponse()
        ->setBody( [ 'message' => $message ] )
        ->setContentType( 'application/json' )
        ->statusCode( 201 )
        ->send();
