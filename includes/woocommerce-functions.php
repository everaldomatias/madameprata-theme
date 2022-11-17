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

            print "<h3>Informações do cliente</h3>";

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

        print "<h3>Detalhes do pedido</h3>";
        print "<span>";
        echo 'Número do pedido: ' . $order_id;
        print "</span>";

        print "<span>";
        echo 'Data do pedido: ' . date( 'd\/m\/Y', wc_string_to_timestamp( $order->get_date_created() ) );
        print "</span>";

        print "<span>";
        echo 'Método de pagamento: ' . $order->get_payment_method_title();
        print "</span>";

    print '</div>';

    print '</div>';
    ?>

    <table class="order-details">
        <thead>
            <tr>
                <th class="product"><span>Produto</span></th>
                <th class="quantity"><span>Quantidade</span></th>
                <th class="price"><span>Preço</span></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $order->get_items() as $item_id => $item ) :
                $product = $item->get_product(); ?>
                <tr>
                    <td class="product">
                        <?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
                        <span class="item-name"><?php echo $item['name']; ?></span>
                        <dl class="meta">
                            <?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
                            <?php if ( ! empty( $product->get_sku() ) ) : ?><dt class="sku">SKU:</dt><dd class="sku"><?php echo $product->get_sku(); ?></dd><?php endif; ?>
                            <?php if ( ! empty( $product->get_weight() ) ) : ?><dt class="weight">Peso:</dt><dd class="weight"><?php echo nl2br( $product->get_weight() ); ?><?php echo get_option( 'woocommerce_weight_unit' ); ?></dd><?php endif; ?>
                        </dl>
                    </td>
                    <td class="quantity"><?php echo $item['quantity']; ?></td>
                    <td class="price"><?php echo $product->get_price_html(); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="no-borders">
                <td class="no-borders">
                </td>
                <td class="no-borders" colspan="2">
                    <table class="totals">
                        <tfoot>
                            <?php foreach ( get_woocommerce_totals( $order ) as $key => $total ) : ?>
                                <tr class="<?php echo $key; ?>">
                                    <th class="description"><?php echo $total['label']; ?></th>
                                    <td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tfoot>
    </table>

    <?php

    print '<script type="text/javascript">';
    print 'window.onload = function() { window.print(); }';
    print '</script>';
}


/**
 * Return the order totals listing
 */
function get_woocommerce_totals( $order ) {
    $totals = $order->get_order_item_totals();

    foreach ( $totals as $key => $total ) {
        $label = $total['label'];
        $colon = strrpos( $label, ':' );
        if( $colon !== false ) {
            $label = substr_replace( $label, '', $colon, 1 );
        }
        $totals[$key]['label'] = $label;
    }

    return $totals;
}

/**
 * Add parceled info on single
 */
function add_parceled_price() {
    echo do_shortcode( '[product_parceled_loop]' );
}

add_action( 'woocommerce_before_add_to_cart_form', 'add_parceled_price', 1 );


function add_woocommerce_register_form_start_message() {
	echo '<p class="woocommerce_register_form_start_message">Caso já possua cadastro conosco, faça o seu login abaixo.</p>';
}

add_action( 'woocommerce_register_form_start', 'add_woocommerce_register_form_start_message' );
