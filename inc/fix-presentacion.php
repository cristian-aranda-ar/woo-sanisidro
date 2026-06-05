<?php
/**
 * Actualiza los valores del atributo Presentación.
 * Ejecutar UNA SOLA VEZ: /wp-admin/?sanisidro_fix_presentacion=1
 * Eliminar este archivo luego.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function () {
	if ( ! isset( $_GET['sanisidro_fix_presentacion'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	$map = [
		'ENV. AL VACÍO X 4 UNID.'   => 'ENVASADO AL VACÍO X 4 UNIDADES',
		'ENV. AL VACÍO X 2 UNID.'   => 'ENVASADO AL VACÍO X 2 UNIDADES',
		'ENV. AL VACÍO X 1 UNID.'   => 'ENVASADO AL VACÍO X 1 UNIDAD',
		'ENV. AL VACÍO X 1/2 UNID.' => 'ENVASADO AL VACÍO X 1/2 UNIDAD',
		'ENV. AL VACÍO X UNID.'     => 'ENVASADO AL VACÍO X UNIDAD',
	];

	$products = wc_get_products( [ 'limit' => -1, 'status' => 'publish' ] );
	$updated  = 0;

	foreach ( $products as $product ) {
		$changed = false;

		// Atributos
		$attributes = $product->get_attributes();
		foreach ( $attributes as $attribute ) {
			if ( $attribute->is_taxonomy() ) continue;
			$options     = $attribute->get_options();
			$new_options = array_map( fn( $v ) => $map[ $v ] ?? $v, $options );
			if ( $new_options !== $options ) {
				$attribute->set_options( $new_options );
				$changed = true;
			}
		}
		if ( $changed ) $product->set_attributes( $attributes );

		// Descripción corta
		$short = $product->get_short_description();
		$new_short = strtr( $short, $map );
		if ( $new_short !== $short ) {
			$product->set_short_description( $new_short );
			$changed = true;
		}

		if ( $changed ) {
			$product->save();
			$updated++;
		}
	}

	wp_die( '<p style="font-family:sans-serif;padding:2rem;">✅ <strong>' . $updated . ' productos actualizados.</strong><br>Eliminá <code>inc/fix-presentacion.php</code> y su require de <code>functions.php</code>.</p>' );
} );
