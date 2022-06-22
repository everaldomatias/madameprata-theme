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

        print "<p>";
        print $order->get_formatted_billing_address();
        print "</p>";

        print "<h3>Endereco de e-mail</h3>";
        print "<p>";
        print $order->get_billing_email();
        print "</p>";

        print "<h3>Telefone</h3>";
        print "<p>";
        print $order->get_billing_phone();
        print "</p>";

    print '</div>';

    print '<div class="column">';
        print "<h3>Entrega</h3>";

        print "<p>";
        print $order->get_formatted_shipping_address();
        print "</p>";
    print '</div>';

    print '</div>';

    print '</div>';

    print '<script type="text/javascript">';
    print 'window.onload = function() { window.print(); }';
    print '</script>';
}
