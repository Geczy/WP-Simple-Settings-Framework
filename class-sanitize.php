<?php

/* Text */

add_filter( 'geczy_sanitize_text', 'sanitize_text_field' );
add_filter( 'geczy_sanitize_number', 'geczy_sanitize_number_field' );

if ( !function_exists( 'geczy_sanitize_number_field' ) ) {
	function geczy_sanitize_number_field( $input ) {

		if ( is_numeric( $input ) ) {
			return (float) $input;
		}

	}
}
/* Textarea */

if ( !function_exists( 'geczy_sanitize_textarea' ) ) {
	function geczy_sanitize_textarea( $input ) {
		global $allowedposttags;
		$output = wp_kses( $input, $allowedposttags );
		return $output;
	}
}
add_filter( 'geczy_sanitize_textarea', 'geczy_sanitize_textarea' );

/* Select */

add_filter( 'geczy_sanitize_select', 'geczy_sanitize_enum', 10, 2 );
add_filter( 'geczy_sanitize_single_select_page', 'geczy_sanitize_select_pages', 10, 2 );

if ( !function_exists( 'geczy_sanitize_select_pages' ) ) {
	function geczy_sanitize_select_pages( $input, $option ) {

		if ( get_page( $input ) ) {
			return (int) $input;
		}

	}
}
/* Radio */

add_filter( 'geczy_sanitize_radio', 'geczy_sanitize_enum', 10, 2 );

/* Checkbox */

if ( !function_exists( 'geczy_sanitize_checkbox' ) ) {
	function geczy_sanitize_checkbox( $input ) {
		if ( $input ) {
			$output = '1';
		} else {
			$output = '0';
		}
		return $output;
	}
}
add_filter( 'geczy_sanitize_checkbox', 'geczy_sanitize_checkbox' );

/* Check that the key value sent is valid */

if ( !function_exists( 'geczy_sanitize_enum' ) ) {
	function geczy_sanitize_enum( $input, $option ) {
		$output = '';
		if ( array_key_exists( $input, $option['options'] ) ) {
			$output = $input;
		}
		return $output;
	}
}
