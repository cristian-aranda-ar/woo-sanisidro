(function () {
	document.addEventListener( 'click', function ( e ) {
		var toggle = e.target.closest( '.sanisidro-om-toggle' );
		if ( toggle ) {
			var orderId = toggle.dataset.order;
			var detail = document.querySelector( '.sanisidro-om-detail-row[data-order="' + orderId + '"]' );
			if ( ! detail ) return;

			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
			detail.hidden = expanded;
			return;
		}

		var waBtn = e.target.closest( '.sanisidro-om-whatsapp-btn' );
		if ( waBtn ) {
			generarWhatsapp( waBtn );
		}
	} );

	document.addEventListener( 'change', function ( e ) {
		var pesoInput = e.target.closest( '.sanisidro-om-peso-input' );
		if ( pesoInput ) return guardarPeso( pesoInput );

		var envioInput = e.target.closest( '.sanisidro-om-envio-input' );
		if ( envioInput ) return guardarEnvio( envioInput );
	} );

	function post( data ) {
		var body = new URLSearchParams();
		body.append( 'nonce', sanisidroOM.nonce );
		Object.keys( data ).forEach( function ( key ) { body.append( key, data[ key ] ); } );

		return fetch( sanisidroOM.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} ).then( function ( r ) { return r.json(); } );
	}

	function flashSaved( row ) {
		row.classList.remove( 'is-saving' );
		row.classList.add( 'is-saved' );
		setTimeout( function () { row.classList.remove( 'is-saved' ); }, 900 );
	}

	function guardarPeso( input ) {
		var row = input.closest( '.sanisidro-om-group' );
		var detailRow = input.closest( '.sanisidro-om-detail-row' );

		row.classList.remove( 'is-saved' );
		row.classList.add( 'is-saving' );

		post( { action: 'sanisidro_om_save_peso', order_id: input.dataset.order, product_id: input.dataset.product, peso: input.value } )
			.then( function ( json ) {
				if ( ! json.success ) { row.classList.remove( 'is-saving' ); return; }

				row.querySelector( '.sanisidro-om-total-real' ).innerHTML = json.data.total_real_html;

				if ( detailRow ) {
					var subtotalCell = detailRow.querySelector( '.sanisidro-om-subtotal' );
					if ( subtotalCell ) subtotalCell.innerHTML = json.data.subtotal_html;

					var totalCell = detailRow.querySelector( '.sanisidro-om-order-total' );
					if ( totalCell ) totalCell.innerHTML = '<strong>' + json.data.total_html + '</strong>';
				}

				flashSaved( row );
			} )
			.catch( function () { row.classList.remove( 'is-saving' ); } );
	}

	function guardarEnvio( input ) {
		var detailRow = input.closest( '.sanisidro-om-detail-row' );
		var row = input.closest( 'tr' );

		row.classList.add( 'is-saving' );

		post( { action: 'sanisidro_om_save_envio', order_id: input.dataset.order, envio: input.value } )
			.then( function ( json ) {
				if ( ! json.success ) { row.classList.remove( 'is-saving' ); return; }

				if ( detailRow ) {
					var totalCell = detailRow.querySelector( '.sanisidro-om-order-total' );
					if ( totalCell ) totalCell.innerHTML = '<strong>' + json.data.total_html + '</strong>';
				}

				flashSaved( row );
			} )
			.catch( function () { row.classList.remove( 'is-saving' ); } );
	}

	function generarWhatsapp( btn ) {
		var result = btn.parentElement.querySelector( '.sanisidro-om-whatsapp-result' );
		btn.disabled = true;
		result.textContent = sanisidroOM.strings.generando;

		post( { action: 'sanisidro_om_generar_whatsapp', order_id: btn.dataset.order } )
			.then( function ( json ) {
				btn.disabled = false;

				if ( ! json.success ) {
					var motivo = json.data === 'no_phone' ? sanisidroOM.strings.sinTelefono : sanisidroOM.strings.error;
					result.textContent = motivo;
					return;
				}

				result.innerHTML = '';

				var link = document.createElement( 'a' );
				link.href = json.data.whatsapp_url;
				link.target = '_blank';
				link.rel = 'noopener noreferrer';
				link.className = 'button';
				link.textContent = sanisidroOM.strings.abrirWhatsapp;
				result.appendChild( link );
			} )
			.catch( function () {
				btn.disabled = false;
				result.textContent = sanisidroOM.strings.error;
			} );
	}
} )();
