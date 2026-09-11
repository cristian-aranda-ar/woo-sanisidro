<?php
/**
 * Vista: lista de pedidos + panel desplegable por pedido.
 * Se incluye desde Sanisidro_Order_Manager::render_page(), por lo que $this y $orders están disponibles.
 *
 * @var WC_Order $order
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap sanisidro-om">
	<h1><?php esc_html_e( 'Gestor de Pedidos', 'sanisidro' ); ?></h1>
	<p class="sanisidro-om-intro">
		<?php esc_html_e( 'Desplegá un pedido para cargar el pesaje real de cada producto y ver el precio final calculado por kilo.', 'sanisidro' ); ?>
	</p>

	<table class="wp-list-table widefat fixed striped sanisidro-om-table">
		<thead>
			<tr>
				<th class="sanisidro-om-col-toggle"></th>
				<th><?php esc_html_e( 'Pedido', 'sanisidro' ); ?></th>
				<th><?php esc_html_e( 'Fecha', 'sanisidro' ); ?></th>
				<th><?php esc_html_e( 'Cliente', 'sanisidro' ); ?></th>
				<th><?php esc_html_e( 'Estado', 'sanisidro' ); ?></th>
				<th class="sanisidro-om-col-total"><?php esc_html_e( 'Total', 'sanisidro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $orders->orders ) ) : ?>
				<tr>
					<td colspan="6"><?php esc_html_e( 'No hay pedidos todavía.', 'sanisidro' ); ?></td>
				</tr>
			<?php endif; ?>

			<?php foreach ( $orders->orders as $order ) :
				if ( ! $order instanceof WC_Order ) continue;

				$order_id = $order->get_id();
				$groups   = $this->get_order_product_groups( $order );
				$subtotal = array_sum( wp_list_pluck( $groups, 'total_real' ) );
				$envio    = $this->get_shipping_cost( $order );
				$cliente  = $order->get_formatted_billing_full_name();
			?>
			<tr class="sanisidro-om-row">
				<td>
					<button type="button" class="sanisidro-om-toggle" data-order="<?php echo esc_attr( $order_id ); ?>" aria-expanded="false">
						<span class="sanisidro-om-toggle__icon" aria-hidden="true">›</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Ver productos del pedido', 'sanisidro' ); ?></span>
					</button>
				</td>
				<td>
					<a href="<?php echo esc_url( $order->get_edit_order_url() ); ?>">
						<strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
					</a>
				</td>
				<td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
				<td><?php echo esc_html( $cliente ?: __( 'Invitado', 'sanisidro' ) ); ?></td>
				<td>
					<mark class="sanisidro-om-status status-<?php echo esc_attr( $order->get_status() ); ?>">
						<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
					</mark>
				</td>
				<td class="sanisidro-om-col-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
			</tr>
			<tr class="sanisidro-om-detail-row" data-order="<?php echo esc_attr( $order_id ); ?>" hidden>
				<td colspan="6">
					<div class="sanisidro-om-detail">
						<table class="sanisidro-om-groups">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Producto', 'sanisidro' ); ?></th>
									<th class="sanisidro-om-col-num"><?php esc_html_e( 'Cant.', 'sanisidro' ); ?></th>
									<th class="sanisidro-om-col-num"><?php esc_html_e( 'Precio / kg', 'sanisidro' ); ?></th>
									<th class="sanisidro-om-col-num"><?php esc_html_e( 'Pesaje total', 'sanisidro' ); ?></th>
									<th class="sanisidro-om-col-num"><?php esc_html_e( 'Total real', 'sanisidro' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $groups as $product_id => $group ) : ?>
								<tr class="sanisidro-om-group">
									<td><?php echo esc_html( $group['name'] ); ?></td>
									<td class="sanisidro-om-col-num"><?php echo esc_html( $group['quantity'] ); ?></td>
									<td class="sanisidro-om-col-num sanisidro-om-precio-kg">
										<?php echo $group['precio_por_kg'] ? wp_kses_post( wc_price( $group['precio_por_kg'] ) ) : '—'; ?>
									</td>
									<td class="sanisidro-om-col-num">
										<?php if ( $group['precio_por_kg'] ) : ?>
										<label class="sanisidro-om-peso-wrap">
											<span class="screen-reader-text"><?php esc_html_e( 'Pesaje total en kilos', 'sanisidro' ); ?></span>
											<input
												type="number"
												step="0.001"
												min="0"
												class="sanisidro-om-peso-input"
												data-order="<?php echo esc_attr( $order_id ); ?>"
												data-product="<?php echo esc_attr( $product_id ); ?>"
												value="<?php echo esc_attr( $group['peso_real'] ? $group['peso_real'] : '' ); ?>"
												placeholder="0,000"
											>
											<span class="sanisidro-om-peso-unit" aria-hidden="true"><?php esc_html_e( 'kg', 'sanisidro' ); ?></span>
										</label>
										<?php else : ?>
											<span class="sanisidro-om-sin-kilaje"><?php esc_html_e( 'Sin atributo Kilaje', 'sanisidro' ); ?></span>
										<?php endif; ?>
									</td>
									<td class="sanisidro-om-col-num sanisidro-om-total-real"><?php echo wp_kses_post( wc_price( $group['total_real'] ) ); ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4"><?php esc_html_e( 'Subtotal', 'sanisidro' ); ?></td>
									<td class="sanisidro-om-col-num sanisidro-om-subtotal"><?php echo wp_kses_post( wc_price( $subtotal ) ); ?></td>
								</tr>
								<tr>
									<td colspan="4"><?php esc_html_e( 'Costo de envío', 'sanisidro' ); ?></td>
									<td class="sanisidro-om-col-num">
										<label class="sanisidro-om-peso-wrap">
											<span class="screen-reader-text"><?php esc_html_e( 'Costo de envío', 'sanisidro' ); ?></span>
											<input
												type="number"
												step="0.01"
												min="0"
												class="sanisidro-om-envio-input"
												data-order="<?php echo esc_attr( $order_id ); ?>"
												value="<?php echo esc_attr( $envio ? $envio : '' ); ?>"
												placeholder="0,00"
											>
										</label>
									</td>
								</tr>
								<tr class="sanisidro-om-total-row">
									<td colspan="4"><strong><?php esc_html_e( 'Total', 'sanisidro' ); ?></strong></td>
									<td class="sanisidro-om-col-num sanisidro-om-order-total"><strong><?php echo wp_kses_post( wc_price( $subtotal + $envio ) ); ?></strong></td>
								</tr>
								<tr class="sanisidro-om-whatsapp-row">
									<td colspan="5">
										<button type="button" class="button button-primary sanisidro-om-whatsapp-btn" data-order="<?php echo esc_attr( $order_id ); ?>">
											<?php esc_html_e( 'Generar link de WhatsApp', 'sanisidro' ); ?>
										</button>
										<span class="sanisidro-om-whatsapp-result"></span>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ( $orders->max_num_pages > 1 ) : ?>
	<div class="tablenav">
		<div class="tablenav-pages">
			<?php
			echo wp_kses_post( paginate_links( [
				'base'      => add_query_arg( 'paged', '%#%' ),
				'format'    => '',
				'current'   => $paged,
				'total'     => $orders->max_num_pages,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
			] ) );
			?>
		</div>
	</div>
	<?php endif; ?>
</div>
