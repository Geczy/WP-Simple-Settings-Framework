<?php
/**
 * Sanitize filters
 */

if ( ! class_exists('SF_Sanitize' ) ) {

	class SF_Sanitize {

		function __construct() {
			add_filter( 'geczy_sanitize_text', 'sanitize_text_field' );
			add_filter( 'geczy_sanitize_number', array( $this, 'geczy_sanitize_number_field' ) );
			add_filter( 'geczy_sanitize_textarea', array( $this, 'geczy_sanitize_textarea' ) );
			add_filter( 'geczy_sanitize_checkbox', array( $this, 'geczy_sanitize_checkbox' ) );
			add_filter( 'geczy_sanitize_radio', array( $this, 'geczy_sanitize_enum' ), 10, 2 );
			add_filter( 'geczy_sanitize_select', array( $this, 'geczy_sanitize_enum' ), 10, 2 );
			add_filter( 'geczy_sanitize_single_select_page', array( $this, 'geczy_sanitize_select_pages' ), 10, 2 );
		}

		public function geczy_sanitize_number_field( $input ) {
			$output = is_numeric( $input ) ? (float) $input : false;
			return $input;
		}

		public function geczy_sanitize_textarea( $input ) {
			global $allowedposttags;
			$output = wp_kses( $input, $allowedposttags );
			return $output;
		}

		public function geczy_sanitize_checkbox( $input ) {
			$output = $input ? 1 : 0;
			return $output;
		}

		public function geczy_sanitize_enum( $input, $option ) {
			$output = array_key_exists( $input, $option['options'] ) ? $input : false;
			return $output;
		}

		public function geczy_sanitize_select_pages( $input, $option ) {
			$output = get_page( $input ) ? (int) $input : 0;
			return $output;
		}

	}

}