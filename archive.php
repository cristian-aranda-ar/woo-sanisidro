<?php get_header(); ?>

<main id="main" class="recetas-archive">

	<!-- Hero -->
	<div class="recetas-hero">
		<div class="container recetas-hero__inner">
			<p class="recetas-hero__sup">Campo San Isidro</p>
			<h1 class="recetas-hero__title">Recetas</h1>
			<?php if ( is_category() && category_description() ) : ?>
				<p class="recetas-hero__desc"><?php echo wp_kses_post( category_description() ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Grid -->
	<div class="container recetas-content">

		<?php if ( have_posts() ) : ?>

			<div class="recetas-grid recetas-grid--archive">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$cats   = get_the_category();
					$imagen = get_the_post_thumbnail_url( get_the_ID(), 'large' );
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'receta-card' ); ?> onclick="window.location='<?php the_permalink(); ?>'" style="cursor:pointer;">
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
						<h2 class="receta-card__titulo"><?php the_title(); ?></h2>
						<a href="<?php the_permalink(); ?>" class="link-cta">Ver receta</a>
					</article>
				<?php endwhile; ?>
			</div>

			<!-- Paginación -->
			<div class="recetas-pagination">
				<?php
				the_posts_pagination( [
					'mid_size'  => 2,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
				] );
				?>
			</div>

		<?php else : ?>
			<p class="recetas-empty">No hay recetas publicadas todavía.</p>
		<?php endif; ?>

	</div>

</main>

<?php get_footer(); ?>
