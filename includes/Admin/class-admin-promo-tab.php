<?php
/**
 * SNIPI Admin Promo Tab
 *
 * Vsebina taba "Promo diapozitiv" v edit screenu ekrana.
 * Omogoča izbiro promo objav za prikaz kot statičen diapozitiv —
 * bodisi kot dodatna stran po zadnji strani urnika (primer A)
 * bodisi kot edini prikaz kadar ni dogodkov (primer B).
 *
 * @package SNIPI_Ekrani
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SNIPI_Admin_Promo_Tab {

	/**
	 * Razpoložljive postavitve z oznakami in številom kolon
	 *
	 * @var array
	 */
	private static $layouts = array(
		'thirds'               => array( 'label' => '1/3 + 1/3 + 1/3', 'columns' => 3 ),
		'quarter-half-quarter' => array( 'label' => '1/4 + 1/2 + 1/4', 'columns' => 3 ),
		'halves'               => array( 'label' => '1/2 + 1/2',        'columns' => 2 ),
	);

	/**
	 * Renderaj vsebino taba Promo diapozitiv
	 *
	 * @param int   $post_id ID ekrana
	 * @param array $meta    Asociativni array z meta podatki
	 * @return void
	 */
	public static function render_content( $post_id, $meta ) {
		// Pridobi vse objavljene promo objave za selecte
		$promo_posts = get_posts( array(
			'post_type'      => 'promo_objava',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$current_layout = $meta['promo_layout'] ?? 'thirds';
		?>

		<!-- OMOGOČI PROMO DIAPOZITIV -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Promo diapozitiv', 'snipi-ekrani' ); ?></h3>

			<label class="snipi-checkbox-label">
				<input
					type="checkbox"
					id="snipi_promo_enabled"
					name="snipi_promo_enabled"
					value="1"
					<?php checked( $meta['promo_enabled'], '1' ); ?>
				/>
				<span><?php esc_html_e( 'Omogoči promo diapozitiv', 'snipi-ekrani' ); ?></span>
			</label>
			<p class="description">
				<?php esc_html_e( 'Ko je vključeno, se promo diapozitiv prikaže po zadnji strani urnika ali namesto urnika kadar ni dogodkov.', 'snipi-ekrani' ); ?>
			</p>
		</div>

		<!-- POSTAVITEV -->
		<div class="snipi-field-group">
			<label for="snipi_promo_layout" class="snipi-label">
				<?php esc_html_e( 'Postavitev kolon', 'snipi-ekrani' ); ?>
			</label>
			<select id="snipi_promo_layout" name="snipi_promo_layout">
				<?php foreach ( self::$layouts as $slug => $def ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $current_layout, $slug ); ?>>
						<?php echo esc_html( $def['label'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description">
				<?php esc_html_e( 'Določi razmerje širin kolon promo diapozitiva.', 'snipi-ekrani' ); ?>
			</p>
		</div>

		<!-- IZBIRA PROMO OBJAV -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Vsebina kolon', 'snipi-ekrani' ); ?></h3>

			<?php if ( empty( $promo_posts ) ) : ?>
				<div class="notice notice-warning inline">
					<p>
						<?php
						printf(
							/* translators: %s: URL do dodajanja promo objave */
							wp_kses(
								__( 'Ni promo objav. <a href="%s">Dodajte prvo promo objavo</a> preden konfigurirate diapozitiv.', 'snipi-ekrani' ),
								array( 'a' => array( 'href' => array() ) )
							),
							esc_url( admin_url( 'post-new.php?post_type=promo_objava' ) )
						);
						?>
					</p>
				</div>
			<?php else : ?>

				<!-- PRVA KOLONA -->
				<div class="snipi-field-group">
					<label for="snipi_promo_col1_id" class="snipi-label">
						<?php esc_html_e( 'Prva kolona', 'snipi-ekrani' ); ?>
					</label>
					<select id="snipi_promo_col1_id" name="snipi_promo_col1_id">
						<option value="0"><?php esc_html_e( '— Ni izbrano —', 'snipi-ekrani' ); ?></option>
						<?php foreach ( $promo_posts as $promo ) : ?>
							<option value="<?php echo intval( $promo->ID ); ?>" <?php selected( intval( $meta['promo_col1_id'] ), $promo->ID ); ?>>
								<?php echo esc_html( $promo->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- DRUGA KOLONA -->
				<div class="snipi-field-group">
					<label for="snipi_promo_col2_id" class="snipi-label">
						<?php esc_html_e( 'Druga kolona', 'snipi-ekrani' ); ?>
					</label>
					<select id="snipi_promo_col2_id" name="snipi_promo_col2_id">
						<option value="0"><?php esc_html_e( '— Ni izbrano —', 'snipi-ekrani' ); ?></option>
						<?php foreach ( $promo_posts as $promo ) : ?>
							<option value="<?php echo intval( $promo->ID ); ?>" <?php selected( intval( $meta['promo_col2_id'] ), $promo->ID ); ?>>
								<?php echo esc_html( $promo->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- TRETJA KOLONA (skrita pri 2-kolonski postavitvi) -->
				<div class="snipi-field-group" id="snipi-promo-col3-row">
					<label for="snipi_promo_col3_id" class="snipi-label">
						<?php esc_html_e( 'Tretja kolona', 'snipi-ekrani' ); ?>
					</label>
					<select id="snipi_promo_col3_id" name="snipi_promo_col3_id">
						<option value="0"><?php esc_html_e( '— Ni izbrano —', 'snipi-ekrani' ); ?></option>
						<?php foreach ( $promo_posts as $promo ) : ?>
							<option value="<?php echo intval( $promo->ID ); ?>" <?php selected( intval( $meta['promo_col3_id'] ), $promo->ID ); ?>>
								<?php echo esc_html( $promo->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'Ni na voljo pri postavitvi "1/2 + 1/2".', 'snipi-ekrani' ); ?>
					</p>
				</div>

			<?php endif; ?>
		</div>

		<!-- TRAJANJE IN PRAG -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Pogoji prikaza', 'snipi-ekrani' ); ?></h3>

			<div class="snipi-field-group snipi-field-group--inline">

				<!-- TRAJANJE DIAPOZITIVA -->
				<div class="snipi-field-inline">
					<label for="snipi_promo_duration" class="snipi-label">
						<?php esc_html_e( 'Trajanje (s)', 'snipi-ekrani' ); ?>
					</label>
					<input
						type="number"
						id="snipi_promo_duration"
						name="snipi_promo_duration"
						class="small-text"
						value="<?php echo esc_attr( $meta['promo_duration'] ); ?>"
						min="5"
						max="30"
					/>
				</div>

				<!-- PRAG PRAZNIH VRSTIC -->
				<div class="snipi-field-inline">
					<label for="snipi_promo_threshold" class="snipi-label">
						<?php esc_html_e( 'Prag praznih vrstic', 'snipi-ekrani' ); ?>
					</label>
					<input
						type="number"
						id="snipi_promo_threshold"
						name="snipi_promo_threshold"
						class="small-text"
						value="<?php echo esc_attr( $meta['promo_threshold'] ); ?>"
						min="1"
						max="10"
					/>
				</div>

			</div>
			<p class="description">
				<?php esc_html_e( 'Trajanje: koliko sekund je promo diapozitiv prikazan (5–30). Prag: koliko praznih vrstic mora ostati na zadnji strani preden se doda promo stran (1–10).', 'snipi-ekrani' ); ?>
			</p>
		</div>

		<!-- Inline JS za skrivanje tretje kolone pri 2-kolonski postavitvi -->
		<script>
		( function () {
			var layoutSelect = document.getElementById( 'snipi_promo_layout' );
			var col3Row      = document.getElementById( 'snipi-promo-col3-row' );

			if ( ! layoutSelect || ! col3Row ) {
				return;
			}

			function toggleCol3() {
				col3Row.style.display = ( layoutSelect.value === 'halves' ) ? 'none' : '';
			}

			layoutSelect.addEventListener( 'change', toggleCol3 );
			toggleCol3();
		}() );
		</script>
		<?php
	}
}
