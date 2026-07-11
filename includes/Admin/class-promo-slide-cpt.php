<?php
/**
 * Promo Diapozitiv CPT
 *
 * Registracija Custom Post Type 'promo_slide'.
 * Celozaslonska vsebina za rotacijo na TV ekranih — oblikovana s
 * polnim Gutenberg urejevalnikom. Namenjena zahtevnejšim promocijskim
 * vsebinam (slike, besedilo, bloki), kjer prosto oblikovanje nadomesti
 * preproste kartice promo_objava.
 *
 * @package SNIPI_Ekrani
 * @since   2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promo_Slide_CPT {

	/**
	 * Inicializacija – registracija WordPress hookov
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_cpt' ) );
	}

	/**
	 * Registracija Custom Post Type 'promo_slide'
	 *
	 * @return void
	 */
	public static function register_cpt() {
		$labels = array(
			'name'               => 'Promo diapozitivi',
			'singular_name'      => 'Promo diapozitiv',
			'menu_name'          => 'Promo diapozitivi',
			'name_admin_bar'     => 'Promo diapozitiv',
			'add_new'            => 'Nov promo diapozitiv',
			'add_new_item'       => 'Dodaj nov promo diapozitiv',
			'edit_item'          => 'Uredi promo diapozitiv',
			'new_item'           => 'Nov promo diapozitiv',
			'view_item'          => 'Poglej promo diapozitiv',
			'search_items'       => 'Išči promo diapozitive',
			'not_found'          => 'Ni najdenih promo diapozitivov',
			'not_found_in_trash' => 'Ni promo diapozitivov v smeteh',
			'all_items'          => 'Promo diapozitivi',
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'edit.php?post_type=ekran',
			'supports'        => array( 'title', 'editor' ),
			'has_archive'     => false,
			'rewrite'         => false,
			'show_in_rest'    => true,
			'capability_type' => 'post',
		);

		register_post_type( 'promo_slide', $args );
	}
}

Promo_Slide_CPT::init();
