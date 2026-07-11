<?php
/**
 * SNIPI Admin Promo Tab
 *
 * Vsebina taba "Promo diapozitiv" v edit screenu ekrana.
 * Dva neodvisna primera:
 *  A. Zapolnitev — promo objava zapolni prazne vrstice na zadnji strani urnika
 *  B. Promo diapozitivi — promo_slide rotacija kot dodaten diapozitiv ali celozaslonski prikaz
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

		$slide_posts = get_posts( array(
			'post_type'      => 'promo_slide',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$current_layout   = $meta['promo_layout'] ?? 'thirds';
		$selected_slide_ids = is_array( $meta['promo_slide_ids'] ) ? $meta['promo_slide_ids'] : array();
		?>

		<!-- ================================================================
		     PRIMER A — ZAPOLNITEV (PROMO OBJAVA)
		     ================================================================ -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Primer A — Zapolnitev (Promo Objava)', 'snipi-ekrani' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Promo vsebina iz kolon (Promo Objava) se doda kot dodatna stran v rotacijo urnika, kadar na zadnji strani ostane dovolj praznih vrstic.', 'snipi-ekrani' ); ?>
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

			<!-- Postavitev in vsebina kolon -->
			<div class="snipi-field-group" style="margin-top:16px;">
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
					<?php esc_html_e( 'Določi razmerje širin kolon promo vsebine.', 'snipi-ekrani' ); ?>
				</p>
			</div>

			<?php if ( empty( $promo_posts ) ) : ?>
				<div class="notice notice-warning inline">
					<p>
						<?php
						printf(
							wp_kses(
								/* translators: %s: URL do dodajanja promo objave */
								__( 'Ni promo objav. <a href="%s">Dodajte prvo promo objavo</a> preden konfigurirate Primer A.', 'snipi-ekrani' ),
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

		<!-- ================================================================
		     PRIMER B — PROMO DIAPOZITIVI
		     ================================================================ -->
		<div class="snipi-field-group">
			<h3><?php esc_html_e( 'Primer B — Promo Diapozitivi', 'snipi-ekrani' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Celozaslonski diapozitivi iz knjižnice Promo Diapozitivi. Neodvisno od Primera A.', 'snipi-ekrani' ); ?>
			</p>

			<!-- Izbira diapozitivov -->
			<div class="snipi-field-group">
				<label class="snipi-label"><?php esc_html_e( 'Izberi diapozitive', 'snipi-ekrani' ); ?></label>

				<?php if ( empty( $slide_posts ) ) : ?>
					<div class="notice notice-warning inline">
						<p>
							<?php
							printf(
								wp_kses(
									/* translators: %s: URL do dodajanja promo diapozitiva */
									__( 'Ni promo diapozitivov. <a href="%s">Dodajte prvi promo diapozitiv</a> preden konfigurirate Primer B.', 'snipi-ekrani' ),
									array( 'a' => array( 'href' => array() ) )
								),
								esc_url( admin_url( 'post-new.php?post_type=promo_slide' ) )
							);
							?>
						</p>
					</div>
				<?php else : ?>
					<div class="snipi-checkbox-list">
						<?php foreach ( $slide_posts as $slide ) : ?>
							<label class="snipi-checkbox-label">
								<input
									type="checkbox"
									name="snipi_promo_slide_ids[]"
									value="<?php echo intval( $slide->ID ); ?>"
									<?php checked( in_array( $slide->ID, $selected_slide_ids, true ) ); ?>
								/>
								<span><?php echo esc_html( $slide->post_title ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="description">
						<?php esc_html_e( 'Izberi 1–3 diapozitive za prikaz na tem ekranu. Prikazani bodo v vrstnem redu, ki si ga določiš z naslovi.', 'snipi-ekrani' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<!-- Trajanje posameznega diapozitiva -->
			<div class="snipi-field-group">
				<label for="snipi_promo_slide_duration" class="snipi-label">
					<?php esc_html_e( 'Trajanje diapozitiva (s)', 'snipi-ekrani' ); ?>
				</label>
				<input
					type="number"
					id="snipi_promo_slide_duration"
					name="snipi_promo_slide_duration"
					class="small-text"
					value="<?php echo esc_attr( $meta['promo_slide_duration'] ); ?>"
					min="10"
					max="60"
				/>
				<p class="description">
					<?php esc_html_e( 'Koliko sekund je vsak diapozitiv prikazan (10–60).', 'snipi-ekrani' ); ?>
				</p>
			</div>

			<!-- Sproži: rotacija -->
			<div class="snipi-field-group">
				<label class="snipi-checkbox-label">
					<input
						type="checkbox"
						id="snipi_promo_slide_show_in_rotation"
						name="snipi_promo_slide_show_in_rotation"
						value="1"
						<?php checked( $meta['promo_slide_show_in_rotation'], '1' ); ?>
					/>
					<span><?php esc_html_e( 'Prikaži kot dodaten diapozitiv po urniku', 'snipi-ekrani' ); ?></span>
				</label>
				<p class="description">
					<?php esc_html_e( 'Diapozitivi se dodajo v rotacijo za zadnjo stranjo urnika.', 'snipi-ekrani' ); ?>
				</p>
			</div>

			<!-- Sproži: prazno -->
			<div class="snipi-field-group">
				<label class="snipi-checkbox-label">
					<input
						type="checkbox"
						id="snipi_promo_slide_show_on_empty"
						name="snipi_promo_slide_show_on_empty"
						value="1"
						<?php checked( $meta['promo_slide_show_on_empty'], '1' ); ?>
					/>
					<span><?php esc_html_e( 'Prikaži čez cel zaslon, ko ni dogodkov', 'snipi-ekrani' ); ?></span>
				</label>
				<p class="description">
					<?php esc_html_e( 'Ko za ta dan ni nobenega dogodka, diapozitivi v celoti nadomestijo urnik. Privzeto izklopljeno — vklopi samo ko je vsebina pripravljena.', 'snipi-ekrani' ); ?>
				</p>
			</div>
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
