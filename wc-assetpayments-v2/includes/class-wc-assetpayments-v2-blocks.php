<?php
if (!defined('ABSPATH')) exit;

/**
 * WooCommerce Blocks integration for AssetPayments gateway.
 * Makes the gateway available in Block-based Checkout.
 */

add_action('woocommerce_blocks_loaded', 'wc_assetpayments_v2_register_blocks_support');

// If WooCommerce Blocks was loaded before this file was included (can happen depending on
// plugin load order), register immediately so the admin Checkout block editor can detect
// compatibility.
if ( did_action( 'woocommerce_blocks_loaded' ) ) {
    wc_assetpayments_v2_register_blocks_support();
}

function wc_assetpayments_v2_register_blocks_support() {
    if (!class_exists('\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }

    require_once __DIR__ . '/class-wc-assetpayments-v2-blocks-integration.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function($payment_method_registry) {
            $payment_method_registry->register(new WC_AssetPayments_V2_Blocks_Integration());
        }
    );
}
