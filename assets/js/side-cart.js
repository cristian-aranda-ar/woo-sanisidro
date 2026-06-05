/* global sanisidroCart, jQuery */
( function ( $ ) {
	'use strict';

	const DEBUG = true;
	const log = ( msg, data ) => { if ( DEBUG ) console.log( '[SideCart]', msg, data || '' ); };

	const $body    = $( document.body );
	const sideCart = document.getElementById( 'sideCart' );
	const overlay  = document.getElementById( 'sideCartOverlay' );
	const toastWrap = document.getElementById( 'toastContainer' );

	log( 'Inicializando side-cart.js' );
	log( 'sanisidroCart global:', sanisidroCart );

	/* ── Panel lateral ─────────────────────────────── */
	function openCart() {
		log( 'Abriendo carrito' );
		sideCart.classList.add( 'side-cart--open' );
		overlay.classList.add( 'side-cart__overlay--visible' );
		document.body.classList.add( 'cart-open' );
		sideCart.setAttribute( 'aria-hidden', 'false' );
	}

	function closeCart() {
		log( 'Cerrando carrito' );
		sideCart.classList.remove( 'side-cart--open' );
		overlay.classList.remove( 'side-cart__overlay--visible' );
		document.body.classList.remove( 'cart-open' );
		sideCart.setAttribute( 'aria-hidden', 'true' );
	}

	/* ── Toast ─────────────────────────────────────── */
	function showToast( message ) {
		log( 'Mostrando toast:', message );
		const toast = document.createElement( 'div' );
		toast.className = 'toast';
		toast.innerHTML = `
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
			<span>${ message }</span>
			<button class="toast__close" aria-label="Cerrar">&times;</button>
		`;
		toastWrap.appendChild( toast );
		requestAnimationFrame( () => requestAnimationFrame( () => toast.classList.add( 'toast--visible' ) ) );
		const dismiss = () => {
			toast.classList.remove( 'toast--visible' );
			toast.addEventListener( 'transitionend', () => toast.remove(), { once: true } );
		};
		const timer = setTimeout( dismiss, 4000 );
		toast.querySelector( '.toast__close' ).addEventListener( 'click', () => { clearTimeout( timer ); dismiss(); } );
	}

	/* ── Helpers ────────────────────────────────────── */
	function updateCartBody( html ) {
		log( 'Inyectando HTML en .widget_shopping_cart_content' );
		const $container = $( '.widget_shopping_cart_content' );
		if ( ! $container.length ) {
			console.error( '[SideCart] No se encontró .widget_shopping_cart_content' );
			return;
		}
		$container.html( html );
		log( 'HTML inyectado exitosamente' );
	}

	function updateCartCount( count ) {
		log( 'Actualizando conteo del carrito:', count );
		const badge = document.querySelector( '.cart-count' );
		if ( ! badge ) {
			log( 'Advertencia: no se encontró .cart-count' );
			return;
		}
		badge.textContent = count;
		badge.classList.toggle( 'cart-count--empty', count < 1 );
	}

	/* ── AJAX: actualizar cantidad / eliminar ──────── */
	function updateQty( key, qty ) {
		log( 'updateQty llamado:', { key, qty } );

		if ( ! key ) {
			console.error( '[SideCart] Cart key inválida' );
			return;
		}

		if ( ! sanisidroCart || ! sanisidroCart.ajaxUrl ) {
			console.error( '[SideCart] sanisidroCart no está disponible', sanisidroCart );
			return;
		}

		log( 'Enviando AJAX POST a:', sanisidroCart.ajaxUrl );

		$.ajax( {
			url    : sanisidroCart.ajaxUrl,
			type   : 'POST',
			data   : {
				action : 'sanisidro_update_qty',
				nonce  : sanisidroCart.nonce,
				key    : key,
				qty    : qty,
			},
			success: function ( res ) {
				log( 'AJAX exitoso, respuesta:', res );

				if ( ! res.success ) {
					console.error( '[SideCart] Error del servidor:', res.data );
					return;
				}

				updateCartBody( res.data.html );
				updateCartCount( res.data.cart_count );
			},
			error: function ( xhr, status, error ) {
				console.error( '[SideCart] Error AJAX:', { status, error, xhr } );
			}
		} );
	}

	/* ── Eliminar producto ───────────────────────── */
	$body.on( 'click', '.wc-block-cart-item__remove-link', function ( e ) {
		e.preventDefault();
		log( 'Click en botón eliminar' );
		const key = $( this ).data( 'cart_item_key' );
		log( 'Cart item key:', key );
		if ( key ) {
			updateQty( key, 0 );
		} else {
			console.error( '[SideCart] No se pudo obtener cart_item_key' );
		}
	} );

	/* ── Stepper de cantidad ───────────────────────── */
	$body.on( 'click', '.wc-quantity-button', function ( e ) {
		e.preventDefault();
		log( 'Click en botón cantidad' );

		const $stepper = $( this ).closest( '.wc-block-cart-item__quantity' );
		const key      = $stepper.data( 'key' );
		const $input   = $stepper.find( '.wc-quantity-input' );
		let   qty      = parseInt( $input.val(), 10 ) || 1;

		log( 'Stepper info:', { key, qty_actual: qty } );

		qty = $( this ).hasClass( 'wc-quantity-button--minus' )
			? Math.max( 0, qty - 1 )
			: qty + 1;

		log( 'Nueva cantidad:', qty );
		$input.val( qty );
		updateQty( key, qty );
	} );

	/* ── Icono carrito → abrir panel ───────────────── */
	document.querySelector( '.header-action--cart' )?.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		log( 'Click en icono carrito del header' );
		openCart();
	} );

	/* ── Cerrar ────────────────────────────────────── */
	document.getElementById( 'sideCartClose' )?.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		closeCart();
	} );

	overlay?.addEventListener( 'click', closeCart );

	document.addEventListener( 'keydown', ( e ) => {
		if ( e.key === 'Escape' ) {
			log( 'Escape presionado, cerrando carrito' );
			closeCart();
		}
	} );

	/* ── WC Notices → toast al cargar página ────────── */
	$( function () {
		log( 'DOM listo, buscando notices de WC' );
		$( '.woocommerce-notices-wrapper .woocommerce-message, .woocommerce-notices-wrapper .woocommerce-error, .woocommerce-notices-wrapper .woocommerce-info' ).each( function () {
			const $n = $( this );
			log( 'Notice encontrado' );

			// Traducción de textos en inglés
			let html = $n.html();
			html = html.replace( / has been added to your cart/g, ' ha sido agregado a tu carrito' );
			html = html.replace( /View cart/g, 'Ver carrito' );
			html = html.replace( /Proceed to checkout/g, 'Ir al pago' );
			$n.html( html );

			requestAnimationFrame( () => requestAnimationFrame( () => $n.addClass( 'wc-toast--visible' ) ) );
			setTimeout( () => {
				$n.removeClass( 'wc-toast--visible' );
				$n.one( 'transitionend', () => $n.closest( 'li, div' ).remove() );
			}, 5000 );
		} );
	} );

	/* ── Agregar desde el loop (archive/home) ─── */
	$body.on( 'added_to_cart', function ( e, fragments, hash, $btn ) {
		log( 'Producto agregado al carrito vía WC AJAX' );
		const name = $btn.closest( '.product' )
			.find( '.producto-card__nombre, .product_title' )
			.first().text().trim() || 'Producto agregado al carrito';
		showToast( name );
		openCart();
		$( '.woocommerce-notices-wrapper' ).empty();
	} );

	log( 'side-cart.js cargado completamente' );

} )( jQuery );
