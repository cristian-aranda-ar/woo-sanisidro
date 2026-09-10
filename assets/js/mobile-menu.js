(function () {
	var toggle = document.querySelector( '.menu-toggle' );
	if ( ! toggle ) return;

	var body = document.body;

	function closeMenu() {
		body.classList.remove( 'nav-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
	}

	function openMenu() {
		body.classList.add( 'nav-open' );
		toggle.setAttribute( 'aria-expanded', 'true' );
	}

	toggle.addEventListener( 'click', function () {
		if ( body.classList.contains( 'nav-open' ) ) {
			closeMenu();
		} else {
			openMenu();
		}
	} );

	document.querySelectorAll( '.site-nav__list a' ).forEach( function ( link ) {
		link.addEventListener( 'click', closeMenu );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) closeMenu();
	} );
} )();
