<?php
/*
Plugin Name: AssetPayments Woocommerce Payment Gateway
Description: Plugin for paying for products through the AssetPayments service. Works in conjunction with the Woocommerce plugin
Version: 4.1.0
Requires at least: 5.7.2
Requires PHP: 7.x
Author: AssetPayments
License: GPL v2 or later
Text Domain: wc-assetpayments
*/

if (!defined('ABSPATH')) exit;

// Main plugin file constant used by Blocks integration for stable asset URLs.
if ( ! defined( 'WC_ASSETPAYMENTS_PLUGIN_FILE' ) ) {
    define( 'WC_ASSETPAYMENTS_PLUGIN_FILE', __FILE__ );
}

add_action('plugins_loaded', 'woocommerce_assetpayments_init', 11);

function woocommerce_assetpayments_init() {
    if (!class_exists('WC_Payment_Gateway')) return;

    include_once('includes/WC_Gateway_kmnd_Assetpayments.php');

    // Always load Blocks integration so WooCommerce can detect compatibility.
    // The integration itself will decide whether it should be active based on settings.
    include_once('includes/class-wc-assetpayments-blocks.php');

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_assetpayments_gateway');
    function woocommerce_add_assetpayments_gateway($methods) {
        $methods[] = 'WC_Gateway_kmnd_Assetpayments';
        return $methods;
    }
}

add_filter('woocommerce_locate_template', 'custom_woocommerce_locate_template', 10, 3);

function custom_woocommerce_locate_template($template, $template_name, $template_path) {
    global $woocommerce;

    $_template = $template;

    if (!$template_path) $template_path = $woocommerce->template_url;

    $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/';

    $template = locate_template(
        array(
            $template_path . $template_name,
            $template_name
        )
    );

    if (!$template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;

    if (!$template)
        $template = $_template;

    return $template;
}

require_once plugin_dir_path(__FILE__) . 'includes/wc-assetpayments-callback.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-assetpayments-page-redirect.php';

// Add Settings link on Plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'assetpayments_settings_link');

function assetpayments_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=assetpayments') . '">' . __('Settings', 'wc-assetpayments') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
