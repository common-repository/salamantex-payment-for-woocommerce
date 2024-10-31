<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.salamantex.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/admin
 * @author     Salamantex <info@salamantex.com>
 */
class Woocommerce_Salamantex_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Salamantex_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Salamantex_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-salamantex-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Salamantex_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Salamantex_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-salamantex-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add the gateway to WC Available Gateways
	 *
	 * @since 1.0.0
	 * @param array $gateways all available WC gateways
	 * @return array $gateways all WC gateways + Salamantex gateway
	 */
	public function wc_salamantex_add_to_gateways( $gateways ) {
		$gateways[] = 'WC_Gateway_Salamantex';

		return $gateways;
	}

	/**
	 * Initiate custom payment gateway class
	 *
	 * @since 1.0.0
	 */
	public function wc_salamantex_gateway_init() {
		require_once( 'class-wc-gateway-salamantex.php' );
	}

	/**
	 * Add Settings link on plugin listing page.
	 *
	 * @since    1.0.0
	 */
	public function salamantex_add_action_links( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=salamantex_gateway' ) . '">' . __( 'Settings', 'woocommerce-salamantex' ) . '</a>',
		);

		return array_merge( $links, $mylinks );
	}
}
