<?php

include dirname( __FILE__ ) . '/includes/settings.php';
include dirname( __FILE__ ) . '/includes/woocommerce-functions.php';

/**
 * Madame Prata Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package madameprata
 */

add_action( 'wp_enqueue_scripts', 'betheme_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function betheme_parent_theme_enqueue_styles() {
	$rand = rand( 1, 9999999 );
	wp_enqueue_style( 'betheme-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'madameprata-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'betheme-style' ],
		$rand
	);
}

/**
 * Enqueue admin styles.
 */
function betheme_admin_enqueue_styles() {
	$rand = rand( 1, 9999999 );
	$get_stylesheet_directory_uri = get_stylesheet_directory_uri();
	wp_enqueue_style( 'madameprata-style-admin', $get_stylesheet_directory_uri . '/admin.css', [], $rand );
	wp_enqueue_style( 'madameprata-print-admin', $get_stylesheet_directory_uri . '/print.css', [], $rand, 'print' );
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