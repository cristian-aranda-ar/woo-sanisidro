<?php
defined( 'ABSPATH' ) || exit;

/**
 * Traducciones de WooCommerce al español (Argentina).
 */
add_filter( 'gettext', 'sanisidro_wc_translate', 10, 3 );
add_filter( 'ngettext', 'sanisidro_wc_translate_plural', 10, 5 );

function sanisidro_wc_translate( string $translated, string $original, string $domain ): string {
	if ( $domain !== 'woocommerce' ) return $translated;

	$strings = [
		// Botones generales
		'Add to cart'                        => 'Agregar al carrito',
		'Read more'                          => 'Comprar',
		'Select options'                     => 'Ver opciones',
		'View cart'                          => 'Ver carrito',
		'Update cart'                        => 'Actualizar carrito',
		'Proceed to checkout'                => 'Ir al pago',
		'Place order'                        => 'Realizar pedido',
		'Continue shopping'                  => 'Seguir comprando',
		'Return to shop'                     => 'Volver a la tienda',
		'Apply coupon'                       => 'Aplicar cupón',
		'Remove'                             => 'Eliminar',
		'Edit'                               => 'Editar',
		'Search'                             => 'Buscar',

		// Notificaciones / Mensajes
		'Product added to cart'              => 'Producto agregado al carrito',
		'Item added to your cart'            => 'Producto agregado a tu carrito',
		'has been added to your cart'        => 'ha sido agregado a tu carrito',
		'%s has been added to your cart'     => '%s ha sido agregado a tu carrito',
		'Product removed from cart'          => 'Producto eliminado del carrito',
		'Cart updated'                       => 'Carrito actualizado',
		'Cart cleared'                       => 'Carrito vaciado',

		// Stock
		'In stock'                           => 'En stock',
		'Out of stock'                       => 'Sin stock',
		'Available on backorder'             => 'Disponible bajo pedido',
		'On backorder'                       => 'Bajo pedido',
		'Sale!'                              => '¡Oferta!',

		// Carrito
		'Cart'                               => 'Carrito',
		'Your cart is currently empty.'      => 'Tu carrito está vacío.',
		'Cart totals'                        => 'Totales del carrito',
		'Subtotal'                           => 'Subtotal',
		'Total'                              => 'Total',
		'Shipping'                           => 'Envío',
		'Coupon:'                            => 'Cupón:',
		'Coupon code'                        => 'Código de cupón',
		'Tax'                                => 'Impuesto',
		'Taxes'                              => 'Impuestos',
		'Fee'                                => 'Cargo',
		'Discount'                           => 'Descuento',
		'%s item removed.'                   => '%s producto eliminado.',
		'Undo?'                              => '¿Deshacer?',

		// Checkout
		'Checkout'                           => 'Finalizar compra',
		'Billing details'                    => 'Datos de facturación',
		'Billing address'                    => 'Dirección de facturación',
		'Shipping details'                   => 'Datos de envío',
		'Ship to a different address?'       => '¿Enviar a una dirección diferente?',
		'Order notes'                        => 'Notas del pedido',
		'Notes about your order'             => 'Notas sobre tu pedido',
		'Your order'                         => 'Tu pedido',
		'Payment'                            => 'Pago',
		'Payment method'                     => 'Método de pago',
		'I have read and agree to the website %s'
		                                     => 'He leído y acepto los %s del sitio web',
		'terms and conditions'               => 'términos y condiciones',

		// Campos de formulario
		'First name'                         => 'Nombre',
		'Last name'                          => 'Apellido',
		'Company name (optional)'            => 'Empresa (opcional)',
		'Country / Region'                   => 'País / Región',
		'Street address'                     => 'Dirección',
		'Apartment, suite, unit, etc. (optional)' => 'Piso, depto, etc. (opcional)',
		'Town / City'                        => 'Ciudad',
		'State / County'                     => 'Provincia',
		'Postcode / ZIP'                     => 'Código postal',
		'Phone'                              => 'Teléfono',
		'Email address'                      => 'Correo electrónico',
		'Order notes (optional)'             => 'Notas del pedido (opcional)',
		'Username'                           => 'Usuario',
		'Password'                           => 'Contraseña',
		'Remember me'                        => 'Recordarme',
		'Log in'                             => 'Ingresar',
		'Lost your password?'                => '¿Olvidaste tu contraseña?',
		'Register'                           => 'Registrarse',

		// Mi cuenta
		'My account'                         => 'Mi cuenta',
		'My Account'                         => 'Mi cuenta',
		'Orders'                             => 'Pedidos',
		'Order'                              => 'Pedido',
		'Downloads'                          => 'Descargas',
		'Addresses'                          => 'Direcciones',
		'Account details'                    => 'Detalles de la cuenta',
		'Logout'                             => 'Cerrar sesión',
		'Log out'                            => 'Cerrar sesión',
		'Dashboard'                          => 'Panel',
		'Hello %s'                           => 'Hola %s',
		'No order has been made yet.'        => 'Todavía no realizaste ningún pedido.',
		'Browse products'                    => 'Ver productos',

		// Tienda / catálogo
		'Shop'                               => 'Tienda',
		'Products'                           => 'Productos',
		'Product'                            => 'Producto',
		'Category'                           => 'Categoría',
		'Categories'                         => 'Categorías',
		'Tag'                                => 'Etiqueta',
		'Tags'                               => 'Etiquetas',
		'Price'                              => 'Precio',
		'Quantity'                           => 'Cantidad',
		'SKU'                                => 'Código',
		'SKU:'                               => 'Código:',
		'N/A'                                => 'N/D',
		'Default sorting'                    => 'Orden predeterminado',
		'Sort by popularity'                 => 'Más populares',
		'Sort by average rating'             => 'Mejor valorados',
		'Sort by latest'                     => 'Más recientes',
		'Sort by price: low to high'         => 'Precio: menor a mayor',
		'Sort by price: high to low'         => 'Precio: mayor a menor',
		'No products found'                  => 'No se encontraron productos',
		'No products were found matching your selection.' => 'No se encontraron productos que coincidan con tu búsqueda.',

		// Producto individual
		'Description'                        => 'Descripción',
		'Additional information'             => 'Información adicional',
		'Reviews'                            => 'Reseñas',
		'Related products'                   => 'Productos relacionados',
		'You may also like&hellip;'          => 'También te puede gustar&hellip;',
		'Product categories'                 => 'Categorías',
		'Product tags'                       => 'Etiquetas',
		'Posted in'                          => 'Categoría:',
		'Tagged'                             => 'Etiquetas:',
		'Add a review'                       => 'Dejar una reseña',
		'Your rating'                        => 'Tu calificación',
		'Your review'                        => 'Tu reseña',
		'Submit'                             => 'Enviar',
		'Be the first to review &ldquo;%s&rdquo;' => 'Sé el primero en reseñar &ldquo;%s&rdquo;',
		'There are no reviews yet.'          => 'Todavía no hay reseñas.',

		// Mensajes / errores
		'Billing and shipping addresses are the same.'
		                                     => 'La dirección de facturación y envío son iguales.',
		'Please fill in your details above to see available shipping methods.'
		                                     => 'Completá tus datos para ver los métodos de envío.',
		'No shipping options were found.'    => 'No se encontraron opciones de envío.',
		'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.'
		                                     => 'No hay métodos de pago disponibles. Contactanos si necesitás ayuda.',
		'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.'
		                                     => 'No hay métodos de pago disponibles. Contactanos si necesitás ayuda.',

		// Breadcrumb / Home
		'Home'                               => 'Inicio',
		'Search results for &ldquo;%s&rdquo;' => 'Resultados para &ldquo;%s&rdquo;',

		// Result count — forma singular ya resuelta por gettext
		'Showing the single result'          => 'Mostrando el único resultado',
	];

	return $strings[ $original ] ?? $translated;
}

