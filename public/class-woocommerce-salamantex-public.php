<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.salamantex.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Salamantex
 * @subpackage Woocommerce_Salamantex/public
 * @author     Salamantex <info@salamantex.com>
 */
class Woocommerce_Salamantex_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-salamantex-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-salamantex-public.js', array( 'jquery' ), $this->version, false );

		$salamantax_vars = [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		];
		wp_localize_script( $this->plugin_name, 'salamantax', $salamantax_vars );
	}

	/**
	 * Get supported currencies of partner.
	 *
	 * @since    1.0.0
	 */
	private function get_supported_currencies() {

		$processor = new WC_Gateway_Salamantex;

		$args     = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $processor->get_environment_details()['token'],
			),
		);
		$response = wp_remote_get( $processor->get_environment_details()['gateway_url'] . '/supported_currencies', $args );
		if ( is_array( $response ) && ! is_wp_error( $response ) && 200 === $response['response']['code'] ) {

            $body     = json_decode( $response['body'] );

            $supported_currencies = [];
            foreach ( $body as $currency ) {

                // no fiat currencies
                if ( false === $currency->isFiat ) { // phpcs:ignore

                    // filter test currencies and live currencies dependent on plugin mode
                    if ( true === $currency->forTesting && 'yes' === $processor->settings['testmode'] ) { // phpcs:ignore
                        $supported_currencies[] = $currency->symbol;
                    } elseif ( false === $currency->forTesting && 'no' === $processor->settings['testmode'] ) { // phpcs:ignore
                        $supported_currencies[] = $currency->symbol;
                    }

                }
            }

            return $supported_currencies;

		}

		return null;

	}

	/**
	 * Display images of supported currencies on checkout page.
	 *
	 * @since    1.0.0
	 */
	public function display_supported_currencies( $icon, $id ) {
		if ( 'salamantex_gateway' === $id ) {

            $supported_currencies = $this->get_supported_currencies();
            if( $supported_currencies === null ) return '<div class="salamantex-currencies"><b>'.__( 'This payment method is currently not available', 'woocommerce-salamantex' ).'</b></div>';

            $icon  = '<div class="salamantex-currencies">';
            $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/salamantex-icon.png' . '" alt="' . __( 'Salamantex', 'woocommerce-salamantex' ) . '" title="' . __( 'Salamantex', 'woocommerce-salamantex' ) . '" />';

            if ( in_array( 'BTC', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/btc.png' . '" alt="' . __( 'Bitcoin', 'woocommerce-salamantex' ) . '" title="' . __( 'Bitcoin', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'ETH', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/eth.png' . '" alt="' . __( 'Ethereum', 'woocommerce-salamantex' ) . '" title="' . __( 'Ethereum', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'DASH', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/dash.png' . '" alt="' . __( 'Dash', 'woocommerce-salamantex' ) . '" title="' . __( 'Dash', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'NIM', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/nimiq.png' . '" alt="' . __( 'Nimiq', 'woocommerce-salamantex' ) . '" title="' . __( 'Nimiq', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'LTC', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/ltc.png' . '" alt="' . __( 'Litecoin', 'woocommerce-salamantex' ) . '" title="' . __( 'Litecoin', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'XLM', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/xlm.png' . '" alt="' . __( 'Ripple', 'woocommerce-salamantex' ) . '" title="' . __( 'Ripple', 'woocommerce-salamantex' ) . '" />';
            }
            if ( in_array( 'XRP', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/xrp.png' . '" alt="' . __( 'Stellar', 'woocommerce-salamantex' ) . '" title="' . __( 'Stellar', 'woocommerce-salamantex' ) . '" />';
            }

            if ( in_array( 'BCY', $supported_currencies, true ) ) {
                $icon .= '<img src="' . plugin_dir_url( __FILE__ ) . 'images/charry.png' . '" alt="' . __( 'Charry', 'woocommerce-salamantex' ) . '" title="' . __( 'Charry', 'woocommerce-salamantex' ) . '" />';
            }

            $icon .= '</div>';

		}

		return $icon;
	}

	/**
	 * Register every 10 seconds interval for scheduled events.
	 *
	 * @since    1.0.0
	 */
	public function every_ten_seconds_cron_schedules( $schedules ) {
		if ( ! isset( $schedules['every_ten_seconds'] ) ) {
			$schedules['every_ten_seconds'] = array(
				'interval' => 10,
				'display'  => __( 'Once every 10 seconds' ),
			);
		}

		return $schedules;
	}

	/**
	 * Global update order status function
	 *
	 * @since    1.0.0
	 */
	public function salamantex_update_order_status( $order_id, $sequence = false ) {
		$processor    = new WC_Gateway_Salamantex;
		$order        = wc_get_order( $order_id );
		$order_status = $order->get_status();
		$method       = $order->get_payment_method();
		$payment_id   = get_post_meta( $order_id, 'salamantex_payment_id', true );

		if ( 'completed' === $order_status ) {
			return;
		}

		if ( 'salamantex_gateway' !== $method ) {
			return;
		}

		if ( true === $sequence ) {
			$timestamp   = (int) wp_next_scheduled( 'salamantex_run_cron_job_new_order_hook', array( $order_id ) );
			$cron_run_at = (int) get_post_meta( $order_id, 'salamantex_cron_job_started_at', true );
		}

		$args = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $processor->get_environment_details()['token'],
			),
		);

		$response = wp_remote_get( $processor->get_environment_details()['gateway_url'] . '/payment/status?paymentId=' . $payment_id, $args );

		if ( is_array( $response ) && ! is_wp_error( $response ) && 200 === $response['response']['code'] ) {

            $body     = json_decode( $response['body'] );
            $status   = $body->status;

			update_post_meta( $order_id, 'salamantex_payment_status_updated', $status->frontendStatus ); // phpcs:ignore

			$current_status = get_post_meta( $order_id, 'salamantex_payment_status', true );
			$new_status     = get_post_meta( $order_id, 'salamantex_payment_status_updated', true );

			if ( $new_status !== $current_status ) {
				update_post_meta( $order_id, 'salamantex_payment_status', $status->frontendStatus ); // phpcs:ignore
				$order->add_order_note( __( 'Salamantex payment status - double checked: ', 'woocommerce-salamantex' ) . $status->frontendStatus ); // phpcs:ignore
			}
			if ( 'pending' === $order_status && ( 'FTS_CONFIRMED' === $status->frontendStatus || 'FTS_STABLE' === $status->frontendStatus ) ) { // phpcs:ignore
				$order->update_status( 'processing' );
			}
			if ( 'yes' === $processor->settings['pretty_safe'] && 'FTS_PRETTY_SAFE' === $status->frontendStatus ) { // phpcs:ignore
				$order->update_status( 'processing' );
			}
			if ( true === $sequence ) {
				if ( 'FTS_STABLE' === $current_status || 'FTS_CANCELLED' === $current_status || 'FTS_ERROR' === $current_status || ( $timestamp - $cron_run_at ) >= 240 ) {
					wp_unschedule_event( $timestamp, 'salamantex_run_cron_job_new_order_hook', array( $order_id ) );
				}
			}
			if ( 'FTS_CANCELLED' === $current_status ) {
				$order->update_status( 'cancelled' );
			}
			if ( 'FTS_ERROR' === $current_status ) {
				$order->update_status( 'failed' );
			}
		} else {
			return;
		}
	}

	/**
	 * Run scheduled event on thank you page.
	 *
	 * @since    1.0.0
	 */
	public function salamantex_run_cron_job_new_order() {
		$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
		$order    = wc_get_order( $order_id );
		$method   = $order->get_payment_method();
		if ( 'salamantex_gateway' === $method ) {
			$time_now = time();
			update_post_meta( $order_id, 'salamantex_cron_job_started_at', $time_now );
			wp_schedule_event( $time_now, 'every_ten_seconds', 'salamantex_run_cron_job_new_order_hook', array( $order_id ) );
		} else {
			return;
		}
	}

	/**
	 * Update status sequence on Woocommerce thank you page via scheduled event.
	 *
	 * @since    1.0.0
	 */
	public function salamantax_update_status_sequence( $order_id ) {
		$this->salamantex_update_order_status( $order_id, true );
	}

	/**
	 * Add payment URL to transactional emails
	 *
	 * @since    1.0.0
	 */
	public function custom_new_order_email( $order, $sent_to_admin, $plain_text, $email ) {
		$order_id           = $order->get_id();
		$method             = $order->get_payment_method();
		$payment_status_url = get_post_meta( $order_id, 'salamantex_payment_url', true );

		if ( 'salamantex_gateway' === $method && ! empty( $payment_status_url ) ) {
			echo __( 'Unique Salamantex payment URL: ', 'woocommerce-salamantex' ) . esc_url( $payment_status_url ) . '<br><br>';
		}
	}
}
