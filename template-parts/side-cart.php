<?php if ( ! function_exists( 'WC' ) ) return; ?>

<!-- Panel lateral del carrito -->
<div class="side-cart" id="sideCart" aria-hidden="true" role="dialog" aria-label="Tu carrito">

	<div class="side-cart__header">
		<h2 class="side-cart__title">Tu carrito</h2>
		<button class="side-cart__close" id="sideCartClose" aria-label="Cerrar carrito">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true">
				<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
			</svg>
		</button>
	</div>

	<div class="side-cart__body widget_shopping_cart_content">
		<?php woocommerce_mini_cart(); ?>
	</div>

</div>

<!-- Overlay -->
<div class="side-cart__overlay" id="sideCartOverlay"></div>

<!-- Contenedor de toasts -->
<div class="toast-container" id="toastContainer" aria-live="polite"></div>
