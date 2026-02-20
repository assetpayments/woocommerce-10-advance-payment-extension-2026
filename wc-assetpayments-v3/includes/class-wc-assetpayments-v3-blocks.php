<?php
if (!defined('ABSPATH')) exit;

add_action('woocommerce_blocks_loaded', 'wc_assetpayments_v3_register_blocks_support');

if ( did_action( 'woocommerce_blocks_loaded' ) ) {
    wc_assetpayments_v3_register_blocks_support();
}

function wc_assetpayments_v3_register_blocks_support() {
    if (!class_exists('\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }

    require_once __DIR__ . '/class-wc-assetpayments-v3-blocks-integration.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function($payment_method_registry) {
            $payment_method_registry->register(new WC_AssetPayments_V3_Blocks_Integration());
        }
    );
}
