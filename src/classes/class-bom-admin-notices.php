<?php
/**
 * Admin notice class file.
 *
 * @package Billmate_Order_Management/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin notices class.
 */
class BOM_Admin_Notices {

	/**
	 * The reference the *Singleton* instance of this class.
	 *
	 * @var $instance
	 */
	protected static $instance;

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
	 * Whether the plugin is enabled.
	 *
	 * @var string
	 */
	private $enabled;

	/**
	 * Whether auto capture is enabled.
	 *
	 * @var string
	 */
	private $auto_capture;

	/**
	 * Whether auto cancel is enabled.
	 *
	 * @var string
	 */
	private $auto_cancel;

	/**
	 * Old Billmate auto capture setting.
	 *
	 * @var string
	 */
	private $old_billmate_auto_capture;

	/**
	 * Old Billmate auto cancel setting.
	 *
	 * @var string
	 */
	private $old_billmate_auto_cancel;

	/**
	 * BOM_Admin_Notices constructor.
	 */
	public function __construct() {
		$settings           = get_option( 'woocommerce_bco_settings', array() );
		$this->enabled      = isset( $settings['enabled'] ) ? $settings['enabled'] : 'no';
		$this->auto_capture = isset( $settings['auto_capture'] ) ? $settings['auto_capture'] : 'no';
		$this->auto_cancel  = isset( $settings['auto_cancel'] ) ? $settings['auto_cancel'] : 'no';

		// Old Qvickly plugin settings.
		$this->old_billmate_auto_capture = null !== get_option( 'billmate_common_activateonstatus' ) ? get_option( 'billmate_common_activateonstatus' ) : 'no';
		$this->old_billmate_auto_cancel  = null !== get_option( 'billmate_common_cancelonstatus' ) ? get_option( 'billmate_common_cancelonstatus' ) : 'no';

		add_action( 'admin_init', array( $this, 'check_settings' ) );
	}

	/**
	 * Checks the settings.
	 */
	public function check_settings() {
		add_action( 'admin_notices', array( $this, 'check_old_billmate_plugin' ) );
	}

	/**
	 * Check if Optimizing plugins exist.
	 */
	public function check_old_billmate_plugin() {
		if ( 'yes' !== $this->enabled ) {
			return;
		}

		if ( 'no' === $this->auto_capture && 'no' === $this->auto_cancel ) {
			return;
		}

		if ( 'active' !== $this->old_billmate_auto_capture && 'active' !== $this->old_billmate_auto_cancel ) {
			return;
		}

		if ( ! get_user_meta( get_current_user_id(), 'dismissed_kco_check_optimize_notice', true ) ) {
			if ( class_exists( 'WC_Gateway_Billmate_Checkout' ) ) {
				?>
				<div class="bco-message notice woocommerce-message notice-error">
					<?php echo wp_kses_post( wpautop( '<p>' . __( 'It looks as you have both <i>Qvickly Payment Gateway for WooCommerce</i> and <i>Qvickly Order Management</i> plugin activated. Make sure to deactivate order management in the old Billmate plugin since this can be handled via the Qvickly Order Management plugin, even for orders created in the old Billmate plugin.', 'billmate-order-management-for-woocommerce' ) . '</p>' ) ); ?>
				</div>
				<?php
			}
		}
	}
}

BOM_Admin_Notices::get_instance();
