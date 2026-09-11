<?php
defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/import-recetas.php';
require_once get_template_directory() . '/inc/wc-translations.php';
require_once get_template_directory() . '/inc/set-prices.php';
require_once get_template_directory() . '/inc/fix-presentacion.php';
require_once get_template_directory() . '/inc/setup-recetas-page.php';
require_once get_template_directory() . '/inc/setup-pages.php';
require_once get_template_directory() . '/inc/order-manager/class-order-manager.php';
add_action( 'init', [ 'Sanisidro_Order_Manager', 'instance' ] );

/* ================================================================
   WOOCOMMERCE — SINGLE PRODUCT
================================================================ */
// Registrar el fragmento del side cart para que WC lo refresque automáticamente
add_filter( 'woocommerce_add_to_cart_fragments', function ( array $fragments ): array {
	ob_start();
	woocommerce_mini_cart();
	$mini = ob_get_clean();
	$fragments['div.widget_shopping_cart_content'] = '<div class="side-cart__body widget_shopping_cart_content">' . $mini . '</div>';
	return $fragments;
} );

// AJAX: actualizar cantidad / eliminar del mini carrito
add_action( 'wp_ajax_sanisidro_update_qty',        'sanisidro_handle_update_qty' );
add_action( 'wp_ajax_nopriv_sanisidro_update_qty', 'sanisidro_handle_update_qty' );
function sanisidro_handle_update_qty(): void {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'sanisidro-cart' ) ) {
		wp_send_json_error( [ 'message' => 'Nonce inválido' ] );
	}

	$key = sanitize_text_field( $_POST['key'] ?? '' );
	$qty = absint( $_POST['qty'] ?? 0 );

	if ( ! $key ) {
		wp_send_json_error( [ 'message' => 'Cart key inválida' ] );
	}

	if ( ! function_exists( 'WC' ) ) {
		wp_send_json_error( [ 'message' => 'WooCommerce no activo' ] );
	}

	if ( $qty === 0 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		WC()->cart->set_quantity( $key, $qty, true );
	}

	WC()->cart->calculate_totals();

	// Capturar el mini-cart completo
	ob_start();
	woocommerce_mini_cart();
	$full_html = ob_get_clean();

	// Extraer solo el contenido interior del .widget_shopping_cart_content
	// Si contiene el contenedor, extraer solo el contenido
	if ( preg_match( '/<div[^>]*class="[^"]*widget_shopping_cart_content[^"]*"[^>]*>(.*?)<\/div>/s', $full_html, $matches ) ) {
		$html = $matches[1];
	} else {
		$html = $full_html;
	}

	wp_send_json_success( [
		'cart_count' => WC()->cart->get_cart_contents_count(),
		'html'       => $html,
	] );
}

// Cambiar texto "Read more" → "Comprar" en el loop de productos
add_filter( 'woocommerce_product_add_to_cart_text', fn() => 'Comprar', 10, 2 );
add_filter( 'gettext', function ( $translated, $original, $domain ) {
	if ( $domain === 'woocommerce' && $original === 'Read more' ) {
		return 'Comprar';
	}
	return $translated;
}, 10, 3 );

// Eliminar el bloque de tabs completo
add_action( 'wp', function () {
	if ( ! is_singular( 'product' ) ) return;
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
} );

// Devuelve el valor (texto) de un atributo del producto por su etiqueta, o '' si no existe
function sanisidro_get_product_attribute_value( WC_Product $product, string $label ): string {
	foreach ( $product->get_attributes() as $attribute ) {
		if ( strcasecmp( wc_attribute_label( $attribute->get_name() ), $label ) !== 0 ) continue;
		$values = $attribute->is_taxonomy()
			? wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'names' ] )
			: $attribute->get_options();
		return implode( ', ', $values );
	}
	return '';
}

// Extrae el primer número (acepta coma o punto decimal) del texto de Kilaje, ej. "1,5 KG APROX" → 1.5
function sanisidro_get_product_kilaje_number( WC_Product $product ): ?float {
	$kilaje = sanisidro_get_product_attribute_value( $product, 'Kilaje' );
	if ( ! $kilaje ) return null;
	if ( ! preg_match( '/(\d+(?:[.,]\d+)?)/', $kilaje, $m ) ) return null;
	return (float) str_replace( ',', '.', $m[1] );
}

