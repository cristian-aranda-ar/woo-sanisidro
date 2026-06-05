<?php
/**
 * Importador de recetas — ejecutar UNA SOLA VEZ.
 * Desde el panel admin ir a: /wp-admin/?sanisidro_recetas=1
 * Eliminar este archivo luego de ejecutar.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', function () {
	if ( ! isset( $_GET['sanisidro_recetas'] ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	// Categoría "Recetas"
	$cat = get_category_by_slug( 'recetas' );
	if ( ! $cat ) {
		$cat_id = wp_insert_term( 'Recetas', 'category', [ 'slug' => 'recetas' ] );
		$cat_id = is_wp_error( $cat_id ) ? 1 : $cat_id['term_id'];
	} else {
		$cat_id = $cat->term_id;
	}

	$recetas = [

		/* ── 1. OSOBUCO ─────────────────────────────────────── */
		[
			'title' => 'Osobuco de novillo a la criolla',
			'slug'  => 'osobuco-de-novillo-a-la-criolla',
			'content' => '
<p>El osobuco a la criolla es uno de esos platos que definen el domingo argentino. Cocción lenta, caldo que perfuma toda la casa y esa carne que se desprende sola del hueso. Simple, rendidor y con todo el sabor del campo.</p>

<h2>Ingredientes</h2>
<ul>
<li>4 rodajas de osobuco de novillo (aprox. 500 g cada una)</li>
<li>2 cebollas medianas cortadas en pluma</li>
<li>3 dientes de ajo picados</li>
<li>2 tomates peritas maduros, picados</li>
<li>1 pimiento rojo cortado en tiras</li>
<li>1 zanahoria cortada en rodajas</li>
<li>1 taza de vino tinto (Malbec o Cabernet)</li>
<li>1½ taza de caldo de carne</li>
<li>Sal gruesa y pimienta negra recién molida</li>
<li>Laurel, orégano, pimentón dulce</li>
<li>Aceite para sellar</li>
</ul>

<h2>Preparación</h2>

<h3>1. Sellar la carne</h3>
<p>Salpimentar generosamente cada rodaja de osobuco por ambas caras. Calentar una olla o cacerola de fondo grueso con un chorrito de aceite a fuego bien alto. Sellar la carne de a dos o tres rodajas por vez, 3 minutos por lado, hasta que tome buen color dorado. Reservar.</p>

<h3>2. Rehogar las verduras</h3>
<p>En la misma olla, bajar el fuego a medio y rehogar la cebolla y el ajo hasta que estén transparentes, unos 5 minutos. Agregar el pimiento, la zanahoria y el tomate. Cocinar 5 minutos más revolviendo.</p>

<h3>3. Desglasar y cocinar</h3>
<p>Verter el vino tinto y raspar el fondo de la olla con una cuchara de madera para levantar todos los jugos del sellado. Agregar el caldo, el laurel, el orégano y una cucharadita de pimentón. Volver el osobuco a la olla, que el líquido llegue hasta la mitad de las rodajas. Tapar y cocinar a fuego muy bajo durante <strong>2 horas y media a 3 horas</strong>, dando vuelta la carne a mitad de cocción.</p>

<h3>4. Servir</h3>
<p>El osobuco está listo cuando la carne se separa del hueso con apenas tocarlo. Servir sobre puré rústico de papas o polenta cremosa, con abundante salsa de la cocción por encima.</p>

<p><strong>Tip:</strong> La médula del hueso es el mejor bocado. Sacarla con una cucharita y untarla en pan es obligatorio.</p>
',
		],

		/* ── 2. COSTILLA Y VACÍO A LA PARRILLA ──────────────── */
		[
			'title' => 'Costilla y vacío a la parrilla',
			'slug'  => 'costilla-y-vacio-a-la-parrilla',
			'content' => '
<p>Si hay un asado que no falla en ninguna reunión argentina, es la combinación de costilla y vacío. Dos cortes distintos, dos texturas diferentes, el mismo fuego de siempre. Acá va el método para sacarle el mejor partido a los dos.</p>

<h2>Ingredientes</h2>
<ul>
<li>1 costilla de novillo entera (2 kg aprox.)</li>
<li>1 vacío de novillo (1,2 kg aprox.)</li>
<li>Sal gruesa, cantidad necesaria</li>
<li>Chimichurri para acompañar</li>
</ul>

<h3>Para el chimichurri</h3>
<ul>
<li>1 taza de perejil fresco picado fino</li>
<li>4 dientes de ajo picados</li>
<li>1 cucharadita de orégano seco</li>
<li>½ cucharadita de ají molido</li>
<li>½ taza de aceite de oliva</li>
<li>3 cucharadas de vinagre de manzana</li>
<li>Sal y pimienta</li>
</ul>

<h2>Preparación</h2>

<h3>1. Preparar el fuego</h3>
<p>Armar el fuego con leña o carbón con anticipación. El secreto del asado argentino es la brasa pareja, sin llama. Esperar a que el carbón esté blanco y la brasa tenga temperatura estable antes de poner la carne. La parrilla tiene que estar limpia y caliente.</p>

<h3>2. Salar la carne</h3>
<p>Salar la costilla y el vacío con sal gruesa por el lado de la grasa únicamente. No pinchar ni abrir la carne. La sal se aplica justo antes de llevar a la parrilla.</p>

<h3>3. Cocinar la costilla</h3>
<p>Colocar la costilla con el hueso hacia abajo primero, a fuego medio. <strong>45 a 60 minutos</strong> de ese lado sin tocarla. La costilla se cocina desde el hueso hacia afuera: cuando los extremos empiezan a tomar color, dar vuelta. Cocinar del lado de la carne otros 20 a 30 minutos. La costilla está lista cuando al pincharla los jugos salen claros.</p>

<h3>4. Cocinar el vacío</h3>
<p>El vacío va con el cuero hacia abajo al principio, a fuego fuerte, para que se forme una costra crocante. Unos <strong>20 minutos</strong> de ese lado. Dar vuelta y cocinar a fuego más suave otros 15 a 20 minutos. El vacío debe quedar jugoso por dentro, nunca seco.</p>

<h3>5. Chimichurri</h3>
<p>Mezclar todos los ingredientes del chimichurri, ajustar sal y dejar reposar al menos 30 minutos antes de servir. Mejor aún si se prepara el día anterior.</p>

<h3>6. Servir</h3>
<p>Dejar descansar la carne 5 minutos antes de cortar. Servir la costilla en porciones individuales y el vacío en tiras finas al bies. Chimichurri aparte, pan casero y listo.</p>

<p><strong>Tip:</strong> El vacío tiene una capa de grasa exterior que hay que dejar: protege la carne durante la cocción y le da sabor. Se retira al momento de comer si se quiere.</p>
',
		],

		/* ── 3. BONDIOLA DE CERDO ────────────────────────────── */
		[
			'title' => 'Bondiola de cerdo a la parrilla con papas a la provenzal',
			'slug'  => 'bondiola-de-cerdo-a-la-parrilla',
			'content' => '
<p>La bondiola de cerdo es uno de los cortes más populares de la parrilla argentina. Tiene la cantidad justa de grasa entreverada para quedar súper tierna y jugosa sin necesidad de marinadas complicadas. Acá la hacemos con papas a la provenzal, que son el acompañamiento perfecto.</p>

<h2>Ingredientes</h2>
<ul>
<li>1 bondiola de cerdo (1,5 kg aprox.)</li>
<li>Sal gruesa</li>
<li>Pimienta negra molida</li>
</ul>

<h3>Para las papas a la provenzal</h3>
<ul>
<li>6 papas medianas</li>
<li>4 dientes de ajo picados muy finos</li>
<li>1 taza de perejil fresco picado</li>
<li>Aceite de oliva</li>
<li>Sal y pimienta</li>
</ul>

<h2>Preparación</h2>

<h3>1. Preparar la bondiola</h3>
<p>Sacar la bondiola de la heladera al menos 30 minutos antes de cocinar para que llegue a temperatura ambiente. Secarla bien con papel absorbente. Salpimentar por todos lados con generosidad.</p>

<h3>2. Sellar a fuego fuerte</h3>
<p>Llevar la bondiola a la parrilla a fuego bien alto para sellarla por todos los lados, aproximadamente <strong>4 a 5 minutos por lado</strong>. Este paso es clave para formar una costra sabrosa que retenga los jugos.</p>

<h3>3. Cocción lenta</h3>
<p>Una vez sellada, bajar la parrilla o reducir el fuego a temperatura media-baja. Cocinar la bondiola durante <strong>1 hora a 1 hora y media</strong>, girándola cada 15 a 20 minutos para que la cocción sea pareja. La grasa se irá derritiendo lentamente e impregnando toda la carne. Está lista cuando al pinchar el centro con un palillo los jugos salen claros, no rosados.</p>

<h3>4. Papas a la provenzal</h3>
<p>Cortar las papas en cubos medianos y cocinarlas en agua con sal hasta que estén tiernas pero firmes. Escurrir. En una sartén con aceite de oliva caliente, saltar las papas hasta dorar. Apagar el fuego, agregar el ajo y el perejil, mezclar bien y rectificar la sal. El ajo no debe cocinarse con la llama prendida para que no se queme y amargue.</p>

<h3>5. Servir</h3>
<p>Dejar reposar la bondiola 5 minutos antes de cortar. Cortar en rodajas de 1,5 cm de grosor. Servir junto a las papas a la provenzal y, si se quiere, un poco de mostaza artesanal a un costado.</p>

<p><strong>Tip:</strong> Si te sobra bondiola, al otro día hacé un sándwich con pan de campo, queso provolone y tomate. No falla.</p>
',
		],

	];

	foreach ( $recetas as $r ) {
		$existe = get_page_by_path( $r['slug'], OBJECT, 'post' );
		if ( $existe ) continue;

		wp_insert_post( [
			'post_title'    => $r['title'],
			'post_name'     => $r['slug'],
			'post_content'  => trim( $r['content'] ),
			'post_status'   => 'publish',
			'post_type'     => 'post',
			'post_category' => [ $cat_id ],
		] );
	}

	wp_die( '<p style="font-family:sans-serif;padding:2rem;">✅ <strong>3 recetas importadas correctamente.</strong><br>Podés eliminar el archivo <code>inc/import-recetas.php</code> y remover el require de <code>functions.php</code>.</p>' );
} );
