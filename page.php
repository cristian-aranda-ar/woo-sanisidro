<?php get_header(); ?>

<main id="main" class="container" style="padding-block: 4rem;">

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