// Formatea un número al estilo argentino (coma decimal, sin ceros de más), ej. 1.5 → "1,5"
function sanisidro_format_kilaje_number( float $num ): string {
	$str = rtrim( rtrim( number_format( $num, 2, '.', '' ), '0' ), '.' );
	return str_replace( '.', ',', $str );
}

// Mostrar "Precio por pieza de {kilaje}kg · {precio base}" debajo del precio total (precio es prioridad 10)
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product instanceof WC_Product ) return;

	$kilaje = sanisidro_get_product_kilaje_number( $product );
	if ( ! $kilaje ) return;

	echo '<p class="precio-nota">Precio por pieza de ' . esc_html( sanisidro_format_kilaje_number( $kilaje ) ) . 'kg* &middot; ' . wc_price( (float) $product->get_price(), [ 'decimals' => 0 ] ) . '*</p>';
	echo '<p class="precio-disclaimer">* El peso y el precio son estimados; el valor final se muestra en el vínculo de pago.</p>';
}, 11 );

// El precio mostrado en el detalle del producto = precio ÷ kilaje (precio real de la pieza)
add_filter( 'woocommerce_get_price_html', function ( $html, $product ) {
	if ( ! is_product() || ! $product instanceof WC_Product ) return $html;
	if ( (int) $product->get_id() !== get_queried_object_id() ) return $html;

	$kilaje = sanisidro_get_product_kilaje_number( $product );
	if ( ! $kilaje ) return $html;

	$unit = '<span class="price-unit">/kg</span>';

	if ( $product->is_on_sale() && $product->get_regular_price() !== '' ) {
		$regular_total = (float) $product->get_regular_price() / $kilaje;
		$sale_total    = (float) $product->get_price() / $kilaje;
		return '<del aria-hidden="true">' . wc_price( $regular_total, [ 'decimals' => 0 ] ) . '</del> <ins>' . wc_price( $sale_total, [ 'decimals' => 0 ] ) . '</ins>' . $unit;
	}

	$total = (float) $product->get_price() / $kilaje;
	return wc_price( $total, [ 'decimals' => 0 ] ) . $unit;
}, 10, 2 );

// Mostrar atributos del producto debajo del product_meta (prioridad 45, meta es 40)
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product instanceof WC_Product ) return;
	$attributes = array_filter( $product->get_attributes(), fn( $a ) => $a->get_visible() );
	if ( empty( $attributes ) ) return;

	$presentacion_map = [
		'ENV. AL VACÍO X 4 UNID.'   => 'ENVASADO AL VACÍO X 4 UNIDADES',
		'ENV. AL VACÍO X 2 UNID.'   => 'ENVASADO AL VACÍO X 2 UNIDADES',
		'ENV. AL VACÍO X 1 UNID.'   => 'ENVASADO AL VACÍO X 1 UNIDAD',
		'ENV. AL VACÍO X 1/2 UNID.' => 'ENVASADO AL VACÍO X 1/2 UNIDAD',
		'ENV. AL VACÍO X UNID.'     => 'ENVASADO AL VACÍO X UNIDAD',
	];

	echo '<table class="product-attrs-inline">';
	foreach ( $attributes as $attribute ) {
		$label  = wc_attribute_label( $attribute->get_name() );
		$values = $attribute->is_taxonomy()
			? wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'names' ] )
			: $attribute->get_options();
		$values = array_map( fn( $v ) => $presentacion_map[ $v ] ?? $v, $values );
		echo '<tr>';
		echo '<th>' . esc_html( $label ) . '</th>';
		echo '<td>' . esc_html( implode( ', ', $values ) ) . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}, 45 );

/* ================================================================
   SETUP
================================================================ */
function sanisidro_setup(): void {
	load_theme_textdomain( 'sanisidro', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'custom-logo' );

	// WooCommerce
	add_theme_support( 'woocommerce', [
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => [
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 3,
			'min_columns'     => 1,
			'max_columns'     => 6,
		],
	] );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Menús de navegación
	register_nav_menus( [
		'primary'    => __( 'Menú principal (centro)', 'sanisidro' ),
		'secondary'  => __( 'Menú secundario (derecha)', 'sanisidro' ),
		'footer-1'   => __( 'Footer — Col 1', 'sanisidro' ),
		'footer-2'   => __( 'Footer — Col 2', 'sanisidro' ),
		'footer-3'   => __( 'Footer — Col 3', 'sanisidro' ),
		'footer-4'   => __( 'Footer — Col 4', 'sanisidro' ),
	] );
}
add_action( 'after_setup_theme', 'sanisidro_setup' );

