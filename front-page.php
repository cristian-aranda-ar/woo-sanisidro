<?php get_header(); ?>

<main id="main">

	<!-- ═══ HERO ════════════════════════════════════════════════ -->
	<div class="home-hero">
		<div class="hero-slider" aria-label="Slider principal">
			<div class="hero-slide hero-slide--active">
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/si1.webp' ); ?>"
					alt="Campo San Isidro"
					fetchpriority="high"
					loading="eager"
				>
			</div>
			<div class="hero-slide">
				<img
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/si2.webp' ); ?>"
					alt="Campo San Isidro"
					loading="lazy"
				>
			</div>
		</div>
		<div class="hero-overlay-logo">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/NOS.svg' ); ?>"
				alt="Campo San Isidro"
				loading="eager"
			>
		</div>
	</div>

	<!-- ═══ S1: PRODUCTOS DESTACADOS ════════════════════════════ -->
	<section class="home-productos">
		<div class="container">
			<h2 class="section-title">Los auténticos de campo</h2>
			<div class="productos-grid">
				<?php
				$productos = new WP_Query( [
					'post_type'      => 'product',
					'posts_per_page' => 3,
					'orderby'        => 'rand',
					'post_status'    => 'publish',
				] );

				while ( $productos->have_posts() ) :
					$productos->the_post();
					$product = wc_get_product( get_the_ID() );
					$url     = get_permalink();
					$imagen  = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
				?>
				<article class="producto-card" onclick="window.location='<?php echo esc_url( $url ); ?>'" style="cursor:pointer;">
					<div class="producto-card__imagen">
						<?php if ( $imagen ) : ?>
							<img src="<?php echo esc_url( $imagen ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
						<?php else : ?>
							<div class="producto-card__placeholder" style="background-color:#d4cdc7;"></div>
						<?php endif; ?>
					</div>
					<h3 class="producto-card__nombre"><?php the_title(); ?></h3>
					<a href="<?php echo esc_url( $url ); ?>" class="link-cta">Comprar</a>
				</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<div class="section-cta">
				<a href="<?php echo esc_url( home_url( '/tienda' ) ); ?>" class="btn">Ver más</a>
			</div>
		</div>
	</section>

	<!-- ═══ S2: CATEGORÍAS SPLIT ════════════════════════════════ -->
	<section class="home-categorias">
		<div class="home-categorias__imagen" id="categoriasSlider">
			<?php
			$cat_products = new WP_Query( [
				'post_type'      => 'product',
				'posts_per_page' => 6,
				'orderby'        => 'rand',
				'post_status'    => 'publish',
				'meta_query'     => [ [
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				] ],
			] );
			$first = true;
			while ( $cat_products->have_posts() ) :
				$cat_products->the_post();
				$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				if ( ! $img ) continue;
			?>
			<div class="cat-slide<?php echo $first ? ' cat-slide--active' : ''; ?>">
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php the_title_attribute(); ?>" loading="<?php echo $first ? 'eager' : 'lazy'; ?>">
			</div>
			<?php $first = false; endwhile; wp_reset_postdata(); ?>
		</div>
		<div class="home-categorias__texto">
			<img
				src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mas.svg' ); ?>"
				alt="Ver más"
				class="cat-mas-svg"
				loading="lazy"
			>
		</div>
	</section>

	<!-- ═══ S3: MANIFIESTO ══════════════════════════════════════ -->
	<section class="home-manifiesto">
		<div class="home-manifiesto__texto"></div>
		<div class="home-manifiesto__imagenes"></div>
	</section>

	<!-- ═══ S4: SOMOS CAMPO SPLIT ═══════════════════════════════ -->
	<section class="home-nosotros">
		<div class="home-nosotros__imagen"></div>
		<div class="home-nosotros__texto">
			<h2>Somos Campo</h2>
			<p>Vos sabés lo que significa ponerse a comer con quien le puso el cuerpo al asado. Alguien verdadero, que sabe cortar los salamines, alguna verdura, descorchar el vino y sentarse al aire libre al borde del paso, hinchar y ser relajado, bromear, repasar cosas, tomarse algún tiempo sin teléfono. Eso somos. Simples y los menos que ellos que caben en un cuento.</p>
		</div>
	</section>

	<!-- ═══ S5: RECETAS ═════════════════════════════════════════ -->
	<section class="home-recetas">
		<div class="container">
			<h2 class="section-title">Nuestras recetas</h2>
			<div class="recetas-grid">
				<?php
				$recetas = new WP_Query( [
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'post_status'    => 'publish',
					'orderby'        => 'date',
					'order'          => 'DESC',
				] );

				while ( $recetas->have_posts() ) :
					$recetas->the_post();
					$cats   = get_the_category();
					$imagen = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				?>
				<article class="receta-card" onclick="window.location='<?php the_permalink(); ?>'" style="cursor:pointer;">
					<div class="receta-card__imagen">
						<?php if ( $imagen ) : ?>
							<img src="<?php echo esc_url( $imagen ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
						<?php else : ?>
							<div class="receta-card__placeholder" style="background-color:#d4cdc7;"></div>
						<?php endif; ?>
					</div>
					<?php if ( $cats ) : ?>
					<p class="receta-card__cat"><?php echo esc_html( $cats[0]->name ); ?></p>
					<?php endif; ?>
					<h3 class="receta-card__titulo"><?php the_title(); ?></h3>
					<a href="<?php the_permalink(); ?>" class="link-cta">Ver receta</a>
				</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</section>

	<!-- ═══ S6: INSTAGRAM ═══════════════════════════════════════ -->
	<?php
	$ig_url = get_theme_mod( 'sanisidro_instagram_url', 'https://instagram.com/' );
	$ig_imgs = [];
	for ( $i = 1; $i <= 8; $i++ ) {
		$ig_imgs[] = get_template_directory_uri() . '/assets/images/insta/ig' . $i . '.jpg';
	}
	?>
	<section class="home-instagram">
		<div class="container">
			<h2 class="section-title">@camposanisidro</h2>
		</div>
		<div class="instagram-grid">
			<?php foreach ( $ig_imgs as $src ) : ?>
			<a href="<?php echo esc_url( $ig_url ); ?>" target="_blank" rel="noopener noreferrer" class="instagram-item">
				<img src="<?php echo esc_url( $src ); ?>" alt="Campo San Isidro en Instagram" loading="lazy">
				<div class="ig-hover">
					<span class="ig-hover__stat">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
						<?php echo rand( 18, 342 ); ?>
					</span>
					<span class="ig-hover__stat">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
						<?php echo rand( 2, 47 ); ?>
					</span>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
	</section>

</main>

<?php get_footer(); ?>
