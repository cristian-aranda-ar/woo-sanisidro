document.addEventListener( 'DOMContentLoaded', function () {
	document.addEventListener( 'click', function ( e ) {
		const btn = e.target.closest( '.qty-btn' );
		if ( ! btn ) return;

		const input = btn.closest( '.qty-stepper' ).querySelector( 'input[type="number"]' );
		if ( ! input ) return;

		const step = parseFloat( input.step ) || 1;
		const min  = parseFloat( input.min )  || 0;
		const max  = input.max !== '' ? parseFloat( input.max ) : Infinity;
		let   val  = parseFloat( input.value ) || min;

		if ( btn.classList.contains( 'qty-btn--minus' ) ) {
			val = Math.max( min, val - step );
		} else {
			val = Math.min( max, val + step );
		}

		input.value = val;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	} );
} );
