<?php

define( 'MADAMEPRATA_VERSION', '0.3.7' );

/**
 * To develop, change version to random number
 */
// define( 'MADAMEPRATA_VERSION', rand( 1, 9999999 ) );

include dirname( __FILE__ ) . '/includes/admin.php';
include dirname( __FILE__ ) . '/includes/contatos.php';
include dirname( __FILE__ ) . '/includes/settings.php';
include dirname( __FILE__ ) . '/includes/woocommerce-functions.php';
include dirname( __FILE__ ) . '/includes/my-account.php';

/**
 * Madame Prata Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package madameprata
 */

add_action( 'wp_enqueue_scripts', 'betheme_parent_theme_enqueue_styles', 100 );

/**
 * Enqueue scripts and styles.
 */
function betheme_parent_theme_enqueue_styles() {
	$rand = rand( 1, 9999999 );
	wp_enqueue_style( 'betheme-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'madameprata-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'betheme-style' ],
		MADAMEPRATA_VERSION
	);

	if ( is_singular( 'product' ) ) {
		wp_enqueue_script( 'custom-woocommerce',  get_stylesheet_directory_uri() . '/assets/js/woo.js', [], MADAMEPRATA_VERSION, true );

		$product = wc_get_product( get_the_ID() );
		$woo_payment_discounts_setting = get_option( 'woo_payment_discounts_setting' );
		$woo_payment_discounts_setting = maybe_unserialize( $woo_payment_discounts_setting );
		$percentage = '';

		if ( isset( $woo_payment_discounts_setting['wc_piggly_pix_gateway'] ) && ! empty( $woo_payment_discounts_setting['wc_piggly_pix_gateway'] ) ) {
			if ( $woo_payment_discounts_setting['wc_piggly_pix_gateway']['type'] == 'percentage' ) {
				$percentage = $woo_payment_discounts_setting['wc_piggly_pix_gateway']['amount'];
			}
		}

		if ( $product && ! is_wp_error( $product ) ) {
			/**
			 * Print price and discount on HTML
			 */
			wp_localize_script( 'custom-woocommerce', 'priceAndDiscount', [
				'price'    => $product->get_price(),
				'discount' => $percentage
			]);
		}

		/**
		 * Print stock on HTML of the product variable
		 */
		if ( $product && ! is_wp_error( $product ) ) {
			if ( $product->is_type( 'variable' ) ) {

				$product_stock = mp_print_product_stock( get_the_ID() );

				wp_localize_script( 'custom-woocommerce', 'productStock', [
						'productType' => 'variable',
						'stock'       => $product_stock
					]
				);
			}
		}
	}

	wp_enqueue_script( 'custom-header',  get_stylesheet_directory_uri() . '/assets/js/header.js', [], MADAMEPRATA_VERSION, true );
}

/**
 * Enqueue admin styles.
 */
function betheme_admin_enqueue_styles() {
	$get_stylesheet_directory_uri = get_stylesheet_directory_uri();
	wp_enqueue_style( 'madameprata-style-admin', $get_stylesheet_directory_uri . '/admin.css', [], MADAMEPRATA_VERSION );
	wp_enqueue_style( 'madameprata-print-admin', $get_stylesheet_directory_uri . '/print.css', [], MADAMEPRATA_VERSION, 'print' );
}

add_action( 'admin_enqueue_scripts', 'betheme_admin_enqueue_styles' );

/**
 * Add woo search on home
 */
function mp_home_search() {

	if ( is_front_page()) {
		echo '<div class="woo-home-search">';
			echo '<div class="container">';
				get_product_search_form();
			echo '</div>';
		echo '</div><!-- /.container.woo-home-search -->';
	}

}
add_action( 'mfn_hook_content_before', 'mp_home_search' );

/**
 * Hide shipping rates when free shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function mp_hide_shipping_when_free_is_available( $rates, $package ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'mp_hide_shipping_when_free_is_available', 100, 2 );

/**
 * Change URL of the tracking code Correios
 */
function mp_change_tracking_code_url( $url, $tracking_code, $object ) {
	if ( class_exists( 'WC_Correios' ) ) {
		$url = sprintf( '<a href="https://rastreamento.correios.com.br/app/index.php">%s</a>', $tracking_code );
	}
	return $url;
}
add_filter( 'woocommerce_correios_email_tracking_core_url', 'mp_change_tracking_code_url', 10, 3 );

/**
 * Print array with stock products
 */
function mp_print_product_stock( $product_id ) {
	$product          = wc_get_product( $product_id );
	$variations_stock = [];

	if ( $product->is_type( 'variable' ) ) {
		$variations = $product->get_available_variations();

		foreach ( $variations as $variation ) {
			$variation_o = new WC_Product_Variation( $variation['variation_id'] );
			$variations_stock[implode( " / ", $variation_o->get_variation_attributes() )] = $variation_o->get_stock_quantity();
		}
	}

	return $variations_stock;
}

/**
 * Print class on hook body_class to woocommerce
 */
function mp_add_woocommerce_body_class( $classes ) {
	if ( is_singular( 'product' ) ) {

		$product = wc_get_product( get_the_ID() );

		if ( $product->is_type( 'variable' ) ) {
			$classes[] = 'woocommerce-type-variable';
		} else {
			$classes[] = 'woocommerce-type-simple';
		}

		if ( $product->is_in_stock() ) {
			$classes[] = 'woocommerce-in-stock';
		} else {
			$classes[] = 'woocommerce-out-stock';
		}

	}

	return $classes;
}

add_filter( 'body_class', 'mp_add_woocommerce_body_class' );

function mp_search_filter( $query ) {
	global $wpdb;
	if ( $query->is_search() && ! is_admin() ) {
		if ( isset( $query->query['s'] ) && ! empty( $query->query['s'] ) ) {
			$like = '%' . $wpdb->esc_like( sanitize_text_field( $query->query['s'] ) ) . '%';
			$prepare = $wpdb->prepare(
				"SELECT p.ID
				FROM {$wpdb->prefix}posts AS p
				WHERE p.post_type = %s
					AND p.post_status = 'publish'
					AND (p.post_excerpt LIKE %s OR p.post_title LIKE %s OR p.post_content LIKE %s)",
				'product',
				$like,
				$like,
				$like
			);

			$results = $wpdb->get_results( $prepare );

			$post_ids = [];

			foreach ( $results as $result ) {
				$post_ids[] = $result->ID;
			}

			if ( count( $post_ids ) ) {
				$query->set( 'post__in', $post_ids );
				$query->set( 'posts_per_page', 12 );
			}
		}
	}
}

add_action( 'pre_get_posts', 'mp_search_filter', 99 );
