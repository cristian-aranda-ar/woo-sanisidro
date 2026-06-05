<?php
defined( 'ABSPATH' ) || exit;

global $product;
if ( ! is_a( $product, 'WC_Product' ) ) return;

$url    = get_permalink();
$imagen = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
?>

<li <?php wc_product_class( 'producto-card', $product ); ?> onclick="window.location='<?php echo esc_url( $url ); ?>'" style="cursor:pointer;">

	<div class="producto-card__imagen">
		<?php if ( $imagen ) : ?>
			<img src="<?php echo esc_url( $imagen ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
		<?php else : ?>
			<div class="producto-card__placeholder" style="background-color:#d4cdc7;"></div>
		<?php endif; ?>
	</div>

	<h3 class="producto-card__nombre"><?php the_title(); ?></h3>

	<a href="<?php echo esc_url( $url ); ?>" class="link-cta">Comprar</a>

</li>
