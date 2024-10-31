<?php

/**
 * Salamantex Payment for WooCommerce class.
 *
 * Extended by individual payment gateways to handle payments.
 *
 * @class       WC_Salamantex_Gateway
 * @extends     WC_Payment_Gateway
 * @since       1.0.0
 * @package     Woocommerce_Salamantex
 * @subpackage  Woocommerce_Salamantex/admin
 */
class WC_Gateway_Salamantex extends WC_Payment_Gateway {

	private $callback_url = 'slmtx_payment_success';

	private $allowed_currencies = array( 'EUR' );

	/**
	 * Constructor for the gateway.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 */
	public function __construct() {

		// Define plugin path
		$plugin_dir = plugin_dir_url( __FILE__ );

		// Define variables
		$this->id                 = 'salamantex_gateway';
		$this->icon               = apply_filters( 'woocommerce_salamantex_icon', $plugin_dir . '/images/salamantex-icon.png' );
		$this->has_fields         = false;
		$this->method_title       = __( 'Salamantex Crypto Payment Service', 'woocommerce-salamantex' );
		$this->method_description = __( 'Pay quickly and easily with the following crypto currencies: Bitcoin, Ethereum, Dash, Nimiq, Litecoin, Ripple or Stellar', 'woocommerce-salamantex' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title           = $this->get_option( 'title' );
		$this->description     = $this->get_option( 'description' );
		$this->instructions    = $this->get_option( 'instructions', $this->description );
		$this->testmode        = $this->get_option( 'testmode' );
		$this->test_auth_token = $this->get_option( 'test_auth_token' );
		$this->test_url        = $this->get_option( 'test_url' );
		$this->auth_token      = $this->get_option( 'auth_token' );
		$this->prod_url        = $this->get_option( 'prod_url' );
		$this->pretty_safe     = $this->get_option( 'pretty_safe' );
		$this->defaultdesc     = $this->get_option( 'defaultdesc' );

		if($this->defaultdesc === 'yes') $this->description = $this->method_description;

		// Site URL
		$this->site_url = get_site_url();

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		// Thank you page
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

		// Customer Emails
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );

		// Register callbacks
		add_action( 'woocommerce_api_' . $this->callback_url, array( $this, 'payment_success_callback' ) );
	}

