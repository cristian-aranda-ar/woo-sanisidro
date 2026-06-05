<?php
$x_url         = get_theme_mod( 'sanisidro_x_url', '' );
$facebook_url  = get_theme_mod( 'sanisidro_facebook_url', '' );
$instagram_url = get_theme_mod( 'sanisidro_instagram_url', '' );
$handle        = get_theme_mod( 'sanisidro_social_handle', '@SANISIDRO' );
$sitio         = get_theme_mod( 'sanisidro_footer_sitio', 'SANISIDRO.COM.AR' );
$direccion     = get_theme_mod( 'sanisidro_footer_direccion', '' );
$telefono      = get_theme_mod( 'sanisidro_footer_telefono', '' );
$email         = get_theme_mod( 'sanisidro_footer_email', '' );

$col_titles = [
	1 => get_theme_mod( 'sanisidro_footer_title_1', 'Sobre nosotros' ),
	2 => get_theme_mod( 'sanisidro_footer_title_2', 'Tienda' ),
	3 => get_theme_mod( 'sanisidro_footer_title_3', 'Legales' ),
	4 => get_theme_mod( 'sanisidro_footer_title_4', 'Políticas' ),
];

$col_fallbacks = [
	1 => [ 'Quiénes somos' => '/nosotros', 'Producción' => '/produccion', 'Historia' => '/historia', 'Proveedores' => '/proveedores' ],
	2 => [ 'Mi cuenta' => '/mi-cuenta', 'Mis pedidos' => '/mis-pedidos', 'Promociones' => '/promociones' ],
	3 => [ 'Política de privacidad' => '/privacidad', 'Términos y condiciones' => '/terminos', 'Devoluciones' => '/devoluciones' ],
	4 => [ 'Preguntas frecuentes' => '/faq', 'Contacto' => '/contacto', 'Envíos' => '/envios' ],
];
?>

<footer class="site-footer">

	<!-- ── Cuerpo principal ─────────────────────────── -->
	<div class="site-footer__main">

		<!-- Logo -->
		<div class="footer-logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-naranja.svg' ); ?>"
					alt="<?php bloginfo( 'name' ); ?>"
					width="160"
					loading="lazy"
				>
			</a>
		</div>

		<!-- Columnas de navegación -->
		<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
		<div class="footer-col">
			<h4 class="footer-col__title"><?php echo esc_html( $col_titles[ $i ] ); ?></h4>
			<?php
			wp_nav_menu( [
				'theme_location' => "footer-{$i}",
				'container'      => false,
				'menu_class'     => 'footer-col__list',
				'fallback_cb'    => function() use ( $col_fallbacks, $i ) {
					echo '<ul class="footer-col__list">';
					foreach ( $col_fallbacks[ $i ] as $label => $url ) {
						echo '<li><a href="' . esc_url( home_url( $url ) ) . '">' . esc_html( $label ) . '</a></li>';
					}
					echo '</ul>';
				},
			] );
			?>
		</div>
		<?php endfor; ?>

		<!-- Redes sociales -->
		<div class="footer-col">
			<h4 class="footer-col__title">
				<?php esc_html_e( 'Seguinos', 'sanisidro' ); ?><br>
				<?php echo esc_html( $handle ); ?>
			</h4>
			<ul class="footer-col__list footer-col__list--social">
				<li class="social-row">
					<a href="<?php echo esc_url( $x_url ?: '#' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.726-8.84L1.254 2.25H8.08l4.261 5.636L18.244 2.25Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z"/></svg>
					</a>
					<a href="<?php echo esc_url( $facebook_url ?: '#' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
					</a>
					<a href="<?php echo esc_url( $instagram_url ?: '#' ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.919-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
					</a>
				</li>
			</ul>
		</div>

	</div>

	<!-- ── Barra inferior ──────────────────────────── -->
	<div class="site-footer__bar">
		<div class="site-footer__bar-inner">

			<span class="footer-bar__sitio">CAMPOSANISIDRO.COM.AR</span>

			<span class="footer-bar__info">
				<strong>DIRECCIÓN</strong> Ruta Nac. 12 KM 8 1/2, Garupá, Misiones &mdash;
				<strong>TELEFONOS/FAX</strong> (0376) 4480370 / 448184 &mdash;
				<strong>EMAIL</strong> contacto@elabastocarnes.com
			</span>

			<span class="footer-bar__copy">&copy; <?php echo esc_html( date( 'Y' ) ); ?> CAMPO SAN ISIDRO</span>

		</div>
	</div>

</footer>

<?php get_template_part( 'template-parts/side-cart' ); ?>
<?php wp_footer(); ?>
</body>
</html>
