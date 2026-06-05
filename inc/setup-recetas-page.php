<?php
/**
 * Crea la página "Recetas" y la asigna como Página de entradas.
 * Ejecutar UNA SOLA VEZ: /wp-admin/?sanisidro_setup_recetas=1
 * Eliminar este archivo luego.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function () {
	if ( ! isset( $_GET['sanisidro_setup_recetas'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	// Buscar si ya existe la página
	$existing = get_page_by_path( 'recetas' );
	if ( $existing ) {
		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post( [
			'post_title'  => 'Recetas',
			'post_name'   => 'recetas',
			'post_status' => 'publish',
			'post_type'   => 'page',
		] );
	}

	if ( is_wp_error( $page_id ) ) {
		wp_die( 'Error al crear la página.' );
	}

	// Asignar como página de entradas en Ajustes → Lectura
	update_option( 'page_for_posts', $page_id );

	// Asegurar que haya una página estática como portada
	if ( ! get_option( 'page_on_front' ) ) {
		$front = get_page_by_path( 'inicio' );
		if ( ! $front ) {
			$front_id = wp_insert_post( [
				'post_title'  => 'Inicio',
				'post_name'   => 'inicio',
				'post_status' => 'publish',
				'post_type'   => 'page',
			] );
		} else {
			$front_id = $front->ID;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_id );
	}

	wp_die( '<p style="font-family:sans-serif;padding:2rem;">✅ <strong>Página "Recetas" creada y configurada como Página de entradas.</strong><br>URL: <a href="' . esc_url( get_permalink( $page_id ) ) . '">' . esc_url( get_permalink( $page_id ) ) . '</a><br><br>Eliminá <code>inc/setup-recetas-page.php</code> y su require de <code>functions.php</code>.</p>' );
} );
