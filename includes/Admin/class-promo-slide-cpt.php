<?php
/**
 * Promo Stran CPT
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
			'name'               => 'Promo strani',
			'singular_name'      => 'Promo stran',
			'menu_name'          => 'Promo strani',
			'name_admin_bar'     => 'Promo stran',
			'add_new'            => 'Nova promo stran',
			'add_new_item'       => 'Dodaj novo promo stran',
			'edit_item'          => 'Uredi promo stran',
			'new_item'           => 'Nova promo stran',
			'view_item'          => 'Poglej promo stran',
			'search_items'       => 'Išči promo strani',
			'not_found'          => 'Ni najdenih promo strani',
			'not_found_in_trash' => 'Ni promo strani v smeteh',
			'all_items'          => 'Promo strani',
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
