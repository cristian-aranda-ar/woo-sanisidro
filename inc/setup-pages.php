<?php
/**
 * Crea las páginas Nosotros y Contacto.
 * Ejecutar UNA SOLA VEZ: /wp-admin/?sanisidro_setup_pages=1
 * Eliminar este archivo luego.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function () {
	if ( ! isset( $_GET['sanisidro_setup_pages'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	$pages = [
		[ 'title' => 'Nosotros',  'slug' => 'nosotros'  ],
		[ 'title' => 'Contacto',  'slug' => 'contacto'  ],
	];

	$created = [];
	foreach ( $pages as $p ) {
		$existing = get_page_by_path( $p['slug'] );
		if ( ! $existing ) {
			$id = wp_insert_post( [
				'post_title'  => $p['title'],
				'post_name'   => $p['slug'],
				'post_status' => 'publish',
				'post_type'   => 'page',
			] );
			$created[] = $p['title'] . ' → ' . get_permalink( $id );
		} else {
			$created[] = $p['title'] . ' ya existe → ' . get_permalink( $existing->ID );
		}
	}

	$list = implode( '<br>', array_map( 'esc_html', $created ) );
	wp_die( '<p style="font-family:sans-serif;padding:2rem;">✅ <strong>Páginas configuradas:</strong><br>' . $list . '<br><br>Eliminá <code>inc/setup-pages.php</code> y su require de <code>functions.php</code>.</p>' );
} );
