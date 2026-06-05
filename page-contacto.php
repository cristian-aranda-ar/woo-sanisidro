<?php
get_header();

$enviado = false;
$error   = '';

if ( isset( $_POST['sanisidro_contact'] ) && wp_verify_nonce( $_POST['sanisidro_contact'], 'contacto' ) ) {
	$nombre  = sanitize_text_field( $_POST['nombre']  ?? '' );
	$email   = sanitize_email( $_POST['email']        ?? '' );
	$telefono = sanitize_text_field( $_POST['telefono'] ?? '' );
	$mensaje = sanitize_textarea_field( $_POST['mensaje'] ?? '' );

	if ( $nombre && $email && $mensaje ) {
		$to      = get_option( 'admin_email' );
		$subject = "Nuevo contacto desde el sitio — {$nombre}";
		$body    = "Nombre: {$nombre}\nEmail: {$email}\nTeléfono: {$telefono}\n\nMensaje:\n{$mensaje}";
		$headers = [ "Reply-To: {$nombre} <{$email}>", 'Content-Type: text/plain; charset=UTF-8' ];
		wp_mail( $to, $subject, $body, $headers );
		$enviado = true;
	} else {
		$error = 'Por favor completá todos los campos requeridos.';
	}
}
?>

<main id="main" class="contacto-page">

	<!-- Hero -->
	<div class="recetas-hero">
		<div class="container recetas-hero__inner">
			<p class="recetas-hero__sup">Campo San Isidro</p>
			<h1 class="recetas-hero__title">Contacto</h1>
		</div>
	</div>

	<div class="container contacto-content">

		<!-- Formulario -->
		<div class="contacto-form-wrap">
			<h2 class="contacto-section-title">Envianos un mensaje</h2>

			<?php if ( $enviado ) : ?>
				<div class="contacto-success">
					<p>✓ Tu mensaje fue enviado. Te contactaremos a la brevedad.</p>
				</div>
			<?php else : ?>

				<?php if ( $error ) : ?>
					<p class="contacto-error"><?php echo esc_html( $error ); ?></p>
				<?php endif; ?>

				<form class="contacto-form" method="post" action="">
					<?php wp_nonce_field( 'contacto', 'sanisidro_contact' ); ?>

					<div class="form-group">
						<label for="nombre">Nombre <span aria-hidden="true">*</span></label>
						<input type="text" id="nombre" name="nombre" required placeholder="Tu nombre completo" value="<?php echo esc_attr( $_POST['nombre'] ?? '' ); ?>">
					</div>

					<div class="form-group">
						<label for="email">Email <span aria-hidden="true">*</span></label>
						<input type="email" id="email" name="email" required placeholder="tu@email.com" value="<?php echo esc_attr( $_POST['email'] ?? '' ); ?>">
					</div>

					<div class="form-group">
						<label for="telefono">Teléfono</label>
						<input type="tel" id="telefono" name="telefono" placeholder="(376) 000-0000" value="<?php echo esc_attr( $_POST['telefono'] ?? '' ); ?>">
					</div>

					<div class="form-group">
						<label for="mensaje">Mensaje <span aria-hidden="true">*</span></label>
						<textarea id="mensaje" name="mensaje" required placeholder="¿En qué podemos ayudarte?" rows="5"><?php echo esc_textarea( $_POST['mensaje'] ?? '' ); ?></textarea>
					</div>

					<button type="submit" class="contacto-submit">Enviar mensaje</button>
				</form>

			<?php endif; ?>
		</div>

		<!-- Info + Mapa -->
		<div class="contacto-info-wrap">

			<div class="contacto-map">
				<iframe
					src="https://www.google.com/maps?q=Ruta+Nacional+12+km+8.5+Garupá+Misiones+Argentina&output=embed"
					width="100%"
					height="280"
					style="border:0;"
					allowfullscreen=""
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					title="Ubicación Campo San Isidro"
				></iframe>
			</div>

			<div class="contacto-datos">

				<div class="contacto-dato">
					<h3 class="contacto-dato__titulo">Dirección</h3>
					<p>Garupá, Misiones<br>Ruta Nacional 12, km 8,5</p>
				</div>

				<div class="contacto-dato">
					<h3 class="contacto-dato__titulo">Contacto</h3>
					<p>
						<a href="https://wa.me/5493764381746">WhatsApp: +549 3764 381746</a><br>
						<a href="tel:+543764480370">Teléfono: (376) 4480370</a><br>
						<a href="tel:+543764481804">Teléfono: (376) 4481804</a>
					</p>
				</div>

				<div class="contacto-dato">
					<h3 class="contacto-dato__titulo">Horarios de atención</h3>
					<p>
						<strong>Venta al público</strong><br>
						Lun — Sáb: 5.00 a 12.00 hs<br><br>
						<strong>Administración</strong><br>
						Lun — Vier: 8.00 a 16.00 hs<br>
						Sábados: 8.00 a 12.00 hs
					</p>
				</div>

			</div>
		</div>

	</div>

</main>

<?php get_footer(); ?>
