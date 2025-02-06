<?php // phpcs:ignore
/**
 * Plugin Name:     Qvickly Order Management for WooCommerce
 * Plugin URI:      https://github.com/Billmate/billmate-order-mangement-for-woocommerce
 * Description:     Provides order management for Qvickly Checkout.
 * Version:         __STABLE_TAG__
 * Author:          Billmate, Krokedil
 * Author URI:      https://billmate.se/
 * Developer:       Billmate, Krokedil
 * Developer URI:   http://krokedil.com/
 * Text Domain:     billmate-order-management-for-woocommerce
 * Domain Path:     /languages
 *
 * WC requires at least: 5.0.0
 * WC tested up to: 7.6.1
 *
 * Copyright:       © 2020-2024 Billmate in collaboration with Krokedil.
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Billmate_Order_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'BILLMATE_ORDER_MANAGEMENT_VERSION', '1.3.2' );
define( 'BILLMATE_ORDER_MANAGEMENT_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'BILLMATE_ORDER_MANAGEMENT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BILLMATE_ORDER_MANAGEMENT_ENV', 'https://api.billmate.se' );


if ( ! class_exists( 'Billmate_Order_Management_For_WooCommerce' ) ) {

	/**
	 * Main class for the plugin.
	 */
	class Billmate_Order_Management_For_WooCommerce {
		/**
		 * API instance.
		 *
		 * @var BOM_API
		 */
		public $api;

		/**
		 * Logger instance.
		 *
		 * @var BOM_Logger
		 */
		public $logger;

		/**
		 * Order management instance.
		 *
		 * @var BOM_Order_Management
		 */
		public $order_management;

		/**
		 * The reference the *Singleton* instance of this class.
		 *
		 * @var $instance
		 */
		protected static $instance;

		/**
		 * Class constructor.
		 */
		public function __construct() {
			// Initiate the plugin.
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			add_filter( 'wc_bco_payments_supports', array( $this, 'add_gateway_support' ) );
		}

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @return self::$instance The *Singleton* instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Private clone method to prevent cloning of the instance of the
		 * *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Nope' ), '1.0' );
		}
		/**
		 * Private unserialize method to prevent unserializing of the *Singleton*
		 * instance.
		 *
		 * @return void
		 */
		public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Nope' ), '1.0' );
		}

		/**
		 * Initiates the plugin.
		 *
		 * @return void
		 */
		public function init() {
			load_plugin_textdomain( 'billmate-order-management-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_filter( 'bco_settings', array( $this, 'bom_add_settings' ) );

			$this->include_files();

			// Set class variables.
			$this->api              = new BOM_API();
			$this->logger           = new BOM_Logger();
			$this->order_management = new BOM_Order_Management();

			do_action( 'bom_initiated' );
		}

		/**
		 * Includes the files for the plugin
		 *
		 * @return void
		 */
		public function include_files() {
			// Classes.
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/class-bom-api.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/class-bom-logger.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/class-bom-order-management.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/class-bom-request.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/class-bom-admin-notices.php';

			// Requests.
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-activate-payment.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-cancel-payment.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-credit-payment.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-update-payment.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/get/class-bom-request-get-payment.php';

			// Request Helpers.
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-articles-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-payment-data-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-cart-helper.php';
		}

		/**
		 * Add Qvickly order management settings.
		 *
		 * @param array $settings BCO settings.
		 * @return array $settings BCO settings with added BOM settings.
		 */
		public function bom_add_settings( $settings ) {
			$bom_settings = array(
				'order_management_title' => array(
					'title' => __( 'Order management', 'billmate-order-management-for-woocommerce' ),
					'type'  => 'title',
				),
				'auto_capture'           => array(
					'title'   => __( 'Enable Auto Capture', 'billmate-order-management-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Activate Qvickly order automatically when WooCommerce order status changes to Completed.', 'billmate-order-management-for-woocommerce' ),
					'default' => 'yes',
				),
				'auto_cancel'            => array(
					'title'   => __( 'Enable Auto Cancel', 'billmate-order-management-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Cancel Qvickly order automatically when WooCommerce order status changes to Cancelled.', 'billmate-order-management-for-woocommerce' ),
					'default' => 'yes',
				),
				'auto_update'            => array(
					'title'   => __( 'Enable Auto Update', 'billmate-order-management-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Update Qvickly order automatically when WooCommerce order is updated.', 'billmate-order-management-for-woocommerce' ),
					'default' => 'yes',
				),
				'bom_debug'              => array(
					'title'   => __( 'Order Management Debug Log', 'billmate-order-management-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable logging', 'billmate-order-management-for-woocommerce' ),
					'default' => 'no',
				),
			);
			$settings     = array_merge( $settings, $bom_settings );
			return $settings;
		}

		/**
		 * Add refunds support to Qvickly Payments gateway.
		 *
		 * @param array $features Supported features.
		 *
		 * @return array $features Supported features.
		 */
		public function add_gateway_support( $features ) {
			$features[] = 'refunds';
			return $features;
		}

		/**
		 * Adds plugin action links
		 *
		 * @param array $links Plugin action link before filtering.
		 *
		 * @return array Filtered links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="https://qvickly.io/kundsupport/">' . __( 'Support', 'billmate-order-management-for-woocommerce' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}
	}
	Billmate_Order_Management_For_WooCommerce::get_instance();

	/**
	 * Main instance Billmate_Order_Management_For_WooCommerce.
	 *
	 * Returns the main instance of Billmate_Order_Management_For_WooCommerce.
	 *
	 * @return Billmate_Order_Management_For_WooCommerce
	 */
	function BOM_WC() { // phpcs:ignore
		return Billmate_Order_Management_For_WooCommerce::get_instance();
	}
}
