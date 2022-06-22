<?php

add_action( 'admin_menu', 'mp_add_admin_menu' );
add_action( 'admin_init', 'mp_settings_menu' );

function mp_add_admin_menu() {
    add_options_page(
        __( 'Boas Vindas', 'boas-vindas' ),
        __( 'Boas Vindas', 'boas-vindas' ),
        'manage_options',
        'boas-vindas',
        'mp_settings_page_render'
    );
}

function mp_settings_menu() {
    register_setting( 'boas-vindas', 'mp_settings' );

    add_settings_section(
        'mp_section',
        __( 'Boas Vindas', 'boas-vindas' ),
        'mp_settings_section_callback',
        'boas-vindas'
    );

    add_settings_field(
        'phones',
        __( 'Botões', 'boas-vindas' ),
        'mp_settings_phones_render',
        'boas-vindas',
        'mp_section'
    );
}

function mp_settings_section_callback() {
    echo '';
}

function mp_settings_phones_render() {
    $options = get_option( 'mp_settings' );
    $buttons = isset( $options['buttons'] ) ? $options['buttons'] : [];

    $buttons = array_filter( $buttons );

    if ( is_array( $buttons ) && count( $buttons ) >= 1 ) : ?>

        <?php foreach( $buttons['title'] as $key => $button ) : ?>
            <?php if ( '' !== $button && '' !== $buttons['link'][$key] ) : ?>
                <div class="repeatable-row">
                    <div><input type="text" placeholder="<?php _e( 'Adicione aqui o título do botão', 'boas-vindas' ); ?>" name="mp_settings[buttons][title][<?php echo $key; ?>]" value="<?php if ( $button != '' ) echo esc_attr( $button ); ?>" /></div>
                    <div><input type="text" placeholder="<?php _e( 'Adicione aqui o link do botão', 'boas-vindas' ); ?>" name="mp_settings[buttons][link][<?php echo $key; ?>]" value="<?php if ( $buttons['link'][$key] != '' ) echo esc_attr( $buttons['link'][$key] ); ?>" /></div>
                    <div><a class="button remove-row" href="#1"><?php _e( 'Remover', 'boas-vindas' ); ?></a></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    <?php else : ?>

        <div class="repeatable-row">
            <div><input type="text" placeholder="<?php _e( 'Adicione aqui o título do botão', 'boas-vindas' ); ?>" name="mp_settings[buttons][title][]" /></div>
            <div><input type="text" placeholder="<?php _e( 'Adicione aqui o link do botão', 'boas-vindas' ); ?>" name="mp_settings[buttons][link][]" /></div>
            <div><a class="button remove-row" href="#"><?php _e( 'Remover', 'boas-vindas' ); ?></a></div>
        </div>

    <?php endif; ?>

    <div class="repeatable-row empty-row">
        <div><input type="text" placeholder="<?php _e( 'Adicione aqui o título do botão', 'boas-vindas' ); ?>" name="mp_settings[buttons][title][]" /></div>
        <div><input type="text" placeholder="<?php _e( 'Adicione aqui o link do botão', 'boas-vindas' ); ?>" name=mp_settings[buttons][link][]" /></div>
        <div><a class="button remove-row" href="#1"><?php _e( 'Remover', 'boas-vindas' ); ?></a></div>
    </div>

    <p><small><i>*Preencha corretamente os dois campos, título e link para cada botão.</i></small></p>
    <p><a id="add-row" class="button" href="#"><?php _e( 'Adicionar outro botão', 'boas-vindas' ); ?></a></p>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#add-row').on('click', function() {
                var row = $('.empty-row').clone(true);
                // console.log($('.repeatable-row:not(.empty-row)').length);
                // row.attr('name', 'sel' + rowIndex); 
                row.removeClass('empty-row');
                row.insertBefore('.repeatable-row:last');
                return false;
            });

            $('.remove-row').on('click', function() {
                $(this).closest('.repeatable-row').remove();
                return false;
            });
        });
    </script>

    <style>
        .repeatable-row {
            display: flex;
            column-gap: 15px;
            margin-bottom: 15px;
        }

        .repeatable-row.empty-row {
            display: none;
        }
    </style>

    <?php
}

/**
 * Render the settings page
 */
function mp_settings_page_render() {
    ?>
    <form action='options.php' method='post'>
        <?php
        settings_fields( 'boas-vindas' );
        do_settings_sections( 'boas-vindas' );
        submit_button();
        ?>
    </form>
    <?php
}
