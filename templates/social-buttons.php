<?php
/**
 * Section to show social login buttons
 *
 * @package Opal Social Login
 * @since   1.0.0
 * @author  WpOpal
 */

$button_style = empty( wsl_get_option( 'wsl_button_style' ) ) ? 'default' : wsl_get_option( 'wsl_button_style' );
?>

<div class="wc-social-login">
    
    <?php if( !empty($label) && $button_style != 'buttons'):?>
        <p class="wsl-label"><?php echo $label ?></p>
        <p class="socials-list">
        <?php
        endif;
            
            WSL_Social_Login_Frontend()->wsl_social_buttons('social-icons-'.$button_style);

            if ( ! empty( $label ) && $button_style != 'buttons' ) {
                echo '</p>';
            }
        ?>
</div>