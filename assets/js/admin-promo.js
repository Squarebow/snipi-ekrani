/**
 * Živi števec znakov za Promo objavo (200-znakovni limit)
 *
 * Deluje tako v vizualnem načinu (TinyMCE) kot v besedilnem načinu (textarea #content).
 */
( function () {
	'use strict';

	var MAX     = 200;
	var counter = null;

	/**
	 * Vstavi element števca pod editor
	 */
	function insertCounter() {
		counter = document.createElement( 'p' );
		counter.id        = 'promo-char-counter';
		counter.className = 'description';
		counter.style.marginTop = '6px';

		// Vstavi za #wp-content-wrap ali neposredno za #content
		var wrap = document.getElementById( 'wp-content-wrap' );
		var ref  = wrap || document.getElementById( 'content' );
		if ( ref && ref.parentNode ) {
			ref.parentNode.insertBefore( counter, ref.nextSibling );
		}
	}

	/**
	 * Posodobi prikaz števca
	 *
	 * @param {string} text Golo besedilo (brez HTML)
	 */
	function updateCounter( text ) {
		if ( ! counter ) {
			return;
		}
		var len = text.replace( /<[^>]*>/g, '' ).replace( /&[a-z]+;/gi, ' ' ).length;
		counter.textContent  = len + ' / ' + MAX + ' znakov';
		counter.style.color  = len > MAX ? '#d63638' : '';
		counter.style.fontWeight = len > MAX ? 'bold' : '';
	}

	/**
	 * Pridobi besedilo iz aktivnega TinyMCE urejevalnika
	 *
	 * @return {string}
	 */
	function getTinymceText() {
		if ( typeof window.tinymce !== 'undefined' && tinymce.activeEditor && ! tinymce.activeEditor.isHidden() ) {
			return tinymce.activeEditor.getContent( { format: 'text' } );
		}
		return '';
	}

	/**
	 * Pridobi besedilo iz textarea #content (besedilni način)
	 *
	 * @return {string}
	 */
	function getTextareaText() {
		var ta = document.getElementById( 'content' );
		return ta ? ta.value : '';
	}

	/**
	 * Sproži posodobitev števca glede na aktivni način
	 */
	function refresh() {
		var ta = document.getElementById( 'content' );
		// Besedilni način: textarea je vidna
		if ( ta && ta.offsetParent !== null ) {
			updateCounter( getTextareaText() );
		} else {
			updateCounter( getTinymceText() );
		}
	}

	/**
	 * Inicializacija – čaka na DOMContentLoaded in nato na TinyMCE
	 */
	function init() {
		insertCounter();

		// Poslušaj spremembe v textarea (besedilni način)
		var ta = document.getElementById( 'content' );
		if ( ta ) {
			ta.addEventListener( 'input', refresh );
			ta.addEventListener( 'keyup', refresh );
		}

		// Poslušaj spremembe v TinyMCE (vizualni način)
		if ( typeof window.tinymce !== 'undefined' ) {
			tinymce.on( 'AddEditor', function ( e ) {
				e.editor.on( 'input keyup SetContent', refresh );
			} );
		}

		// Ob menjavi zavihka (Visual ↔ Text) posodobi števec
		var visualTab = document.getElementById( 'content-tmce' );
		var textTab   = document.getElementById( 'content-html' );
		if ( visualTab ) {
			visualTab.addEventListener( 'click', function () {
				setTimeout( refresh, 100 );
			} );
		}
		if ( textTab ) {
			textTab.addEventListener( 'click', function () {
				setTimeout( refresh, 100 );
			} );
		}

		// Začetno stanje
		refresh();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
}() );
