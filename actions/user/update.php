<?php

list($params, $providers) = announce([
	'description'   => 'Updates a user account based on given details.',
	'response'      => [
		'content-type'  => 'application/json',
		'charset'       => 'UTF-8',
		'accept-origin' => '*'
	],
	'params'        => [
		'id' =>  [
			'description'   => 'Identifier of the user to update.',
			'type'          => 'integer',
			'required'      => true
		],
		'firstname' =>  [
			'description'   => 'User firstname.',
			'type'          => 'string',
			'default'       => ''
		],
		'lastname' => [
			'description'   => 'User lastname.',
			'type'          => 'string',
			'default'       => ''
		],
		'language' => [
			'description'   => 'User language.',
			'type'          => 'string',
			'default'       => constant('DEFAULT_LANG')
		]
	],
	'constants'     => ['DEFAULT_LANG'],
	'providers'     => ['context', 'orm']
]);

/**
 * @var \equal\php\Context          $context
 * @var \equal\orm\ObjectManager    $orm
 */
list($context, $orm) = [ $providers['context'], $providers['orm'] ];

// update user instance
eQual::run('do', 'user_update', $params);

