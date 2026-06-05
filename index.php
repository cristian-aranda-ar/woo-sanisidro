<?php get_header(); ?>

<main id="main" class="container" style="padding-block: 4rem;">

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No se encontraron entradas.', 'sanisidro' ); ?></p>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
