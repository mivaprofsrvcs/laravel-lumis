<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Miva JSON API Configuration
	|--------------------------------------------------------------------------
	|
	| Settings for connecting to the Miva JSON API.
	|
	*/

	'api' => [
		'key' => env('MM_API_KEY'),
		'token' => env('MM_API_TOKEN'),
		'url' => env('MM_API_URL'),
	],

	/*
	|--------------------------------------------------------------------------
	| Miva Store Settings
	|--------------------------------------------------------------------------
	|
	| Configuration settings specific to the Miva store.
	|
	*/

	'store' => [
		'auth' => [
			'password' => env('MM_STORE_AUTH_PASSWORD', ''),
			'username' => env('MM_STORE_AUTH_USERNAME', ''),
		],

		'code' => env('MM_STORE_CODE'),

		'graphics_path' => env('MM_STORE_GRAPHICS_PATH', 'graphics/00000001/'),

		'root_path' => env('MM_STORE_ROOT_PATH', '/mm5/'),

		'url' => env('MM_STORE_URL'),
	],

];
