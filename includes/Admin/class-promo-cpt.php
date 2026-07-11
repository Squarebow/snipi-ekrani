<?php
/**
 * Promo Objava CPT
 *
 * Registracija Custom Post Type 'promo_objava'.
 * Samostojen tip vsebine za promocijske objave (slike, naslovi, besedilo),
 * ki ga ekrani povlečejo iz skupne knjižnice — ni vezan na posamezen ekran.
 *
 * Funkcionalnosti:
 * - Registracija CPT z naslovom, urednikm in featured image
 * - Omejitev vsebine na 200 znakov (backend truncate + frontend števec)
 * - Pomoč za featured image (razmerje, object-fit)
 *
 * @package SNIPI_Ekrani
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promo_CPT {

	/**
	 * Inicializacija – registracija WordPress hookov
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init',                  array( __CLASS__, 'register_cpt' ) );
		add_filter( 'wp_insert_post_data',   array( __CLASS__, 'enforce_content_limit' ), 10, 1 );
		add_action( 'add_meta_boxes',        array( __CLASS__, 'register_image_help_box' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	/**
	 * Registracija Custom Post Type 'promo_objava'
	 *
	 * @return void
	 */
	public static function register_cpt() {
		$labels = array(
			'name'               => 'Promo objave',
			'singular_name'      => 'Promo objava',
			'menu_name'          => 'Promo objave',
			'name_admin_bar'     => 'Promo objava',
			'add_new'            => 'Nova promo objava',
			'add_new_item'       => 'Dodaj novo promo objavo',
			'edit_item'          => 'Uredi promo objavo',
			'new_item'           => 'Nova promo objava',
			'view_item'          => 'Poglej promo objavo',
			'search_items'       => 'Išči promo objave',
			'not_found'          => 'Ni najdenih promo objav',
			'not_found_in_trash' => 'Ni promo objav v smeteh',
			'all_items'          => 'Promo objave',
		);

		$args = array(
			'labels'        => $labels,
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => 'edit.php?post_type=ekran',
			'supports'      => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'   => false,
			'rewrite'       => false,
			'show_in_rest'  => false,
			'capability_type' => 'post',
		);

		register_post_type( 'promo_objava', $args );
	}

	/**
	 * Omeji vsebino promo objave na 200 znakov (backend zaščita)
	 *
	 * Vsebina se shrani kot golo besedilo brez HTML oznak —
	 * promo besedilo se prikaže na TV ekranih in ne potrebuje bogatega oblikovanja.
	 *
	 * @param array $data Podatki posta pred shranjevanjem
	 * @return array
	 */
	public static function enforce_content_limit( $data ) {
		if ( 'promo_objava' !== $data['post_type'] ) {
			return $data;
		}

		// Odstrani HTML oznake in omeji na 200 znakov
		$plain = wp_strip_all_tags( $data['post_content'] );
		if ( mb_strlen( $plain ) > 200 ) {
			$plain = mb_substr( $plain, 0, 200 );
		}
		$data['post_content'] = $plain;

		return $data;
	}

	/**
	 * Registracija meta boxa s pomočjo za featured image
	 *
	 * @return void
	 */
	public static function register_image_help_box() {
		add_meta_box(
			'promo_image_help',
			'Napotki za sliko',
			array( __CLASS__, 'render_image_help_box' ),
			'promo_objava',
			'side',
			'low'
		);
	}

	/**
	 * Vsebina meta boxa s pomočjo za featured image
	 *
	 * @return void
	 */
	public static function render_image_help_box() {
		?>
		<p class="description" style="margin-top:0;">
			Slika bo samodejno obrezana in prilagojena na enotno velikost kolone
			(<code>object-fit: cover</code>), ne glede na originalne dimenzije.
		</p>
		<p class="description">
			Priporočamo <strong>kvadratne</strong> ali <strong>ležeče</strong> slike
			(razmerje 1:1 ali 16:9). Portretne slike se bodo znatno obrezale.
		</p>
		<?php
	}

	/**
	 * Naloži JavaScript za živi števec znakov na edit screenu promo_objave
	 *
	 * @param string $hook Trenutna admin stran
	 * @return void
	 */
	public static function enqueue_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'promo_objava' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_script(
			'promo-admin-js',
			SNIPI_EKRANI_URL . 'assets/js/admin-promo.js',
			array(),
			snipi_ekrani_asset_version( 'assets/js/admin-promo.js' ),
			true
		);
	}
}

Promo_CPT::init();
