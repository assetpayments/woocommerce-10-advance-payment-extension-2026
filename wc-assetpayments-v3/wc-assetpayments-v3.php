<?php
/*
Plugin Name: AssetPayments Woocommerce Payment Gateway V3
Description: Plugin for paying for products through the AssetPayments service. Works in conjunction with the Woocommerce plugin (Instance 3)
Version: 4.1.0
Requires at least: 5.7.2
Requires PHP: 7.x
Author: AssetPayments
License: GPL v2 or later
Text Domain: wc-assetpayments-v3
*/

if (!defined('ABSPATH')) exit;

if ( ! defined( 'WC_ASSETPAYMENTS_V3_PLUGIN_FILE' ) ) {
    define( 'WC_ASSETPAYMENTS_V3_PLUGIN_FILE', __FILE__ );
}

add_action('plugins_loaded', 'woocommerce_assetpayments_v3_init', 11);

function woocommerce_assetpayments_v3_init() {
    if (!class_exists('WC_Payment_Gateway')) return;

    include_once('includes/WC_Gateway_kmnd_Assetpayments_V3.php');
    include_once('includes/class-wc-assetpayments-v3-blocks.php');

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_assetpayments_v3_gateway');
    function woocommerce_add_assetpayments_v3_gateway($methods) {
        $methods[] = 'WC_Gateway_kmnd_Assetpayments_V3';
        return $methods;
    }
}

add_filter('woocommerce_locate_template', 'custom_woocommerce_locate_template_v3', 10, 3);

function custom_woocommerce_locate_template_v3($template, $template_name, $template_path) {
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

require_once plugin_dir_path(__FILE__) . 'includes/wc-assetpayments-v3-callback.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-assetpayments-v3-page-redirect.php';

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'assetpayments_v3_settings_link');

function assetpayments_v3_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=assetpayments-v3') . '">' . __('Settings', 'wc-assetpayments-v3') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
