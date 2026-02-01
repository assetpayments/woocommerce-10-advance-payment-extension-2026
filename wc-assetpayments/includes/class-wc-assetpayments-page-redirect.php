<?php
if (!defined('ABSPATH')) exit;

add_action('init', 'assetpayments_page_redirect');

function assetpayments_page_redirect() {
	if (isset($_GET['assetpayments_redirect']) && $_GET['assetpayments_redirect'] == '1') {

		if (!class_exists('WC_Order')) {
			wp_die('WooCommerce is not active');
		}

		$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
		$order_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

		if (!$order_id || !$order_key) {
			wp_die('Invalid order');
		}

		$order = wc_get_order($order_id);

		if (!$order || $order->get_order_key() !== $order_key) {
			wp_die('Invalid order key');
		}

		$return_url = $order->get_checkout_order_received_url();
		wp_safe_redirect($return_url);
		exit;
	}
}
