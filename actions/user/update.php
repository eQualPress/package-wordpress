<?php

list( $params, $providers ) = eQual::announce( [
	'description' => "Update (fully or partially) the given object.",
	'params'      => [
		'id'        => [
			'description' => 'Unique identifier of the object to update.',
			'type'        => 'integer',
			'default'     => 0
		],
		'fields'    => [
			'description' => 'Associative array mapping fields to be updated with their related values.',
			'type'        => 'array',
			'default'     => []
		],
		'update_wp' => [
			'description' => 'Flag for updating the WordPress user.',
			'type'        => 'string',
			'default'     => '0',
			'required'    => false
		]
	],
	'access'      => [
		'visibility' => 'public'
	],
	'response'    => [
		'content-type'  => 'application/json',
		'charset'       => 'UTF-8',
		'accept-origin' => '*'
	],
	'providers'   => [ 'context', 'auth', 'orm' ],
] );

$update_wp = true;

if ( $params['update_wp'] === '0' ) {
	$update_wp = false;
}

include_once ABSPATH . '/wp-content/Log.php';
\wpcontent\Log::report( 'eq_wordpress_user_update => $params', $params );

\wpcontent\Log::report( "eq_wordpress_user_update: array keys fields param", array_keys( $params['fields'] ) );

$fields   = array_keys( $params['fields'] );
$fields[] = 'wordpress_user_id';
$fields[] = 'groups_ids';


\wpcontent\Log::report( 'eq_wordpress_user_update => $fields', $fields );
\wpcontent\Log::report( 'eq_wordpress_user_update => $fields array_values', array_values( $fields ) );

$eq_user = \wordpress\User::id( $params['id'] )->read( $fields )->first( true );

\wpcontent\Log::report( 'eq_wordpress_user_update => $eqUser', $eq_user );

$eq_user_groups = \core\Group::search( [ 'id', 'in', $eq_user['groups_ids'] ] )->read( [ 'name' ] )->get( true );

\wpcontent\Log::report( 'eq_wordpress_user_update => eq_user_groups', $eq_user_groups );

$eq_user_groups = array_values( array_map( function ( $group ) {
	return $group['name'];
}, $eq_user_groups ) );

\wpcontent\Log::report( 'eq_wordpress_user_update => eq_user_groups', $eq_user_groups );


if ( empty( $eq_user ) ) {
	\wpcontent\Log::report( 'eq_wordpress_user_update: eqUser not found' );
//	throw new Exception( "user_not_found", QN_ERROR_INVALID_USER );
}

\wpcontent\Log::report( 'eq_wordpress_user_update => $eq_user before update', $eq_user );

\wordpress\User::search( [ 'login', '=', $params['fields']['login'] ] )
               ->update( $params['fields'] );

$eq_user = \wordpress\User::search( [ 'login', '=', $params['fields']['login'] ] )
                          ->read( [ 'wordpress_user_id', 'firstname', 'lastname' ] )
                          ->first( true );

\wpcontent\Log::report( 'eq_wordpress_user_update => $eq_user after update', $eq_user );

\wpcontent\Log::report( "test if 1", $update_wp ? 'true' : 'false' );
\wpcontent\Log::report( "test if 2", isset( $eq_user['wordpress_user_id'] ) ? 'true' : 'false' );
\wpcontent\Log::report( "test if 3", in_array( 'users', $eq_user_groups ) ? 'true' : 'false' );

if (
	$update_wp &&
	isset( $eq_user['wordpress_user_id'] ) &&
	in_array( 'users', $eq_user_groups )
) {
	$wp_user = get_user_by( 'ID', $eq_user['wordpress_user_id'] );

	\wpcontent\Log::report( 'eq_wordpress_user_update: inside if wordpress_user_update', $wp_user );

	if ( $wp_user ) {
		foreach ( $params['fields'] as $key => $value ) {
			switch ( $key ) {
				case 'login':
					$wp_user->user_login = $value;
					$wp_user->user_email = $value;
					break;
				case 'password':
					$wp_user->user_pass = $value;
					break;
				case 'username':
					$wp_user->user_nicename = $value;
					$wp_user->display_name  = $value;
					break;
			}
		}

		$wpUpdateUserResponse = wp_update_user( $wp_user );

		if ( is_wp_error( $wpUpdateUserResponse ) ) {
			throw new Exception( $wpUpdateUserResponse->get_error_message(), QN_ERROR_INVALID_PARAM );
		} else {
			// If the password is updated, we need to re-authenticate the user because
			// WordPress use the password to generate the cookie for maintaining the authentication up.
			// So, we need to re-authenticate the user with the new password.
			// Like described in the WordPress documentation
			if ( in_array( 'password', array_keys( $params['fields'] ) ) ) {
				wp_set_current_user( $wp_user->ID );
				wp_set_auth_cookie( $wp_user->ID );
			}
		}
	}
}

