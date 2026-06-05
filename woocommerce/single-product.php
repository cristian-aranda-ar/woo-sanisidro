<?php
defined( 'ABSPATH' ) || exit;

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',              'woocommerce_get_sidebar', 10 );

get_header();
?>

<main id="main" class="product-page">
	<div class="container">

		<?php woocommerce_breadcrumb(); ?>

		<?php do_action( 'woocommerce_before_single_product' ); ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php wc_get_template_part( 'content', 'single-product' ); ?>
		<?php endwhile; ?>

	</div>
</main>

<?php get_footer(); ?>
