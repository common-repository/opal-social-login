<?php
/**
 * Only icons for socials
 *
 * @package Opal Social Login
 * @since   1.0.0
 * @author  WpOpal
 */

foreach ( $socials as $key => $value ) {
    $enabled = wsl_get_option( 'wsl_' . $key . '_enable' );
    $image_url = empty( wsl_get_option( 'wsl_' . $key . '_icon' ) ) ? WSL_PLUGIN_ASSET_URI . 'images/' . $key . '.png' : wsl_get_option( 'wsl_' . $key . '_icon' );
    if ( $enabled == '1' ) {

        $args = array(
            'value'     => $value,
            'url'       => esc_url( add_query_arg( array(
                'wsl_social' => $value['label'],
                'redirect'    => urlencode( wsl_curPageURL() )
            ), site_url( 'wp-login.php' ) ) ),
            'image_url' => $image_url,
            'class'     => 'wsl-social wsl-' . $key
        );
        $image  = sprintf( '<img src="%s" alt="%s"/>', $args['image_url'], isset( $value['label'] ) ? $value['label'] : $value );
        $social = sprintf( '<a style="background-color:%s" class="%s" href="%s">%s</a>', $value['color'], $args['class'], $args['url'], $image );

        echo apply_filters( 'social_login_icon', $social, $key, $args );

    }
}