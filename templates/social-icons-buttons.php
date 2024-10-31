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
            'class'     => 'wsl-social-below wsl-' . $key
        );

        $image = sprintf( '<img src="%s" alt="%s"/>', $args['image_url'], isset( $value['label'] ) ? $value['label'] : $value ); ?>

        <a style="background-color: <?php echo esc_html( $value['color']); ?>" class="<?php  echo esc_html( $args['class'] ) ?>" href="<?php echo esc_url( $args['url'] ) ?>">
            <span class="wsl-button-icon">
                <?php echo apply_filters( 'social-icon', $image ); ?>
            </span>
            <span class="wsl-button-label">
                <?php echo esc_html( $label ) ?> <b><?php echo esc_html( $value['label'] ) ?></b>  
            </span>
        </a>

        <?php 
    }
}