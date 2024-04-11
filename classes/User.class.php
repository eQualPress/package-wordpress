<?php

namespace wordpress;

class User extends \core\User {
	public static function getColumns(): array {
		return [
			'wordpress_user_id' => [
				'type'        => 'integer',
				'description' => 'The ID of the WordPress user',
				'required'    => false
			]
		];
	}
}