function sanisidro_wc_translate_plural( string $translated, string $single, string $plural, int $number, string $domain ): string {
	if ( $domain !== 'woocommerce' ) return $translated;

	$strings = [
		// result-count.php
		'Showing all %d result'                          => [ 'Mostrando el único resultado',            'Mostrando los %d resultados'             ],
		'Showing all %d results'                         => [ 'Mostrando el único resultado',            'Mostrando los %d resultados'             ],
		'Showing %1$d&ndash;%2$d of %3$d result'         => [ 'Mostrando %1$d&ndash;%2$d de %3$d resultado', 'Mostrando %1$d&ndash;%2$d de %3$d resultados' ],
		'Showing %1$d&ndash;%2$d of %3$d results'        => [ 'Mostrando %1$d&ndash;%2$d de %3$d resultado', 'Mostrando %1$d&ndash;%2$d de %3$d resultados' ],
		// Genéricos
		'%d item'                                        => [ '%d producto',                             '%d productos'                            ],
		'%d Item'                                        => [ '%d producto',                             '%d productos'                            ],
		'%d result found'                                => [ '%d resultado encontrado',                  '%d resultados encontrados'               ],
		'%d results found'                               => [ '%d resultado encontrado',                  '%d resultados encontrados'               ],
	];

	foreach ( $strings as $key => $forms ) {
		if ( $single === $key || $plural === $key ) {
			return $number === 1 ? $forms[0] : $forms[1];
		}
	}

	return $translated;
}
