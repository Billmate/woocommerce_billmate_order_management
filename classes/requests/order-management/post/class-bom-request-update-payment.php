<?php
/**
 * Update payment request class
 *
 * @package Billmate_Order_Management/Classes/Post/Requests
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Update payment request class
 */
class BOM_Request_Update_Payment extends BOM_Request {

	/**
	 * The function name to be used for update payment request.
	 *
	 * @var string $function_name
	 */
	private $function_name = 'updatePayment';


	/**
	 * Makes the request.
	 *
	 * @param string $order_id Billmate transaction id.
	 *
	 * @return array
	 */
	public function request( $order_id ) {
		$request_url  = $this->base_url;
		$request_args = apply_filters( 'bom_activate_payment_args', $this->get_request_args( $order_id ) );

		$response = wp_remote_request( $request_url, $request_args );
		$code     = wp_remote_retrieve_response_code( $response );

		// Log the request.
		$log = BOM_Logger::format_log( $order_id, 'POST', 'BOM update payment', $request_args, json_decode( wp_remote_retrieve_body( $response ), true ), $code );
		BOM_Logger::log( $log );

		$formated_response = $this->process_response( $response, $request_args, $request_url );

		return $formated_response;
	}

	/**
	 * Gets the request body.
	 *
	 * @param string $order_id A unique order id generated by WC.
	 *
	 * @return array
	 */
	public function get_body( $order_id ) {
		$data = $this->get_request_data( $order_id );

		return array(
			'credentials' => array(
				'id'      => $this->id,
				'hash'    => hash_hmac( 'sha512', wp_json_encode( $data ), $this->secret ),
				'test'    => $this->test,
				'version' => $this->version,
				'client'  => $this->client,
			),
			'data'        => $data,
			'function'    => $this->function_name,
		);
	}

	/**
	 * Gets the request args for the API call.
	 *
	 * @param string $order_id Billmate transaction id.
	 *
	 * @return array
	 */
	public function get_request_args( $order_id ) {
		return array(
			'headers' => $this->get_headers(),
			'method'  => 'POST',
			'body'    => wp_json_encode( $this->get_body( $order_id ) ),
			'timeout' => apply_filters( 'bco_set_timeout', 10 ),
		);
	}

	/**
	 * Get needed data for the request.
	 *
	 * @param string $order_id Generated by WC.
	 *
	 * @return array
	 */
	public function get_request_data( $order_id ) {
		$order = wc_get_order( $order_id );
		$order->calculate_taxes();
		$order->calculate_shipping();
		$order->calculate_totals();
		$payment_data           = BCO_Order_Payment_Data_Helper::get_payment_data( $order );
		$payment_data['number'] = get_post_meta( $order_id, '_billmate_transaction_id', true );

		return array(
			'PaymentData' => $payment_data,
			'Customer'    =>
				array(
					'Billing'  => BCO_Order_Customer_Helper::get_customer_billing( $order ),
					'Shipping' => BCO_Order_Customer_Helper::get_customer_shipping( $order ),
				),
			'Articles'    => BCO_Order_Articles_Helper::get_articles( $order ),
			'Cart'        =>
				array(
					'Handling' => BCO_Order_Cart_Helper::get_order_cart_handling( $order ),
					'Shipping' => BCO_Order_Cart_Helper::get_order_cart_shipping( $order ),
					'Total'    => BCO_Order_Cart_Helper::get_order_cart_total( $order ),
				),
		);
	}
}
