<?php
/**
 * SNIPI Admin Promo Tab
 *
 * Vsebina taba "Promo diapozitiv" v edit screenu ekrana.
 * Dva neodvisna primera:
 *  A. Zapolnitev — promo se doda kot dodatna stran v rotaciji urnika
 *  B. Celozaslonski prikaz — promo nadomesti urnik kadar ni dogodkov
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
		$promo_posts = get_posts( array(
			'post_type'      => 'promo_objava',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$current_layout = $meta['promo_layout'] ?? 'thirds';
		?>

		<!-- ================================================================
		     PRIMER A — ZAPOLNITEV PRAZNEGA PROSTORA
		     ================================================================ -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Primer A — Zapolnitev praznega prostora', 'snipi-ekrani' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Promo diapozitiv se doda kot dodatna stran v rotacijo urnika, kadar na zadnji strani ostane dovolj praznih vrstic.', 'snipi-ekrani' ); ?>
			</p>

			<label class="snipi-checkbox-label">
				<input
					type="checkbox"
					id="snipi_promo_fill_enabled"
					name="snipi_promo_fill_enabled"
					value="1"
					<?php checked( $meta['promo_fill_enabled'], '1' ); ?>
				/>
				<span><?php esc_html_e( 'Omogoči zapolnitev praznega prostora', 'snipi-ekrani' ); ?></span>
			</label>

			<div class="snipi-field-group snipi-field-group--inline" style="margin-top:12px;">

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
				<?php esc_html_e( 'Trajanje: koliko sekund je promo prikazan (5–30). Prag: koliko praznih vrstic mora ostati na zadnji strani (1–10).', 'snipi-ekrani' ); ?>
			</p>
		</div>

		<!-- ================================================================
		     PRIMER B — CELOZASLONSKI PRIKAZ
		     ================================================================ -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Primer B — Celozaslonski prikaz', 'snipi-ekrani' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Ko za ta dan ni nobenega dogodka, promo v celoti nadomesti urnik. Neodvisno od Primera A.', 'snipi-ekrani' ); ?>
			</p>

			<label class="snipi-checkbox-label">
				<input
					type="checkbox"
					id="snipi_promo_takeover_enabled"
					name="snipi_promo_takeover_enabled"
					value="1"
					<?php checked( $meta['promo_takeover_enabled'], '1' ); ?>
				/>
				<span><?php esc_html_e( 'Omogoči celozaslonski prikaz', 'snipi-ekrani' ); ?></span>
			</label>

			<div style="margin-top:16px;">

				<div class="snipi-field-group">
					<label for="snipi_promo_takeover_bg_color" class="snipi-label">
						<?php esc_html_e( 'Barva ozadja', 'snipi-ekrani' ); ?>
					</label>
					<input
						type="text"
						id="snipi_promo_takeover_bg_color"
						name="snipi_promo_takeover_bg_color"
						class="snipi-color-picker"
						value="<?php echo esc_attr( $meta['promo_takeover_bg_color'] ); ?>"
						data-default-color=""
					/>
					<p class="description">
						<?php esc_html_e( 'Barva ozadja celozaslonskega promo prikaza. Prazno = barva ozadja zaslona.', 'snipi-ekrani' ); ?>
					</p>
				</div>

				<div class="snipi-field-group">
					<label class="snipi-checkbox-label">
						<input
							type="checkbox"
							id="snipi_promo_takeover_show_logo"
							name="snipi_promo_takeover_show_logo"
							value="1"
							<?php checked( $meta['promo_takeover_show_logo'], '1' ); ?>
						/>
						<span><?php esc_html_e( 'Prikaži logotip', 'snipi-ekrani' ); ?></span>
					</label>
					<p class="description">
						<?php esc_html_e( 'Prikaže logotip ekrana nad promo kolonami (logotip je nastavljen v zavihku Nastavitve).', 'snipi-ekrani' ); ?>
					</p>
				</div>

				<div class="snipi-field-group">
					<label for="snipi_promo_takeover_heading" class="snipi-label">
						<?php esc_html_e( 'Napis nad kolonami (neobvezno)', 'snipi-ekrani' ); ?>
					</label>
					<input
						type="text"
						id="snipi_promo_takeover_heading"
						name="snipi_promo_takeover_heading"
						class="regular-text"
						value="<?php echo esc_attr( $meta['promo_takeover_heading'] ); ?>"
						placeholder="<?php esc_attr_e( 'npr. Aktualno pri nas', 'snipi-ekrani' ); ?>"
					/>
					<p class="description">
						<?php esc_html_e( 'Besedilo, ki se prikaže nad promo kolonami. Pusti prazno, če napisa ne želiš.', 'snipi-ekrani' ); ?>
					</p>
				</div>

			</div>
		</div>

		<!-- ================================================================
		     SKUPNO — POSTAVITEV IN VSEBINA KOLON
		     ================================================================ -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Postavitev in vsebina kolon', 'snipi-ekrani' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Velja za oba primera. Izberi postavitev in dodeli promo objavo vsaki koloni.', 'snipi-ekrani' ); ?>
			</p>

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

			<?php if ( empty( $promo_posts ) ) : ?>
				<div class="notice notice-warning inline">
					<p>
						<?php
						printf(
							wp_kses(
								/* translators: %s: URL do dodajanja promo objave */
								__( 'Ni promo objav. <a href="%s">Dodajte prvo promo objavo</a> preden konfigurirate diapozitiv.', 'snipi-ekrani' ),
								array( 'a' => array( 'href' => array() ) )
							),
							esc_url( admin_url( 'post-new.php?post_type=promo_objava' ) )
						);
						?>
					</p>
				</div>
			<?php else : ?>

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

		<!-- Inline JS: skrij tretjo kolono pri 2-kolonski postavitvi -->
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