	/**
	 * Initialize Gateway Settings Form Fields
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 */
	public function init_form_fields() {

		$this->form_fields = apply_filters(
			'wc_salamantex_form_fields',
			array(
				'enabled'         => array(
					'title'   => __( 'Enable/Disable', 'woocommerce-salamantex' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Salamantex', 'woocommerce-salamantex' ),
					'default' => 'yes',
				),
				'title'           => array(
					'title'       => __( 'Title', 'woocommerce-salamantex' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'woocommerce-salamantex' ),
					'default'     => __( 'Salamantex Crypto Payment Service', 'woocommerce-salamantex' ),
					'desc_tip'    => true,
				),
                'defaultdesc'     => array(
                    'title'       => __( 'Default description', 'woocommerce-salamantex' ),
                    'label'       => __( 'Use the default description', 'woocommerce-salamantex' ),
                    'type'        => 'checkbox',
                    'description' => __( 'Use the default description instead of the user defined one.', 'woocommerce-salamantex' ),
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
				'description'     => array(
					'title'       => __( 'Description', 'woocommerce-salamantex' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce-salamantex' ),
					'default'     => __( 'Pay quickly and easily with the following crypto currencies: Bitcoin, Ethereum, Dash, Nimiq, Litecoin, Ripple or Stellar', 'woocommerce-salamantex' ),
					'desc_tip'    => true,
				),
				'instructions'    => array(
					'title'       => __( 'Instructions', 'woocommerce-salamantex' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce-salamantex' ),
					'desc_tip'    => true,
				),
				'testmode'        => array(
					'title'       => __( 'Test mode', 'woocommerce-salamantex' ),
					'label'       => __( 'Enable Test Mode', 'woocommerce-salamantex' ),
					'type'        => 'checkbox',
					'description' => __( 'Place the payment gateway in test mode using test API keys.', 'woocommerce-salamantex' ),
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'test_auth_token' => array(
					'title'       => __( 'Test API key', 'woocommerce-salamantex' ),
					'type'        => 'password',
					'placeholder' => __( 'Enter Test API key', 'woocommerce-salamantex' ),
					'default'     => __( '5e79fab88e5ec1000a3e9403-16aaa9f5-a0a3-454f-9788-4ef654e61eba', 'woocommerce-salamantex' ),
				),
				'test_url'        => array(
					'title'       => __( 'Test URL', 'woocommerce-salamantex' ),
					'type'        => 'text',
					'placeholder' => __( 'Enter the API test URL', 'woocommerce-salamantex' ),
					'default'     => __( 'https://webapi.test.salamantex.com/webshop/v1/', 'woocommerce-salamantex' ),
				),
				'auth_token'      => array(
					'title'       => __( 'Live API key', 'woocommerce-salamantex' ),
					'type'        => 'password',
					'placeholder' => __( 'Enter Live API key', 'woocommerce-salamantex' ),
				),
				'prod_url'        => array(
					'title'       => __( 'Live URL', 'woocommerce-salamantex' ),
					'type'        => 'text',
					'placeholder' => __( 'Enter the API live URL', 'woocommerce-salamantex' ),
					'default'     => __( 'https://webapi.salamantex.com/webshop/v1/', 'woocommerce-salamantex' ),
				),
				'pretty_safe'     => array(
					'title'       => __( 'Make order processing', 'woocommerce-salamantex' ),
					'label'       => __( 'Enable Processing order status if Salamantex status is FTS_PRETTY_SAFE', 'woocommerce-salamantex' ),
					'type'        => 'checkbox',
					'description' => __( 'The default order status for FTS_PRETTY_SAFE is Pending Payment.', 'woocommerce-salamantex' ),
					'default'     => 'yes',
					'desc_tip'    => true,
				),
			)
		);
	}

	/**
	 * Output for the order received page.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 */
	public function thankyou_page() {
		if ( $this->instructions ) {
			echo wpautop( wptexturize( $this->instructions ) );
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param WC_Order $order
	 * @param bool     $sent_to_admin
	 * @param bool     $plain_text
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
			echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
		}
	}

	/**
	 * Verify if user enabled test mode or not
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_environment_details() {
		$environment = [];
		if ( 'yes' === $this->testmode && isset( $this->test_auth_token ) && isset( $this->test_url ) ) {
			$environment['gateway_url'] = $this->test_url;
			$environment['token']       = $this->test_auth_token;
		} elseif ( 'yes' !== $this->testmode && isset( $this->auth_token ) && isset( $this->prod_url ) ) {
			$environment['gateway_url'] = $this->prod_url;
			$environment['token']       = $this->auth_token;
		}

		return $environment;
	}

	/**
	 * Generate hashed string that will be used as verification token
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	private function generate_random_string( $length = 10 ) {
		$date              = new DateTime();
		$timestamp         = $date->getTimestamp();
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$random_string     = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$random_string .= $characters[ rand( 0, $characters_length - 1 ) ];
			$random_string .= $timestamp;
			$random_string  = hash( 'md5', $random_string, false );
		}

		return $random_string;
	}

	/**
	 * Check if shop currency is supported.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	private function is_valid_currency() {
		return ( in_array( get_woocommerce_currency(), $this->allowed_currencies, true ) );
	}

	/**
	 * Process the payment and return the result
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @param    int $order_id
	 *
	 * @return   array
	 */
	public function process_payment( $order_id ) {
		$order          = wc_get_order( $order_id );
		$order_number   = $order->get_id();
		$order_key      = $order->get_order_key();
		$order_amount   = $order->get_total();
		$customer_name  = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$customer_email = $order->get_billing_email();
		$token          = $this->generate_random_string();

		$args = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => $this->get_environment_details()['token'],
			),
			'body'    => json_encode(
				array(
					'referenceAmountValue' => $order_amount,
					'orderId'              => strval( $order_number ),
					'callbackUrl'          => $this->site_url . '/wc-api/' . $this->callback_url,
					'cancelUrl'            => $order->get_checkout_payment_url(),
					'successUrl'           => $order->get_checkout_order_received_url(),
					'customerName'         => $customer_name,
					'customerEmail'        => $customer_email,
					'webshopToken'         => $token,
				)
			),
		);

		$response = wp_remote_post( $this->get_environment_details()['gateway_url'] . '/payment', $args );

		if ( ! is_wp_error( $response ) ) {
			$body               = json_decode( $response['body'] );
			$payment_info       = $body->payment;
			$payment_id         = $body->paymentId; // phpcs:ignore
			$payment_url        = $payment_info->paymentUrl; // phpcs:ignore
			$payment_status_url = $payment_info->paymentUrl; // phpcs:ignore
			if ( 200 === $response['response']['code'] && $this->is_valid_currency() ) {
				$order->add_order_note( __( 'Salamantex payment ID: ', 'woocommerce-salamantex' ) . $payment_id );
				$order->add_order_note( '<a href="' . $payment_status_url . '">' . __( 'Check Salamantex transaction status: ', 'woocommerce-salamantex' ) . $payment_status_url . '</a>');
				update_post_meta( $order_number, 'salamantex_payment_id', $payment_id );
				update_post_meta( $order_number, 'salamantex_payment_url', $payment_status_url );
				update_post_meta( $order_number, 'salamantex_payment_token', $token );

				return array(
					'result'   => 'success',
					'redirect' => $payment_url,
				);
			} elseif ( ! $this->is_valid_currency() ) {
				wc_add_notice( 'This currency is not supported by Salamantex.', 'error' );
			} else {
				wc_add_notice( 'Please check if your Salamantex API key is valid.', 'error' );

				return;
			}
		} else {
			wc_add_notice( 'Connection error.', 'error' );

			return;
		}
	}

	/**
	 * Update order status utilizing callbackUrl
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function payment_success_callback() {
		$data          = file_get_contents( 'php://input' );
		$notification  = json_decode( $data );
		$order         = wc_get_order( $notification->orderId ); // phpcs:ignore
		$payment_token = get_post_meta( $notification->orderId, 'salamantex_payment_token', true ); // phpcs:ignore
		if ( ! $notification && $notification->token !== $payment_token ) {
			return;
		}
		$order->add_order_note( __( 'Salamantex payment status: ', 'woocommerce-salamantex' ) . $notification->frontendStatus ); // phpcs:ignore
		if ( 'FTS_CONFIRMED' === $notification->frontendStatus || 'FTS_STABLE' === $notification->frontendStatus ) {  // phpcs:ignore
			$order->update_status( 'processing' );
		} elseif ( 'FTS_PRETTY_SAFE' === $notification->frontendStatus ) { // phpcs:ignore
			if ( 'yes' === $this->pretty_safe ) {
				$order->update_status( 'processing' );
			}
		} elseif ( 'FTS_CANCELLED' === $notification->frontendStatus ) { // phpcs:ignore
			$order->update_status( 'cancelled' );
		} elseif ( 'FTS_ERROR' === $notification->frontendStatus ) { // phpcs:ignore
			$order->update_status( 'failed' );
		} else {
			$order->update_status( 'pending' );
		}
	}

} // end WC_Gateway_Salamantex class
