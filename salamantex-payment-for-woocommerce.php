<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.salamantex.com/
 * @since             1.0.0
 * @package           Woocommerce_Salamantex
 *
 * @wordpress-plugin
 * Plugin Name:       Salamantex Payment for WooCommerce
 * Plugin URI:        https://www.salamantex.com/
 * Description:       WooCommerce integration with Salamantex all-in-one crypto payment solution.
 * Version:           1.1.2
 * Author:            Salamantex
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-salamantex
 * Domain Path:       /languages
 *
 * WC requires at least: 4.0
 * WC tested up to: 5.7
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
	return;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOCOMMERCE_SALAMANTEX_VERSION', '1.1.2' );

/**
 * Define plugin basename.
 */
define( 'WOOCOMMERCE_SALAMANTEX_BASENAME', plugin_basename( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-salamantex-activator.php
 */
function activate_woocommerce_salamantex() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-salamantex-activator.php';
	Woocommerce_Salamantex_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-salamantex-deactivator.php
 */
function deactivate_woocommerce_salamantex() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-salamantex-deactivator.php';
	Woocommerce_Salamantex_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_salamantex' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_salamantex' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-salamantex.php';


function salamantex_payment_load_my_own_textdomain( $mofile, $domain ) {
    if ( 'woocommerce-salamantex' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}
add_filter( 'load_textdomain_mofile', 'salamantex_payment_load_my_own_textdomain', 10, 2 );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_salamantex() {

	$plugin = new Woocommerce_Salamantex();
	$plugin->run();

}

run_woocommerce_salamantex();
