<?php
/**
 * Página pública del pedido (sin login), accedida vía /?pedido=ID&codigo=order_key.
 * Se incluye desde Sanisidro_Order_Manager::maybe_render_public_order_page(), con $order,
 * $groups, $subtotal, $envio, $total, $mp_link, $direccion_envio y $ciudad_envio disponibles.
 *
 * @var WC_Order $order
 * @var array    $groups
 * @var float    $subtotal
 * @var float    $envio
 * @var float    $total
 * @var string|null $mp_link
 * @var string   $direccion_envio
 * @var string   $ciudad_envio
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title><?php printf( esc_html__( 'Pedido #%s — Campo San Isidro', 'sanisidro' ), esc_html( $order->get_order_number() ) ); ?></title>
	<style>
		:root {
			--negro: #131313;
			--primario: #ff8d6a;
			--gris: #edebea;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
			background: #f7f6f5;
			color: var(--negro);
			line-height: 1.5;
		}
		.wrap {
			max-width: 560px;
			margin: 0 auto;
			padding: 32px 20px 56px;
		}
		.brand {
			text-align: center;
			margin: 0 0 24px;
		}
		.brand img {
			display: inline-block;
			width: 140px;
			height: auto;
			filter: invert(1); /* el logo es blanco; se invierte para fondos claros, igual que en el header del sitio */
		}
		h1 {
			font-size: 1.375rem;
			margin: 0 0 4px;
		}
		.sub {
			color: rgba(19,19,19,0.55);
			font-size: 0.875rem;
			margin: 0 0 24px;
		}
		.card {
			background: #fff;
			border: 1px solid var(--gris);
			border-radius: 12px;
			padding: 20px;
			margin-bottom: 16px;
		}
		.card h2 {
			font-size: 0.8125rem;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			color: rgba(19,19,19,0.5);
			margin: 0 0 14px;
		}
		table { width: 100%; border-collapse: collapse; }
		td { padding: 8px 0; vertical-align: top; }
		.producto-nombre { font-weight: 600; }
		.producto-meta { font-size: 0.8125rem; color: rgba(19,19,19,0.55); }
		.num { text-align: right; white-space: nowrap; }
		.linea { border-top: 1px solid var(--gris); }
		.linea td { padding-top: 12px; }
		.total-row td { padding-top: 14px; font-size: 1.125rem; font-weight: 700; }
		.direccion { font-size: 0.9375rem; }
		.pagar {
			display: block;
			text-align: center;
			background: var(--primario);
			color: #fff;
			text-decoration: none;
			font-weight: 700;
			letter-spacing: 0.03em;
			text-transform: uppercase;
			font-size: 0.9375rem;
			padding: 16px;
			border-radius: 10px;
		}
		.pagar:hover { opacity: 0.9; }
		.aviso {
			background: var(--gris);
			border-radius: 10px;
			padding: 16px;
			font-size: 0.875rem;
			text-align: center;
			color: rgba(19,19,19,0.7);
		}
		.disclaimer {
			font-size: 0.75rem;
			color: rgba(19,19,19,0.45);
			text-align: center;
			margin-top: 20px;
		}
	</style>
</head>
<body>
	<div class="wrap">
		<p class="brand">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sic.svg' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="140" height="48">
		</p>
		<h1><?php printf( esc_html__( 'Pedido #%s', 'sanisidro' ), esc_html( $order->get_order_number() ) ); ?></h1>
		<p class="sub"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></p>

		<div class="card">
			<h2><?php esc_html_e( 'Productos', 'sanisidro' ); ?></h2>
			<table>
				<?php foreach ( $groups as $group ) : ?>
				<tr>
					<td>
						<div class="producto-nombre"><?php echo esc_html( $group['name'] ); ?></div>
						<div class="producto-meta">
							<?php
							if ( $group['peso_real'] > 0 ) {
								printf(
									/* translators: %s: weight in kilograms */
									esc_html__( '%s kg', 'sanisidro' ),
									esc_html( number_format_i18n( $group['peso_real'], 3 ) )
								);
							} else {
								esc_html_e( 'Pesaje a confirmar', 'sanisidro' );
							}
							?>
						</div>
					</td>
					<td class="num"><?php echo wp_kses_post( wc_price( $group['total_real'] ) ); ?></td>
				</tr>
				<?php endforeach; ?>
				<tr class="linea">
					<td><?php esc_html_e( 'Subtotal', 'sanisidro' ); ?></td>
					<td class="num"><?php echo wp_kses_post( wc_price( $subtotal ) ); ?></td>
				</tr>
				<tr>
					<td>
						<?php esc_html_e( 'Costo de envío', 'sanisidro' ); ?>
						<?php if ( $direccion_envio ) : ?>
							<div class="producto-meta direccion">
								<?php echo esc_html( trim( $direccion_envio . ( $ciudad_envio ? ', ' . $ciudad_envio : '' ), ', ' ) ); ?>
							</div>
						<?php endif; ?>
					</td>
					<td class="num"><?php echo $envio > 0 ? wp_kses_post( wc_price( $envio ) ) : esc_html__( 'Gratis', 'sanisidro' ); ?></td>
				</tr>
				<tr class="linea total-row">
					<td><?php esc_html_e( 'Total', 'sanisidro' ); ?></td>
					<td class="num"><?php echo wp_kses_post( wc_price( $total ) ); ?></td>
				</tr>
			</table>
		</div>

		<?php if ( $mp_link ) : ?>
			<a class="pagar" href="<?php echo esc_url( $mp_link ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Pagar con Mercado Pago', 'sanisidro' ); ?>
			</a>
		<?php else : ?>
			<p class="aviso"><?php esc_html_e( 'Todavía estamos preparando tu link de pago. Escribinos por WhatsApp si ya pasó un rato.', 'sanisidro' ); ?></p>
		<?php endif; ?>

		<p class="disclaimer">
			<?php esc_html_e( 'El peso y el precio son estimados; el valor final es el que ves en esta página.', 'sanisidro' ); ?>
		</p>
	</div>
</body>
</html>
