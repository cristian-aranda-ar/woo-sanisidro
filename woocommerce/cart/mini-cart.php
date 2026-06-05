<?php
defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_mini_cart' );
?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<ul class="side-cart__items <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php do_action( 'woocommerce_before_mini_cart_contents' ); ?>

		<?php foreach ( WC()->cart->get_cart() as $key => $item ) :
			$product    = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $item['product_id'], $item, $key );

			if ( ! $product || ! $product->exists() || $item['quantity'] <= 0 ) continue;
			if ( ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $item, $key ) ) continue;

			$name      = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $item, $key );
			$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image( 'thumbnail' ), $item, $key );
			$price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $item, $key );
			$permalink = apply_filters( 'woocommerce_cart_item_permalink', $product->is_visible() ? $product->get_permalink( $item ) : '', $item, $key );
		?>
		<li class="side-cart__item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $item, $key ) ); ?>">

			<a href="<?php echo esc_url( $permalink ); ?>" class="side-cart__item-img">
				<?php echo wp_kses_post( $thumbnail ); ?>
			</a>

			<div class="side-cart__item-info">
				<a href="<?php echo esc_url( $permalink ); ?>" class="side-cart__item-name"><?php echo wp_kses_post( $name ); ?></a>
				<span class="side-cart__item-price"><?php echo wp_kses_post( $price ); ?></span>

				<div class="side-cart__item-controls">
					<div class="wc-block-cart-item__quantity" data-key="<?php echo esc_attr( $key ); ?>">
						<button type="button" class="wc-quantity-button wc-quantity-button--minus" aria-label="Reducir cantidad">−</button>
						<input type="number" class="wc-quantity-input" value="<?php echo esc_attr( $item['quantity'] ); ?>" min="1">
						<button type="button" class="wc-quantity-button wc-quantity-button--plus" aria-label="Aumentar cantidad">+</button>
					</div>

					<button type="button" class="wc-block-cart-item__remove-link side-cart__item-remove"
					        data-product_id="<?php echo esc_attr( $product_id ); ?>"
					        data-cart_item_key="<?php echo esc_attr( $key ); ?>"
					        data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					        aria-label="Eliminar del carrito">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
						</svg>
					</button>
				</div>
			</div>

		</li>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_mini_cart_contents' ); ?>
	</ul>

	<div class="side-cart__footer">
		<div class="side-cart__subtotal">
			<span>Subtotal</span>
			<span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		</div>
		<div class="side-cart__footer-actions">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="side-cart__btn side-cart__btn--outline">Ver carrito</a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="side-cart__btn side-cart__btn--primary">Finalizar compra</a>
		</div>
	</div>

<?php else : ?>

	<div class="side-cart__empty">
		<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
		<p>Tu carrito está vacío</p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="side-cart__btn side-cart__btn--primary">Ver productos</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
