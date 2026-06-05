/* Animar elementos al cargar la página */
( function () {
	'use strict';

	// Mapeo de selectores a variantes de animación
	const animationMap = [
		// Home - Productos
		{ selector: '.home-productos .producto-card', variant: 'up' },
		{ selector: '.home-productos h2', variant: 'up' },

		// Home - Categorías
		{ selector: '.home-categorias__imagen', variant: 'left' },
		{ selector: '.home-categorias__texto', variant: 'right' },

		// Home - Manifiesto
		{ selector: '.home-manifiesto', variant: 'up' },

		// Home - Nosotros
		{ selector: '.home-nosotros__imagen', variant: 'right' },
		{ selector: '.home-nosotros__texto', variant: 'left' },

		// Home - Recetas
		{ selector: '.home-recetas h2', variant: 'up' },
		{ selector: '.home-recetas .receta-card', variant: 'scale' },

		// Home - Instagram
		{ selector: '.home-instagram .ig-item', variant: 'scale' },

		// Recetas Archive
		{ selector: '.recetas-grid .receta-card', variant: 'up' },
		{ selector: '.recetas-hero__title', variant: 'up' },

		// Nosotros
		{ selector: '.nosotros-intro h2', variant: 'up' },
		{ selector: '.nosotros-intro__texto', variant: 'left' },
		{ selector: '.nosotros-intro__imagen', variant: 'right' },
		{ selector: '.nosotros-stat', variant: 'scale' },

		// Contacto
		{ selector: '.contacto-form', variant: 'left' },
		{ selector: '.contacto-map', variant: 'right' },
		{ selector: '.contacto-dato', variant: 'up' },

		// Single Product
		{ selector: '.single-product .product-gallery', variant: 'left' },
		{ selector: '.single-product .product-info', variant: 'right' },

		// Archive Products
		{ selector: '.archive-product .producto-card', variant: 'up' },
	];

	// Inyectar observer cuando el DOM esté listo
	document.addEventListener( 'DOMContentLoaded', () => {
		// Crear Intersection Observer para elementos en viewport
		const observerOptions = {
			threshold: 0.1,
			rootMargin: '0px 0px -50px 0px',
		};

		const observer = new IntersectionObserver( ( entries ) => {
			entries.forEach( ( entry, index ) => {
				if ( entry.isIntersecting ) {
					// Pequeño delay escalonado
					setTimeout( () => {
						entry.target.classList.add( 'animate-in' );
					}, index * 50 );
					observer.unobserve( entry.target );
				}
			} );
		}, observerOptions );

		// Aplicar animaciones a cada elemento encontrado
		animationMap.forEach( ( { selector, variant } ) => {
			document.querySelectorAll( selector ).forEach( ( el, index ) => {
				// Asignar clase de variante
				el.classList.add( `animate-in--${ variant }` );

				// Observar para animar cuando entra en viewport
				observer.observe( el );
			} );
		} );
	} );
} )();
