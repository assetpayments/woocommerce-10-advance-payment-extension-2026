<?php
add_action('rest_api_init', function () {
	register_rest_route('assetpayments/v1', '/callback/', array(
		'methods' => 'POST',
		'callback' => 'assetpayments_callback',
	));
});

function assetpayments_callback($request) {
	header('Content-Type: application/json; charset=utf-8');

	if (!class_exists('WC_Order')) {
		echo json_encode(array('status' => 'error', 'message' => 'WooCommerce is not active'));
		return;
	}

	$body = $request->get_body();
	$json = json_decode($body, true);

	if (!$json || !isset($json['MerchantInternalOrderId'])) {
		echo json_encode(array('status' => 'error', 'message' => 'Invalid request'));
		return;
	}

	$order_id = intval($json['MerchantInternalOrderId']);
	$order = wc_get_order($order_id);

	if (!$order) {
		echo json_encode(array('status' => 'error', 'message' => 'Order not found'));
		return;
	}

	$status = isset($json['Status']) ? $json['Status'] : '';

	if ($status == 'Approved') {
		$order->payment_complete();
		$order->add_order_note('Payment successfully completed via AssetPayments callback.');
		echo json_encode(array('status' => 'success'));
		return;
	} else {
		$order->update_status('failed', 'Payment failed via AssetPayments callback.');
		echo json_encode(array('status' => 'failed'));
		return;
	}
}