/* ================================================================
   ASSETS
================================================================ */
function sanisidro_enqueue_assets(): void {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'sanisidro-main',
		$uri . '/assets/css/main.css',
		[],
		filemtime( $dir . '/assets/css/main.css' )
	);

	wp_enqueue_script(
		'sanisidro-animations',
		$uri . '/assets/js/animations.js',
		[],
		filemtime( $dir . '/assets/js/animations.js' ),
		true
	);

	wp_enqueue_script(
		'sanisidro-mobile-menu',
		$uri . '/assets/js/mobile-menu.js',
		[],
		filemtime( $dir . '/assets/js/mobile-menu.js' ),
		true
	);

	if ( is_woocommerce() && ! is_singular( 'product' ) ) {
		wp_enqueue_style(
			'sanisidro-archive-product',
			$uri . '/assets/css/archive-product.css',
			[],
			filemtime( $dir . '/assets/css/archive-product.css' )
		);
	}

	if ( is_singular( 'product' ) ) {
		wp_enqueue_script(
			'sanisidro-qty-stepper',
			$uri . '/assets/js/qty-stepper.js',
			[],
			filemtime( $dir . '/assets/js/qty-stepper.js' ),
			true
		);
		wp_enqueue_style(
			'sanisidro-single-product',
			$uri . '/assets/css/single-product.css',
			[],
			filemtime( $dir . '/assets/css/single-product.css' )
		);
	}

	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script(
			'sanisidro-side-cart',
			$uri . '/assets/js/side-cart.js',
			[ 'jquery' ],
			filemtime( $dir . '/assets/js/side-cart.js' ),
			true
		);
		// Localizar script DESPUÉS de encolarlo
		wp_localize_script( 'sanisidro-side-cart', 'sanisidroCart', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'sanisidro-cart' ),
		] );
	}

	if ( is_front_page() ) {
		wp_enqueue_script(
			'sanisidro-hero-slider',
			$uri . '/assets/js/hero-slider.js',
			[],
			filemtime( $dir . '/assets/js/hero-slider.js' ),
			true
		);
		wp_enqueue_script(
			'sanisidro-cat-slider',
			$uri . '/assets/js/cat-slider.js',
			[],
			filemtime( $dir . '/assets/js/cat-slider.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'sanisidro_enqueue_assets' );

/* ================================================================
   CUSTOMIZER
================================================================ */
function sanisidro_customize_register( WP_Customize_Manager $wp_customize ): void {

	// ── Redes sociales ──────────────────────────────────────────
	$wp_customize->add_section( 'sanisidro_social', [
		'title'    => __( 'Redes Sociales', 'sanisidro' ),
		'priority' => 120,
	] );
	foreach ( [ 'x' => 'X (Twitter)', 'facebook' => 'Facebook', 'instagram' => 'Instagram' ] as $slug => $label ) {
		$wp_customize->add_setting( "sanisidro_{$slug}_url", [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
		$wp_customize->add_control( "sanisidro_{$slug}_url", [ 'label' => $label . ' URL', 'section' => 'sanisidro_social', 'type' => 'url' ] );
	}
	$wp_customize->add_setting( 'sanisidro_social_handle', [ 'default' => '@SANISIDRO', 'sanitize_callback' => 'sanitize_text_field' ] );
	$wp_customize->add_control( 'sanisidro_social_handle', [ 'label' => 'Handle redes', 'section' => 'sanisidro_social', 'type' => 'text' ] );

	// ── Contacto footer ─────────────────────────────────────────
	$wp_customize->add_section( 'sanisidro_contacto', [
		'title'    => __( 'Contacto (footer)', 'sanisidro' ),
		'priority' => 121,
	] );
	$campos = [
		'sanisidro_footer_sitio'     => [ 'label' => 'Sitio web',  'default' => 'SANISIDRO.COM.AR' ],
		'sanisidro_footer_direccion' => [ 'label' => 'Dirección',  'default' => '' ],
		'sanisidro_footer_telefono'  => [ 'label' => 'Teléfono',   'default' => '' ],
		'sanisidro_footer_email'     => [ 'label' => 'Email',       'default' => '' ],
	];
	foreach ( $campos as $id => $args ) {
		$wp_customize->add_setting( $id, [ 'default' => $args['default'], 'sanitize_callback' => 'sanitize_text_field' ] );
		$wp_customize->add_control( $id, [ 'label' => $args['label'], 'section' => 'sanisidro_contacto', 'type' => 'text' ] );
	}

	// ── Títulos columnas footer ──────────────────────────────────
	$wp_customize->add_section( 'sanisidro_footer_cols', [
		'title'    => __( 'Footer — Títulos columnas', 'sanisidro' ),
		'priority' => 122,
	] );
	$titulos = [
		'sanisidro_footer_title_1' => 'Sobre nosotros',
		'sanisidro_footer_title_2' => 'Tienda',
		'sanisidro_footer_title_3' => 'Legales',
		'sanisidro_footer_title_4' => 'Políticas',
	];
	foreach ( $titulos as $id => $default ) {
		$wp_customize->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field' ] );
		$wp_customize->add_control( $id, [ 'label' => $id, 'section' => 'sanisidro_footer_cols', 'type' => 'text' ] );
	}
}

/* ================================================================
   WOOCOMMERCE — CHECKOUT
================================================================ */
// Renombrar "Phone" → "Teléfono (WhatsApp)" y marcar el email como "(Opcional)"
// (afecta Mi cuenta, emails y el checkout con bloques)
add_filter( 'gettext', function ( $translated, $original, $domain ) {
	if ( $domain !== 'woocommerce' ) return $translated;
	if ( $original === 'Phone' ) return 'Teléfono (WhatsApp)';
	if ( $original === 'Email address' ) return $translated . ' (Opcional)';
	return $translated;
}, 10, 3 );

// Quitar País, Provincia y Código postal en las direcciones de Mi cuenta (checkout clásico)
add_filter( 'woocommerce_default_address_fields', function ( array $fields ): array {
	unset( $fields['country'], $fields['state'], $fields['postcode'] );
	return $fields;
} );

// Ocultar País/Provincia/Código postal en el checkout y completarlos con un valor fijo
// (el negocio solo opera con retiro local en Misiones, no calcula envío por dirección)
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_checkout() ) return;
	$uri = get_template_directory_uri();
	$dir = get_template_directory();
	wp_enqueue_script(
		'sanisidro-checkout-fields',
		$uri . '/assets/js/checkout-fields.js',
		[],
		filemtime( $dir . '/assets/js/checkout-fields.js' ),
		true
	);
} );

// Envío según ciudad: Garupá siempre gratis; Posadas/Candelaria gratis desde $100.000,
// por debajo de ese monto el envío queda "a confirmar" junto con el link de pago.
add_filter( 'woocommerce_package_rates', function ( array $rates, array $package ): array {
	if ( ! WC()->customer ) return $rates;

	$city = trim( (string) WC()->customer->get_shipping_city() );
	if ( ! $city ) return $rates;

	$city = function_exists( 'mb_strtolower' ) ? mb_strtolower( $city, 'UTF-8' ) : strtolower( $city );
	$subtotal = (float) WC()->cart->get_subtotal();

	foreach ( $rates as $rate_id => $rate ) {
		if ( strpos( $rate_id, 'flat_rate' ) !== 0 ) continue;

		if ( in_array( $city, [ 'garupá', 'garupa' ], true ) ) {
			$rate->cost = 0;
			$rate->label = 'Envío gratis';
		} elseif ( in_array( $city, [ 'posadas', 'candelaria' ], true ) ) {
			if ( $subtotal >= 100000 ) {
				$rate->cost = 0;
				$rate->label = 'Envío gratis';
			} else {
				$rate->cost = 0;
				$rate->label = 'Envío a confirmar (se informa junto al link de pago)';
			}
		}
	}

	return $rates;
}, 100, 2 );

add_action( 'customize_register', 'sanisidro_customize_register' );
