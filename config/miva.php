<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default connection name
	|--------------------------------------------------------------------------
	*/
	'default' => env('MM_CONNECTION', 'default'),

	/*
	|--------------------------------------------------------------------------
	| Connections
	|--------------------------------------------------------------------------
	|
	| Each connection defines api + store settings for a single Miva store.
	|
	*/
	'connections' => [
		'default' => [
			'api' => [
				'key' => env('MM_API_KEY'),
				'token' => env('MM_API_TOKEN'),
				'url' => env('MM_API_URL'),
				'verify_ssl' => (bool) env('MM_API_VERIFY_SSL', false),
			],
			'store' => [
				'auth' => [
					'username' => env('MM_STORE_AUTH_USERNAME', ''),
					'password' => env('MM_STORE_AUTH_PASSWORD', ''),
				],
				'code' => env('MM_STORE_CODE'),
				'graphics_path' => env('MM_STORE_GRAPHICS_PATH', 'graphics/00000001/'),
				'root_path' => env('MM_STORE_ROOT_PATH', '/mm5/'),
				'url' => env('MM_STORE_URL'),
			],
		],

		// Add more named connections as needed:
		// 'teamsites' => [ 'api' => [...], 'store' => [...] ],
		// 'conferencesites' => [ 'api' => [...], 'store' => [...] ],
	],

];
