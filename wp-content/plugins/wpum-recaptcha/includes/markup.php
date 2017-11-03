<?php
/**
 * Adds markup to the forms
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds markup to the other forms.
 *
 * @since 1.0.0
 * @return void
 */
function wpumre_add_markup() {

	if( function_exists( 'wpum_get_option' ) && wpum_get_option( 'recaptcha_site_key' ) ) {
		echo '<div class="g-recaptcha" data-sitekey="'. wpum_get_option( 'recaptcha_site_key' ) .'"></div>';
	}

}

/**
 * Adds markup to the login form.
 *
 * @since 1.1.0
 * @return void
 */
function wpumre_login_markup( $content, $args ) {

	$content .= '<p class="wpum-recaptcha"><div class="g-recaptcha" data-sitekey="'. wpum_get_option( 'recaptcha_site_key' ) .'"></div></p>';

	return $content;

}

/**
 * Adds an error message to the login form if captcha validation fails
 *
 * @since 1.0.0
 * @return void
 */
function wpum_recaptcha_error_message() {

	if( isset( $_GET['captcha'] ) && $_GET['captcha'] == 'failed_captcha' ) {
		$args = array(
				'id'   => 'wpum-login-captcha-failed',
				'type' => 'error',
				'text' => __( 'Captcha validation failed.', 'wpumre' )
		);
		$warning = wpum_message( $args, true );
	}

}
