<?php

defined( 'ABSPATH' ) || exit();

$slash = defined( 'WSL_FINAL_SLASH' ) && WSL_FINAL_SLASH ? '/' : '';
return  [

	'callback' => Hybridauth\HttpClient\Util::getCurrentUrl(),
	'providers'  => [
		
		'Google' => [
			'enabled' => ( wsl_get_option( 'wsl_google_enable' ) == '1' ) ? true : false,
			'keys'    => [
				'id'     => wsl_get_option( 'wsl_google_id' ),
				'secret' => wsl_get_option( 'wsl_google_secret' ),
			],
		],

		'Facebook' => [
			'enabled'        => ( wsl_get_option( 'wsl_facebook_enable' ) == '1' ) ? true : false,
			'keys'           => [
				'id'     => wsl_get_option( 'wsl_facebook_id' ),
				'secret' => wsl_get_option( 'wsl_facebook_secret' )
			],
			'trustForwarded' => false,
		],

		'Twitter' => [
			'enabled'      => ( wsl_get_option( 'wsl_twitter_enable' ) == '1' ) ? true : false,
			'keys'         => [
				'key'    => wsl_get_option( 'wsl_twitter_key' ),
				'secret' => wsl_get_option( 'wsl_twitter_secret' )
			],
			'includeEmail' => true,
		],

	],

];