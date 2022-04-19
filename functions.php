<?php
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