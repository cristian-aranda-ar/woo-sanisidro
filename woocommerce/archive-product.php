<?php
defined( 'ABSPATH' ) || exit;

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar',              'woocommerce_get_sidebar', 10 );

get_header();

// Imagen de la categoría actual (si tiene)
$term         = get_queried_object();
$thumbnail_id = $term instanceof WP_Term ? get_term_meta( $term->term_id, 'thumbnail_id', true ) : 0;
$hero_img     = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'full' ) : '';
?>

<main id="main" class="archive-page">

	<!-- ── Hero de categoría ── -->
	<div class="archive-hero<?php echo $hero_img ? ' archive-hero--has-image' : ''; ?>"
		<?php if ( $hero_img ) echo 'style="background-image:url(' . esc_url( $hero_img ) . ');"'; ?>>
		<div class="archive-hero__inner container">
			<?php woocommerce_breadcrumb(); ?>
			<h1 class="archive-hero__title"><?php woocommerce_page_title(); ?></h1>
			<?php if ( $desc = term_description() ) : ?>
				<div class="archive-hero__desc"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</div>
	</div>

	<!-- ── Contenido ── -->
	<div class="container archive-content">

		<?php if ( woocommerce_product_loop() ) : ?>

			<!-- Barra: resultados + ordenamiento -->
			<div class="archive-toolbar">
				<?php woocommerce_result_count(); ?>
				<?php woocommerce_catalog_ordering(); ?>
			</div>

			<?php woocommerce_product_loop_start(); ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; ?>
			<?php woocommerce_product_loop_end(); ?>

			<!-- Paginación -->
			<div class="archive-pagination">
				<?php woocommerce_pagination(); ?>
			</div>

		<?php else : ?>
			<?php do_action( 'woocommerce_no_products_found' ); ?>
		<?php endif; ?>

	</div>

</main>

<?php get_footer(); ?>
