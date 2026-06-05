<?php
defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) : ?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>">
	</div>
<?php else : ?>
	<div class="quantity qty-stepper">
		<button type="button" class="qty-btn qty-btn--minus" aria-label="Reducir cantidad">&#8722;</button>
		<input
			type="number"
			id="<?php echo esc_attr( $input_id ); ?>"
			class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $input_value ); ?>"
			min="<?php echo esc_attr( $min_value ); ?>"
			max="<?php echo esc_attr( $max_value ? $max_value : '' ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			autocomplete="<?php echo esc_attr( isset( $autocomplete ) ? $autocomplete : 'on' ); ?>"
			<?php echo $readonly ? 'readonly="readonly"' : ''; ?>
			aria-label="<?php esc_attr_e( 'Product quantity', 'woocommerce' ); ?>"
		>
		<button type="button" class="qty-btn qty-btn--plus" aria-label="Aumentar cantidad">&#43;</button>
	</div>
<?php endif; ?>
