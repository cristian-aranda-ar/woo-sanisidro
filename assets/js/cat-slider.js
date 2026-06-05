( function () {
	const slides = document.querySelectorAll( '#categoriasSlider .cat-slide' );
	if ( slides.length < 2 ) return;

	let current = 0;

	function next() {
		slides[ current ].classList.remove( 'cat-slide--active' );
		current = ( current + 1 ) % slides.length;
		slides[ current ].classList.add( 'cat-slide--active' );
	}

	setInterval( next, 4000 );
} )();
