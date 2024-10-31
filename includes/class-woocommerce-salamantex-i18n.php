<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.salamantex.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/includes
 * @author     Salamantex <info@salamantex.com>
 */
class Woocommerce_Salamantex_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-salamantex',
			false,
			dirname( dirname( WOOCOMMERCE_SALAMANTEX_BASENAME ) ) . '/languages/'
		);

	}



}
