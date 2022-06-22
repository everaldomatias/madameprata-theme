<?php

// Adding Meta container admin shop_order pages
add_action( 'add_meta_boxes', 'mp_add_meta_boxes' );
if ( ! function_exists( 'mp_add_meta_boxes' ) ) {
    function mp_add_meta_boxes() {
        add_meta_box( 'mp_order_fields', __( 'Imprimir Pedido', 'madame-prata' ), 'mp_add_order_fields_for_print', 'shop_order', 'side', 'core' );
    }
}

add_action( 'admin_menu', 'mp_register_custom_page' );

function mp_register_custom_page()
{
    add_submenu_page(
        'hidden.php',
        'Imprimir Pedido',     // page title
        '',     // menu title
        'manage_options',   // capability
        'order-print',     // menu slug
        'mp_print_order_html' // callback function
    );
}

// Adding Meta field in the meta container admin shop_order pages
if ( ! function_exists( 'mp_add_order_fields_for_print' ) ) {
    function mp_add_order_fields_for_print() {
        global $post;
        echo '<a href="' . admin_url( 'admin.php?page=order-print&order=' . $post->ID ) . '" target="_blank">Imprimir</a>';
    }
}

/**
 * @todo move to template file
 */
function mp_print_order_html() {

    global $title;

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $order_id = intval( $_GET['order'] );

    $order = new WC_Order( $order_id );

    print '<div class="print-order">';
    print "<h2>Detalhes do Pedido #$order_id</h2>";
    print '<hr>';

    print '<div class="columns">';
    print '<div class="column">';
        print "<h3>Faturamento</h3>";

        print "<span>";
        print $order->get_formatted_billing_address();
        print "</span>";

        if ( is_plugin_active( 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php' ) ) {

            print "<h3>Informações do clinete</h3>";

            print "<span>";
            print "<b>CPF:</b> " . esc_html( $order->get_meta( '_billing_cpf' ) );
            print "</span>";

            print "<span>";
            print "<b>Data de Nascimento:</b> " . esc_html( $order->get_meta( '_billing_birthdate' ) );
            print "</span>";

            print "<span>";
            print "<b>Sexo:</b> " . esc_html( $order->get_meta( '_billing_sex' ) );
            print "</span>";

            print "<span>";
            print "<b>Telefone:</b> " . esc_html( $order->get_billing_phone() );
            print "</span>";

            if ( '' !== $order->get_meta( '_billing_cellphone' ) ) {
                print "<span>";
                print "<b>Celular:</b> " . esc_html( $order->get_meta( '_billing_cellphone' ) );
                print "</span>";
            }

            print "<span>";
            print "<b>E-mail:</b> " . esc_html( $order->get_billing_email() );
            print "</span>";

        } else {
            print "<h3>Endereco de e-mail</h3>";
            print "<span>";
            print $order->get_billing_email();
            print "</span>";
    
            print "<h3>Telefone</h3>";
            print "<span>";
            print $order->get_billing_phone();
            print "</span>";
    
            print "<h3>Informações do cliente</h3>";
            print "<span>";
            print $order->get_billing_phone();
            print "</span>";
        }

    print '</div>';

    print '<div class="column">';
        print "<h3>Entrega</h3>";

        print "<span>";
        print $order->get_formatted_shipping_address();
        print "</span>";
    print '</div>';

    print '</div>';

    print '</div>';

    print '<script type="text/javascript">';
    print 'window.onload = function() { window.print(); }';
    print '</script>';
}
