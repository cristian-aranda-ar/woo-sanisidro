<?php
/**
 * Asigna precio 14000 a todos los productos.
 * Ejecutar UNA SOLA VEZ: /wp-admin/?sanisidro_set_prices=1
 * Eliminar este archivo luego.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function () {
	if ( ! isset( $_GET['sanisidro_set_prices'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	$products = wc_get_products( [ 'limit' => -1, 'status' => 'publish' ] );
	$updated  = 0;

	foreach ( $products as $product ) {
		$product->set_regular_price( '14000' );
		$product->set_price( '14000' );
		$product->save();
		$updated++;
	}

	wp_die( '<p style="font-family:sans-serif;padding:2rem;">✅ <strong>' . $updated . ' productos actualizados a $14.000.</strong><br>Podés eliminar <code>inc/set-prices.php</code> y el require de <code>functions.php</code>.</p>' );
} );
