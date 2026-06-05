<?php get_header(); ?>

<main id="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-hero">
			<?php the_post_thumbnail( 'full', [ 'class' => 'post-hero__img', 'loading' => 'eager' ] ); ?>
		</div>
		<?php endif; ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'container post-single' ); ?>>
			<h1 class="post-single__title"><?php the_title(); ?></h1>
			<div class="entry-content post-single__content"><?php the_content(); ?></div>
		</article>

		<?php the_post_navigation(); ?>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
