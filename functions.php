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
	wp_enqueue_style( 'betheme-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'madameprata-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'betheme-style' ]
	);
}

/**
 * Add woo search on home
 */
function mp_home_search() {

	if ( is_front_page()) {
		echo '<div class="container woo-home-search">';
			get_product_search_form();
		echo '</div><!-- /.container.woo-home-search -->';
	}

}
add_action( 'mfn_hook_content_before', 'mp_home_search' );