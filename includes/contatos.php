<?php

add_action( 'init', 'contacts_register_post_type', 0 );

/**
 * Register custom post type
 */
function contacts_register_post_type() {

	$labels = array(
		'name'                  => _x( 'Contatos', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Contato', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Contatos', 'text_domain' ),
		'name_admin_bar'        => __( 'Contatos', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'Ver todos', 'text_domain' ),
		'add_new_item'          => __( 'Adicionar novo', 'text_domain' ),
		'add_new'               => __( 'Adicionar novo', 'text_domain' ),
		'new_item'              => __( 'Novo', 'text_domain' ),
		'edit_item'             => __( 'Editar item', 'text_domain' ),
		'update_item'           => __( 'Atualizar item', 'text_domain' ),
		'view_item'             => __( 'Ver item', 'text_domain' ),
		'view_items'            => __( 'Ver itens', 'text_domain' ),
		'search_items'          => __( 'Pesquisar itens', 'text_domain' ),
		'not_found'             => __( 'Nenhum contato cadastrado', 'text_domain' ),
		'not_found_in_trash'    => __( 'Nenhum contato encontrado na lixeiro', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);

	$args = array(
		'label'               => __( 'Contatos', 'text_domain' ),
		'description'         => __( '', 'text_domain' ),
		'labels'              => $labels,
		'taxonomies'          => [],
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
		'can_export'          => false,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'page',
        'menu_icon'           => 'dashicons-groups',
        'supports'            => ['title']
	);
	register_post_type( 'contatos', $args );

}

add_action( 'admin_menu', 'contacts_add_submenu_page' );

/**
 * Add export page
 */
function contacts_add_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=contatos',
        'Exportar todos contatos',
        'Exportar contatos',
        'manage_options',
        'contacts_subpage',
        'contacts_subpage_render'
    );
}

function contacts_subpage_render() {

    if ( isset( $_GET['export'] ) && '1' === $_GET['export'] ) {
        contacts_export();
    }

    echo '
    <div class="wrap">
    <h1 class="wp-heading-inline">
    Exportar contatos</h1>
    <a href="edit.php?post_type=contatos&page=contacts_subpage&export=1" class="page-title-action">Clique aqui e aguarde o download dos contatos.</a>
    <hr class="wp-header-end">
    </div>';
}

add_action( 'wp_enqueue_scripts', 'contacts_enqueue_scripts' );

/**
 * Enqueue scripts and styles.
 */
function contacts_enqueue_scripts() {
    $rand = rand( 1, 9999999 );
    wp_enqueue_script( 'contacts',  get_stylesheet_directory_uri() . '/assets/js/contatos.js', ['jquery'], $rand, true );
    wp_localize_script( 'contacts', 'contacts_script_ajax_object', [
        'ajax_url'   => admin_url( 'admin-ajax.php' ),
        'ajax_nonce' => wp_create_nonce( 'contacts_ajax_nonce' )
    ]);
}

/**
 * Process ajax request.
 */
function contacts_ajax() {

    $data = $_POST;

    do_action( 'logger', $data );

    if ( 'contacts_ajax' !== $data['action'] ) {
        return;
    }

    if ( ! wp_verify_nonce( $data['nonce'], 'contacts_nonce_' . date('dmY') ) ) {
        $error = new WP_Error( 'nonce', 'Algo não está certo, tente novamente mais tarde.' );
        wp_send_json_error( $error );
    }

    if ( '' === $data['email'] ) {
        $error = new WP_Error( 'empty', 'Nenhum e-mail informado.' );
        wp_send_json_error( $error );
    }

    if ( ! is_email( $data['email'] ) ) {
        $error = new WP_Error( 'invalid', 'E-mail inválido' );
        wp_send_json_error( $error );
    }

    $check_contact = get_page_by_title( $data['email'], 'ARRAY_A', 'contatos' );

    if ( $check_contact ) {
        $error = new WP_Error( 'exist', 'O e-mail informado já está cadastrado!.' );
        wp_send_json_error( $error );
    }

    $args = [
        'post_title'  => $data['email'],
        'post_type'   => 'contatos',
        'post_status' => 'publish',
        'post_author' => 1
    ];

    $contact = wp_insert_post( $args );

    wp_send_json_success( "E-mail cadastrado com sucesso!" );

}
add_action( 'wp_ajax_contacts_ajax', 'contacts_ajax' );
add_action( 'wp_ajax_nopriv_contacts_ajax', 'contacts_ajax' );

add_shortcode('contatos', function(){
    $nonce = wp_create_nonce( 'contacts_nonce_' . date('dmY') );
    $content = '
    <div class="contacts">
        <div class="contacts-content">
            <input type="email" class="contacts-email" data-nonce="' . $nonce . '" required>
            <button class="send-email-to-contacts">' .
                __( 'Enviar', 'contacts' ) .
            '</button>
        </div>
        <div class="contacts-response"><span></span></div>
    </div>';

    return $content;
});

/**
 * Export all published contacts
 * 
 * * @since 1.0.0
 */
function contacts_export() {

    /**
     * @see     https://wordpress.org/support/article/roles-and-capabilities/
     * @todo    Create custom capability
     */
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return false;
    }

    global $wpdb;

    $post_type = 'contatos'; // define your custom post type slug here

    // A sql query to return all post titles
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $post_type ), ARRAY_A );

    // Return null if we found no results
    if ( ! $results )
        return;

    ob_clean();
    ob_start();

    $current_datetime = \sanitize_title( date( 'Ymd-His', current_time( 'timestamp', 0 ) ) );
    $blogname = \sanitize_title( get_bloginfo( 'name' ) );
    $filename = $blogname . '-export-contacts-' . $current_datetime . '.csv';

    $header_row = [
        'e-mail'
    ];

    $fh = @fopen('php://output', 'w');
    fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF));

    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-type: text/csv');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Expires: 0');
    header('Pragma: public');

    fputcsv( $fh, $header_row );
    foreach ( $results as $contact ) {
        fputcsv( $fh, $contact );
    }

    fclose( $fh );

    ob_end_flush();

    die();

}
