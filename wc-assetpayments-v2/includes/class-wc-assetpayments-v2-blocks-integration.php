<?php
if (!defined('ABSPATH')) exit;

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class WC_AssetPayments_V2_Blocks_Integration extends AbstractPaymentMethodType {

    /**
     * Payment method name/id used by Blocks.
     * Must match your gateway $this->id
     */
    protected $name = 'assetpayments-v2';

    public function initialize() {
        $this->settings = get_option('woocommerce_assetpayments-v2_settings', array());
    }

    public function is_active() {
        // Only expose this method in the Block-based checkout when:
        // 1) Gateway is enabled
        // 2) Merchant selected "Block-based" in gateway settings
        $enabled = isset($this->settings['enabled']) && $this->settings['enabled'] === 'yes';
        $checkout_type = isset($this->settings['checkout_type']) ? $this->settings['checkout_type'] : 'classic';
        return $enabled && $checkout_type === 'blocks';
    }

    /**
     * Script that registers the payment method in Blocks checkout.
     */
    public function get_payment_method_script_handles() {
      wp_register_script(
        'wc-assetpayments-v2-blocks',
        plugins_url('assets/js/blocks-v2.js', WC_ASSETPAYMENTS_V2_PLUGIN_FILE),
        array('wc-blocks-registry', 'wp-element', 'wp-i18n', 'wp-html-entities'),
        '3.0.0',
        true
      );

        // Pass settings to JS (title/description, etc.)
        $data = array(
            'title'       => isset($this->settings['title']) ? $this->settings['title'] : 'AssetPayments',
            'description' => isset($this->settings['description']) ? $this->settings['description'] : '',
            'supports'    => array(
                'features' => array('products'),
            ),
        );

        wp_add_inline_script(
            'wc-assetpayments-v2-blocks',
            'window.wcAssetPaymentsV2Data = ' . wp_json_encode($data) . ';',
            'before'
        );

        return array('wc-assetpayments-v2-blocks');
    }

    /**
     * Data made available to Blocks on the frontend.
     */
    public function get_payment_method_data() {
        return array(
            'title'       => isset($this->settings['title']) ? $this->settings['title'] : 'AssetPayments',
            'description' => isset($this->settings['description']) ? $this->settings['description'] : '',
            'supports'    => array(
                'features' => array('products'),
            ),
        );
    }
}
