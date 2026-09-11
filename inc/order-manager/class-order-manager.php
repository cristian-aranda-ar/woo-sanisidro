<?php
defined( 'ABSPATH' ) || exit;

class Sanisidro_Order_Manager {

	/** @var self|null */
	private static $instance = null;

	/** @var string */
	private $dir;

	/** @var string */
	private $url;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->dir = get_template_directory() . '/inc/order-manager/';
		$this->url = get_template_directory_uri() . '/inc/order-manager/';

		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_ajax_sanisidro_om_save_peso', [ $this, 'ajax_save_peso' ] );
		add_action( 'wp_ajax_sanisidro_om_save_envio', [ $this, 'ajax_save_envio' ] );
		add_action( 'wp_ajax_sanisidro_om_generar_whatsapp', [ $this, 'ajax_generar_whatsapp' ] );
		add_action( 'template_redirect', [ $this, 'maybe_render_public_order_page' ] );
	}

	public function register_menu(): void {
		add_submenu_page(
			'woocommerce',
			__( 'Gestor de Pedidos', 'sanisidro' ),
			__( 'Gestor de Pedidos', 'sanisidro' ),
			'manage_woocommerce',
			'sanisidro-gestor-pedidos',
			[ $this, 'render_page' ]
		);
	}

	public function enqueue_assets( string $hook ): void {
		if ( strpos( $hook, 'sanisidro-gestor-pedidos' ) === false ) return;

		wp_enqueue_style( 'sanisidro-om-admin', $this->url . 'assets/admin.css', [], filemtime( $this->dir . 'assets/admin.css' ) );
		wp_enqueue_script( 'sanisidro-om-admin', $this->url . 'assets/admin.js', [], filemtime( $this->dir . 'assets/admin.js' ), true );
		wp_localize_script( 'sanisidro-om-admin', 'sanisidroOM', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'sanisidro_om_nonce' ),
			'strings' => [
				'generando'     => __( 'Generando…', 'sanisidro' ),
				'abrirWhatsapp' => __( 'Abrir WhatsApp', 'sanisidro' ),
				'sinTelefono'   => __( 'El pedido no tiene un teléfono cargado.', 'sanisidro' ),
				'error'         => __( 'No se pudo generar el link. Reintentá.', 'sanisidro' ),
			],
		] );
	}

	/**
	 * Devuelve el valor numérico del atributo "Kilaje" del producto, ej. "1,5 KG APROX" → 1.5
	 */
	private function get_kilaje_number( WC_Product $product ): ?float {
		foreach ( $product->get_attributes() as $attribute ) {
			if ( strcasecmp( wc_attribute_label( $attribute->get_name() ), 'Kilaje' ) !== 0 ) continue;

			$values = $attribute->is_taxonomy()
				? wc_get_product_terms( $product->get_id(), $attribute->get_name(), [ 'fields' => 'names' ] )
				: $attribute->get_options();

			$raw = $values[0] ?? '';
			if ( $raw && preg_match( '/(\d+(?:[.,]\d+)?)/', $raw, $m ) ) {
				return (float) str_replace( ',', '.', $m[1] );
			}
		}
		return null;
	}

	/**
	 * Agrupa los ítems del pedido por producto: cantidad, precio/kg (a partir del precio
	 * cobrado en el pedido dividido el atributo Kilaje) y el peso real cargado por el admin.
	 *
	 * @return array<int, array<string, mixed>> Indexado por product_id.
	 */
	private function get_order_product_groups( WC_Order $order ): array {
		$groups = [];

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) continue;

			$product_id = $item->get_product_id();
			if ( ! isset( $groups[ $product_id ] ) ) {
				$product = $item->get_product();
				$groups[ $product_id ] = [
					'product_id' => $product_id,
					'name'       => $item->get_name(),
					'quantity'   => 0,
					'total'      => 0.0,
					'kilaje'     => $product ? $this->get_kilaje_number( $product ) : null,
				];
			}

			$groups[ $product_id ]['quantity'] += $item->get_quantity();
			$groups[ $product_id ]['total']    += (float) $item->get_total();
		}

		foreach ( $groups as $product_id => &$group ) {
			$precio_unitario        = $group['quantity'] > 0 ? $group['total'] / $group['quantity'] : 0.0;
			$group['precio_por_kg'] = $group['kilaje'] ? $precio_unitario / $group['kilaje'] : null;
			$group['peso_real']     = (float) $order->get_meta( '_sanisidro_peso_' . $product_id );
			$group['total_real']    = $group['precio_por_kg'] ? $group['peso_real'] * $group['precio_por_kg'] : 0.0;
			unset( $group );
		}

		return $groups;
	}

	private function get_order_subtotal( WC_Order $order ): float {
		return array_sum( wp_list_pluck( $this->get_order_product_groups( $order ), 'total_real' ) );
	}

	private function get_shipping_cost( WC_Order $order ): float {
		return (float) $order->get_meta( '_sanisidro_costo_envio' );
	}

	public function ajax_save_peso(): void {
		check_ajax_referer( 'sanisidro_om_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'forbidden', 403 );
		}

		$order_id   = absint( $_POST['order_id'] ?? 0 );
		$product_id = absint( $_POST['product_id'] ?? 0 );
		$peso_raw   = isset( $_POST['peso'] ) ? sanitize_text_field( wp_unslash( $_POST['peso'] ) ) : '0';
		$peso       = (float) str_replace( ',', '.', $peso_raw );

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( 'order_not_found', 404 );
		}

		$order->update_meta_data( '_sanisidro_peso_' . $product_id, $peso );
		$order->save();

		$groups   = $this->get_order_product_groups( $order );
		$group    = $groups[ $product_id ] ?? null;
		$subtotal = array_sum( wp_list_pluck( $groups, 'total_real' ) );
		$total    = $subtotal + $this->get_shipping_cost( $order );

		wp_send_json_success( [
			'total_real_html' => $group ? wc_price( $group['total_real'] ) : wc_price( 0 ),
			'subtotal_html'   => wc_price( $subtotal ),
			'total_html'      => wc_price( $total ),
		] );
	}

	public function ajax_save_envio(): void {
		check_ajax_referer( 'sanisidro_om_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'forbidden', 403 );
		}

		$order_id  = absint( $_POST['order_id'] ?? 0 );
		$envio_raw = isset( $_POST['envio'] ) ? sanitize_text_field( wp_unslash( $_POST['envio'] ) ) : '0';
		$envio     = (float) str_replace( ',', '.', $envio_raw );

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( 'order_not_found', 404 );
		}

		$order->update_meta_data( '_sanisidro_costo_envio', $envio );
		$order->save();

		$total = $this->get_order_subtotal( $order ) + $envio;

		wp_send_json_success( [
			'total_html' => wc_price( $total ),
		] );
	}

	/**
	 * Normaliza un teléfono argentino al formato que espera wa.me (54 9 + código de área + número).
	 */
	private function normalize_whatsapp_phone( string $raw ): ?string {
		$digits = preg_replace( '/\D+/', '', $raw );
		if ( ! $digits ) return null;
		if ( strpos( $digits, '54' ) === 0 ) return $digits;
		return '549' . ltrim( $digits, '0' );
	}

	private function get_mp_access_token(): ?string {
		$is_test = get_option( 'checkbox_checkout_test_mode', 'yes' ) === 'yes';
		$token   = $is_test ? get_option( '_mp_access_token_test' ) : get_option( '_mp_access_token_prod' );
		return $token ?: null;
	}

	/**
	 * Crea (o reutiliza si el total no cambió) una preferencia de pago de Mercado Pago
	 * por el monto real del pedido y devuelve el link de pago (init_point).
	 */
	private function get_or_create_mp_link( WC_Order $order, float $total ): ?string {
		if ( $total <= 0 ) return null;

		$cached_total = (float) $order->get_meta( '_sanisidro_mp_link_total' );
		$cached_link  = $order->get_meta( '_sanisidro_mp_link' );

		if ( $cached_link && abs( $cached_total - $total ) < 0.005 ) {
			return $cached_link;
		}

		$access_token = $this->get_mp_access_token();
		if ( ! $access_token ) return null;

		$is_test = get_option( 'checkbox_checkout_test_mode', 'yes' ) === 'yes';

		$response = wp_remote_post( 'https://api.mercadopago.com/checkout/preferences', [
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
				'Content-Type'  => 'application/json',
			],
			'timeout' => 20,
			'body'    => wp_json_encode( [
				'items'              => [ [
					'title'       => sprintf(
						/* translators: %s: order number */
						__( 'Pedido #%s — Campo San Isidro', 'sanisidro' ),
						$order->get_order_number()
					),
					'quantity'    => 1,
					'unit_price'  => round( $total, 2 ),
					'currency_id' => 'ARS',
				] ],
				'external_reference' => (string) $order->get_id(),
				'back_urls'          => [
					'success' => $order->get_checkout_order_received_url(),
					'pending' => $order->get_checkout_order_received_url(),
					'failure' => $order->get_checkout_order_received_url(),
				],
				'auto_return'        => 'approved',
			] ),
		] );

		if ( is_wp_error( $response ) ) return null;

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$link = $is_test ? ( $data['sandbox_init_point'] ?? null ) : ( $data['init_point'] ?? null );
		if ( ! $link ) return null;

		$order->update_meta_data( '_sanisidro_mp_link', $link );
		$order->update_meta_data( '_sanisidro_mp_link_total', $total );
		$order->save();

		return $link;
	}

	private function get_public_order_url( WC_Order $order ): string {
		return add_query_arg(
			[ 'pedido' => $order->get_id(), 'codigo' => $order->get_order_key() ],
			home_url( '/' )
		);
	}

	public function ajax_generar_whatsapp(): void {
		check_ajax_referer( 'sanisidro_om_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( 'forbidden', 403 );
		}

		$order_id = absint( $_POST['order_id'] ?? 0 );
		$order    = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( 'order_not_found', 404 );
		}

		$total = $this->get_order_subtotal( $order ) + $this->get_shipping_cost( $order );

		// Se genera (o reutiliza) el link de pago para que ya esté listo cuando el cliente lo abra.
		$this->get_or_create_mp_link( $order, $total );

		$phone = $this->normalize_whatsapp_phone( $order->get_billing_phone() ?: $order->get_shipping_phone() );
		if ( ! $phone ) {
			wp_send_json_error( 'no_phone', 422 );
		}

		$pedido_url = $this->get_public_order_url( $order );

		$mensaje = sprintf(
			/* translators: 1: order number, 2: URL to the public order/payment page */
			__( 'Campo San Isidro - Para abonar su pedido #%1$s diríjase a: %2$s', 'sanisidro' ),
			$order->get_order_number(),
			$pedido_url
		);

		wp_send_json_success( [
			'whatsapp_url' => 'https://wa.me/' . $phone . '?text=' . rawurlencode( $mensaje ),
			'pedido_url'   => esc_url_raw( $pedido_url ),
		] );
	}

	/**
	 * Página pública (sin login) del pedido: /?pedido=ID&codigo=order_key
	 * Muestra productos, pesajes, subtotal, envío, total y el link de pago de Mercado Pago.
	 */
	public function maybe_render_public_order_page(): void {
		if ( ! isset( $_GET['pedido'], $_GET['codigo'] ) ) return;

		$order_id = absint( $_GET['pedido'] );
		$codigo   = sanitize_text_field( wp_unslash( $_GET['codigo'] ) );
		$order    = $order_id ? wc_get_order( $order_id ) : false;

		if ( ! $order instanceof WC_Order || ! hash_equals( $order->get_order_key(), $codigo ) ) {
			wp_die( esc_html__( 'Pedido no encontrado o código inválido.', 'sanisidro' ), '', [ 'response' => 404 ] );
		}

		$groups   = $this->get_order_product_groups( $order );
		$subtotal = array_sum( wp_list_pluck( $groups, 'total_real' ) );
		$envio    = $this->get_shipping_cost( $order );
		$total    = $subtotal + $envio;
		$mp_link  = $this->get_or_create_mp_link( $order, $total );

		$direccion_envio = trim( ( $order->get_shipping_address_1() ?: $order->get_billing_address_1() ) );
		$ciudad_envio    = $order->get_shipping_city() ?: $order->get_billing_city();

		nocache_headers();
		status_header( 200 );
		include $this->dir . 'view-public-order.php';
		exit;
	}

	public function render_page(): void {
		$paged    = max( 1, absint( $_GET['paged'] ?? 1 ) );
		$per_page = 20;

		$orders = wc_get_orders( [
			'limit'    => $per_page,
			'page'     => $paged,
			'orderby'  => 'date',
			'order'    => 'DESC',
			'paginate' => true,
		] );

		include $this->dir . 'view-orders-list.php';
	}
}
