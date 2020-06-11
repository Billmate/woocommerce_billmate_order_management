<?php // phpcs:ignore
/**
 * Plugin Name:     Billmate Order Management for WooCommerce
 * Plugin URI:      http://krokedil.com/
 * Description:     Provides an order management for Billmate Checkout.
 * Version:         1.0.0
 * Author:          Krokedil
 * Author URI:      http://krokedil.com/
 * Developer:       Krokedil
 * Developer URI:   http://krokedil.com/
 * Text Domain:     billmate-order-management-for-woocommerce
 * Domain Path:     /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.1.0
 *
 * Copyright:       © 2016-2020 Krokedil.
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Billmate_Order_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'BILLMATE_ORDER_MANAGEMENT_VERSION', '1.0.0' );
define( 'BILLMATE_ORDER_MANAGEMENT_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'BILLMATE_ORDER_MANAGEMENT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'BILLMATE_ORDER_MANAGEMENT_ENV', 'https://api.billmate.se' );


if ( ! class_exists( 'Billmate_Order_Management_For_WooCommerce' ) ) {

	/**
	 * Main class for the plugin.
	 */
	class Billmate_Order_Management_For_WooCommerce {
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
		private function __wakeup() {
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

			// Requests.
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-activate-payment.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/order-management/post/class-bom-request-cancel-payment.php';

			// Request Helpers.
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-articles-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-payment-data-helper.php';
			include_once BILLMATE_ORDER_MANAGEMENT_PATH . '/classes/requests/helpers/class-bom-refund-data-cart-helper.php';

		}

		/**
		 * Add Billmate order management settings.
		 *
		 * @param array $settings BCO settings.
		 * @return array $settings BCO settings with added BOM settings.
		 */
		public function bom_add_settings( $settings ) {
			$bom_settings = array(
				'order_management' => array(
					'title'   => __( 'Enable Order Management', 'billmate-order-management-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Billmate order capture on WooCommerce order completion and Billmate order cancellation on WooCommerce order cancellation', 'billmate-order-management-for-woocommerce' ),
					'default' => 'yes',
				),
				'bom_debug'        => array(
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
		 * Adds plugin action links
		 *
		 * @param array $links Plugin action link before filtering.
		 *
		 * @return array Filtered links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="http://krokedil.se/">' . __( 'Support', 'billmate-order-management-for-woocommerce' ) . '</a>',
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
