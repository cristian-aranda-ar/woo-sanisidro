<?php get_header(); ?>

<main id="main" class="nosotros-page">

	<!-- Hero -->
	<div class="recetas-hero">
		<div class="container recetas-hero__inner">
			<p class="recetas-hero__sup">Campo San Isidro</p>
			<h1 class="recetas-hero__title">Nosotros</h1>
		</div>
	</div>

	<!-- Contenido principal -->
	<section class="nosotros-intro container">
		<div class="nosotros-intro__texto">
			<span class="nosotros-label">Industria misionera desde 1994</span>
			<h2 class="nosotros-intro__titulo">Una empresa con raíces profundas en Misiones</h2>
			<p>Te invitamos a conocer <strong>Campo San Isidro</strong> en Garupá, una industria misionera dedicada a la producción de embutidos. La empresa forma parte de la fábrica del Frigorífico El Abasto, que comenzó a funcionar en 1994 distribuyendo carnes en Entre Ríos y Misiones.</p>
			<p>Campo San Isidro nació de la necesidad de diversificar productos y genera más de 70 puestos de trabajo, cuenta con instalaciones propias y mano de obra calificada, incorporando profesionales bromatólogos para controlar la manipulación, conservación, elaboración, sanidad y distribución de sus productos alimenticios.</p>
		</div>
		<div class="nosotros-intro__imagen" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/assets/images/si1.webp' ); ?>'); background-size:cover; background-position:center;"></div>
	</section>

	<!-- Stats -->
	<section class="nosotros-stats">
		<div class="container nosotros-stats__grid">
			<div class="nosotros-stat">
				<span class="nosotros-stat__num">1994</span>
				<span class="nosotros-stat__label">Año de fundación</span>
			</div>
			<div class="nosotros-stat">
				<span class="nosotros-stat__num">+70</span>
				<span class="nosotros-stat__label">Puestos de trabajo</span>
			</div>
			<div class="nosotros-stat">
				<span class="nosotros-stat__num">2</span>
				<span class="nosotros-stat__label">Provincias de distribución</span>
			</div>
			<div class="nosotros-stat">
				<span class="nosotros-stat__num">100%</span>
				<span class="nosotros-stat__label">Mano de obra calificada</span>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
