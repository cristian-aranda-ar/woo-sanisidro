( function () {
	const slides = document.querySelectorAll( '.hero-slide' );
	if ( slides.length < 2 ) return;

	let current = 0;

	setInterval( function () {
		slides[ current ].classList.remove( 'hero-slide--active' );
		current = ( current + 1 ) % slides.length;
		slides[ current ].classList.add( 'hero-slide--active' );
	}, 5000 );
} )();
