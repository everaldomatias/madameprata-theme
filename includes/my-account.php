<?php

/**
 *
 * Edit links on the My Account page
 *
 * @since 0.0.7
 * @version 1.0.1
 *
 */
add_filter( 'woocommerce_account_menu_items', 'remove_my_account_links', 999 );
function remove_my_account_links( $menu_links ) {
	unset( $menu_links[ 'downloads' ] );       // Disable Downloads
	unset( $menu_links[ 'wishlist' ] );        // Disable Wishlist
	unset( $menu_links[ 'customer-logout' ] ); // Disable Logout

	$menu_links[ 'edit-address' ] = __( 'Endereço', 'madame-prata' );
	$menu_links[ 'customer-logout' ] = __( 'Sair', 'madame-prata' );

	return $menu_links;
}
