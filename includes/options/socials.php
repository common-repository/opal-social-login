<?php

defined( 'ABSPATH' ) || exit();

$facebook_color = wsl_get_option('wsl_facebook_color');
$twitter_color = wsl_get_option('wsl_twitter_color');
$google_color = wsl_get_option('wsl_google_color');

return  array(
	'facebook' => array(
		'label' => __( 'Facebook', 'opal-social-login' ),
		'color' => empty( $facebook_color ) ? '#3b5998' : $facebook_color
	),
	'twitter'  => array(
		'label' => __( 'Twitter', 'opal-social-login' ),
		'color' => empty( $twitter_color ) ? '#00aced' : $twitter_color
	),
	'google'   => array(
		'label' => __( 'Google', 'opal-social-login' ),
		'color' => empty( $google_color ) ? '#dd4b39' : $google_color
	)
);