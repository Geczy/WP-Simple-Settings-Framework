<?php
/**
 * Sanitize filters
 */
namespace Geczy\WPSettingsFramework;

class SF_Sanitize {

	function __construct() {
		add_filter( 'geczy_sanitize_text', array( &$this, 'sanitize_text_field' ) );
		add_filter( 'geczy_sanitize_number', array( &$this, 'geczy_sanitize_number_field' ) );
		add_filter( 'geczy_sanitize_textarea', array( &$this, 'geczy_sanitize_textarea' ) );
		add_filter( 'geczy_sanitize_select', array( &$this, 'geczy_sanitize_enum', 10, 2 ) );
		add_filter( 'geczy_sanitize_single_select_page', array( &$this, 'geczy_sanitize_select_pages', 10, 2 ) );
		add_filter( 'geczy_sanitize_radio', array( &$this, 'geczy_sanitize_enum', 10, 2 ) );
		add_filter( 'geczy_sanitize_checkbox', array( &$this, 'geczy_sanitize_checkbox' ) );
	}

	function geczy_sanitize_number_field( $input ) {
		if ( is_numeric( $input ) ) {
			return (float) $input;
		}
	}

	function geczy_sanitize_textarea( $input ) {
		global $allowedposttags;
		$output = wp_kses( $input, $allowedposttags );
		return $output;
	}

	function geczy_sanitize_select_pages( $input, $option ) {
		if ( get_page( $input ) ) {
			return (int) $input;
		}
	}

	function geczy_sanitize_checkbox( $input ) {
		$output = $input ? true : false;
		return $output;
	}

	function geczy_sanitize_enum( $input, $option ) {
		$output = '';
		if ( array_key_exists( $input, $option['options'] ) ) {
			$output = $input;
		}
		return $output;
	}

}
