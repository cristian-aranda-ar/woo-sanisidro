(function () {
	var DEFAULTS = {
		'#billing-country': 'AR',
		'#billing-state': 'N',
		'#shipping-country': 'AR',
		'#shipping-state': 'N',
	};
	var TEXT_DEFAULTS = {
		'#billing-postcode': 'N3300',
		'#shipping-postcode': 'N3300',
	};
	var CIUDADES = [ 'Posadas', 'Garupá', 'Candelaria' ];

	function setNativeValue( el, value ) {
		var proto = el.tagName === 'SELECT' ? window.HTMLSelectElement.prototype : window.HTMLInputElement.prototype;
		var setter = Object.getOwnPropertyDescriptor( proto, 'value' ).set;
		setter.call( el, value );
		el.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		el.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		el.dispatchEvent( new Event( 'blur', { bubbles: true } ) );
	}

	function fillDefaults() {
		Object.keys( DEFAULTS ).forEach( function ( selector ) {
			var el = document.querySelector( selector );
			if ( el && el.value !== DEFAULTS[ selector ] ) setNativeValue( el, DEFAULTS[ selector ] );
		} );
		Object.keys( TEXT_DEFAULTS ).forEach( function ( selector ) {
			var el = document.querySelector( selector );
			if ( el && el.value !== TEXT_DEFAULTS[ selector ] ) setNativeValue( el, TEXT_DEFAULTS[ selector ] );
		} );
	}

	// Reemplaza el input de texto "Ciudad" por 3 botones fijos (Posadas / Garupá / Candelaria)
	function ensureCiudadSelector() {
		document.querySelectorAll( '.wc-block-components-address-form__city' ).forEach( function ( wrapper ) {
			if ( wrapper.dataset.sanisidroDone ) return;

			var input = wrapper.querySelector( 'input' );
			if ( ! input ) return;

			var field = document.createElement( 'div' );
			field.className = 'sanisidro-ciudad-field';

			var label = document.createElement( 'span' );
			label.className = 'sanisidro-ciudad-field__label';
			label.textContent = 'Ciudad';
			field.appendChild( label );

			var options = document.createElement( 'div' );
			options.className = 'sanisidro-ciudad-field__options';
			field.appendChild( options );

			CIUDADES.forEach( function ( ciudad ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'sanisidro-ciudad-btn';
				btn.textContent = ciudad;
				btn.addEventListener( 'click', function () {
					setNativeValue( input, ciudad );
					options.querySelectorAll( '.sanisidro-ciudad-btn' ).forEach( function ( b ) {
						b.classList.toggle( 'is-active', b === btn );
					} );
				} );
				options.appendChild( btn );
			} );

			wrapper.insertAdjacentElement( 'afterend', field );
			wrapper.dataset.sanisidroDone = '1';

			// Si ya tenía un valor cargado (cliente recurrente), reflejarlo en los botones
			if ( CIUDADES.indexOf( input.value ) !== -1 ) {
				options.querySelectorAll( '.sanisidro-ciudad-btn' ).forEach( function ( b ) {
					b.classList.toggle( 'is-active', b.textContent === input.value );
				} );
			}
		} );
	}

	// WooCommerce muestra "Gratis" en cualquier envío con costo $0. Cuando el monto
	// real todavía no está definido (Posadas/Candelaria por debajo del mínimo),
	// reemplazamos ese "Gratis" por "A confirmar" para no confundir al cliente.
	function fixEnvioAConfirmar() {
		document.querySelectorAll( '.wc-block-components-radio-control__option' ).forEach( function ( opt ) {
			var labelEl = opt.querySelector( '.wc-block-components-radio-control__label' );
			var freeEl = opt.querySelector( '.wc-block-checkout__shipping-option--free' );
			if ( labelEl && freeEl && /a confirmar/i.test( labelEl.textContent ) && freeEl.textContent !== 'A confirmar' ) {
				freeEl.textContent = 'A confirmar';
			}
		} );

		document.querySelectorAll( '.wc-block-components-totals-item' ).forEach( function ( row ) {
			var labelEl = row.querySelector( '.wc-block-components-totals-item__label' );
			var valueEl = row.querySelector( '.wc-block-components-totals-item__value' );
			if ( labelEl && valueEl && /a confirmar/i.test( labelEl.textContent ) && /gratis|free/i.test( valueEl.textContent ) && valueEl.textContent.trim() !== 'A confirmar' ) {
				valueEl.textContent = 'A confirmar';
			}
		} );
	}

	// El checkout de bloques (React) monta y re-renderiza estos campos de forma asincrónica
	var attempts = 0;
	var interval = setInterval( function () {
		fillDefaults();
		ensureCiudadSelector();
		fixEnvioAConfirmar();
		attempts++;
		if ( attempts > 60 ) clearInterval( interval ); // ~30s
	}, 500 );
} )();
