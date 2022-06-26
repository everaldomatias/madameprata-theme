<?php

/*
* Remove dashboard widgets
*/
function remove_dashboard_widgets() {
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal');
    remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal');
    remove_meta_box( 'wordfence_activity_report_widget', 'dashboard', 'normal' );
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

/**
 * Remove the default welcome dashboard message
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
remove_action( 'welcome_panel', 'wp_welcome_panel', 100 );

/**
 * Custom welcome panel function
 *
 * @access      public
 * @since       1.0 
 * @return      void
 */
function wpex_wp_welcome_panel() { ?>

    <?php
    $options = get_option( 'mp_settings' );
    $buttons = isset( $options['buttons'] ) ? $options['buttons'] : [];
    $reorder_array_buttons = [];

    if ( count( $buttons ) >= 1 ) {
        foreach ( $buttons['title'] as $key => $value ) {
            $reorder_array_buttons[$key] = [
                'title' => $value,
                'link' => $buttons['link'][$key]
            ];
        }
    }
    ?>

    <div class="madame-prata-welcome-panel-content">
        <?php if ( has_custom_logo() ) : ?>
            <?php echo get_custom_logo(); ?>
        <?php else : ?>
            <h3><?php _e( 'Seja bem vindo ao painel administrativo do Madame Prata' ); ?></h3>
        <?php endif; ?>

        <?php if ( count( $reorder_array_buttons ) >= 1 ) : ?>
            <div class="buttons">
                <?php foreach( $reorder_array_buttons as $button ) : ?>
                    <?php if ( '' !== $button['title'] && '' !== $button['link'] ) : ?>
                        <a href="<?php echo esc_url( $button['link'] ); ?>"><?php echo esc_attr( $button['title'] ); ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div><!-- .madame-prata-welcome-panel-content -->

<?php }
add_action( 'welcome_panel', 'wpex_wp_welcome_panel', 101 );