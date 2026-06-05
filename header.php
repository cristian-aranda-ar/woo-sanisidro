<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header__inner">

		<!-- Nav izquierda -->
		<nav class="site-nav site-nav--center" aria-label="Principal">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'site-nav__list',
				'fallback_cb'    => function() {
					echo '<ul class="site-nav__list">';
					echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/productos' ) ) . '">Productos</a></li>';
					echo '<li><a href="' . esc_url( home_url( '/recetas' ) ) . '">Recetas</a></li>';
					echo '</ul>';
				},
			] );
			?>
		</nav>

		<!-- Logo centro -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" aria-label="<?php bloginfo( 'name' ); ?>">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/sic.svg' ); ?>"
				alt="<?php bloginfo( 'name' ); ?>"
				width="140"
				height="48"
				loading="eager"
			>
		</a>

		<!-- Derecha: nav secundaria + redes + acciones -->
		<div class="site-header__right">
			<nav class="site-nav site-nav--secondary" aria-label="Secundaria">
				<?php
				wp_nav_menu( [
					'theme_location' => 'secondary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => function() {
						echo '<ul class="site-nav__list">';
						echo '<li><a href="' . esc_url( home_url( '/nosotros' ) ) . '">Nosotros</a></li>';
						echo '<li><a href="' . esc_url( home_url( '/contacto' ) ) . '">Contacto</a></li>';
						echo '</ul>';
					},
				] );
				?>
			</nav>

			<div class="header-actions">

				<!-- Botón Ingresar -->
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/mi-cuenta' ) ); ?>" class="btn-ingresar">
					Ingresar
				</a>

				<!-- Carrito -->
				<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/carrito' ) ); ?>" class="header-action header-action--cart" aria-label="Carrito">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
					<?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
						<?php $count = WC()->cart->get_cart_contents_count(); ?>
						<span class="cart-count<?php echo $count > 0 ? '' : ' cart-count--empty'; ?>"><?php echo esc_html( $count ); ?></span>
					<?php endif; ?>
				</a>

			</div>
		</div>

	</div>
</header>
