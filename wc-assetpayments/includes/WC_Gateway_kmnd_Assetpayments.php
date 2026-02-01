<?php

class WC_Gateway_kmnd_Assetpayments extends WC_Payment_Gateway {

    private $_checkout_url = 'https://assetpayments.us/checkout/pay';
    protected $_supportedCurrencies = array('EUR','UAH','USD','RUB','RUR');

    public function __construct() {

            global $woocommerce;
            $this->id = 'assetpayments';
            $this->has_fields = false;
            $this->method_title = 'AssetPayments (https://assetpayments.com)';
            $this->method_description = __('Payment system AssetPayments', 'wc-assetpayments');
            $this->init_form_fields();
            $this->init_settings();
            $this->public_key = $this->get_option('public_key');
            $this->private_key = $this->get_option('private_key');
            $this->template_id = $this->get_option('template_id');
            $this->processing_id = $this->get_option('processing_id');
            $this->skip_checkout = $this->get_option('skip_checkout');
            $this->alternative_callback = $this->get_option('alternative_callback');
            $this->callback_url = $this->get_option('callback_url');
            $this->successful_payment_status = $this->get_option('successful_payment_status', 'wc-processing');
            $this->declined_payment_status   = $this->get_option('declined_payment_status', 'wc-failed');
            $this->refunded_payment_status   = $this->get_option('refunded_payment_status', 'wc-refunded');

            if ($this->get_option('lang') == 'uk/en' && !is_admin()) {
                $this->lang = call_user_func($this->get_option('lang_function'));
                if ($this->lang == 'uk') {
                    $key = 0;
                } else {
                    $key = 1;
                }
            } else {
                $this->lang = $this->get_option('lang');
                $key = 1;
            }

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            if ($key == 0) {
                $this->supports = array(
                    'products'
                );
            } else {
                $this->supports = array(
                    'products'
                );
            }

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            add_action('woocommerce_api_wc_gateway_assetpayments', array($this, 'check_ipn_response'));
            add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
            add_action('admin_notices', array($this, 'check_server_connect'));

        }

    function check_server_connect() {
        if( !is_admin() ) {
            return;
        }
        if(!current_user_can('administrator')) {
            return;
        }

        if ( isset($_GET['page'], $_GET['tab'], $_GET['section']) &&
            $_GET['page'] == 'wc-settings' &&
            $_GET['tab'] == 'checkout' &&
            $_GET['section'] == 'assetpayments' ) {

            $connect_url = "https://assetpayments.us/api/PaymentApi/ConnectionStatus";
            $data = array(
                "PrivateKey" => esc_attr($this->private_key),
                "ProcessingId" => intval($this->processing_id),
                "TemplateId" => intval($this->template_id),
            );

            $args = array(
                'body'        => wp_json_encode($data),
                'headers'     => array(
                    'Content-Type' => 'application/json',
                ),
                'timeout'     => 20,
                'data_format' => 'body',
            );

            $response = wp_remote_post($connect_url, $args);

            if (is_wp_error($response)) {
                $this->update_option('connection_status', 'Connection error. Check keys.');
                $this->connection_status = 'Connection error. Check keys.';
            } else {
                $body = wp_remote_retrieve_body($response);
                $json = json_decode($body, true);

                if (isset($json['IsConnected']) && $json['IsConnected'] == true) {
                    $this->update_option('connection_status', 'Connected');
                    $this->connection_status = 'Connected';
                } else {
                    $this->update_option('connection_status', 'Not connected. Check keys.');
                    $this->connection_status = 'Not connected. Check keys.';
                }
            }

            echo '<div class="notice notice-info is-dismissible"><p><strong>AssetPayments:</strong> ' . esc_html($this->connection_status) . '</p></div>';
        }
    }

    public function init_form_fields() {

        $this->form_fields = array(
                'enabled'     => array(
                    'title'   => __('Turn on/Switch off', 'wc-assetpayments'),
                    'type'    => 'checkbox',
                    'label'   => __('Turn on', 'wc-assetpayments'),
                    'default' => 'yes',
                ),

                'checkout_type' => array(
                    'title'       => __('Checkout Type', 'wc-assetpayments'),
                    'type'        => 'select',
                    'description' => __('Select the checkout type for your store', 'wc-assetpayments'),
                    'default'     => 'classic',
                    'desc_tip'    => true,
                    'options'     => array(
                        'classic' => __('Classic', 'wc-assetpayments'),
                        'blocks'  => __('Block-based', 'wc-assetpayments'),
                    ),
                ),

                'public_key'  => array(
                    'title'       => __('Public key', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Public key AssetPayments. Required parameter', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'private_key' => array(
                    'title'       => __('Secret key', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Secret key AssetPayments. Required parameter', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'processing_id' => array(
                    'title'       => __('Processing ID', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Processing ID AssetPayments. Required parameter', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'template_id' => array(
                    'title'       => __('Template ID', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Template ID AssetPayments. Required parameter', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'skip_checkout'     => array(
                    'title'       => __('Skip checkout page', 'wc-assetpayments'),
                    'label'       => __('Set ON to skip AssetPayments test page', 'wc-assetpayments'),
                    'type'        => 'checkbox',
                    'description' => __('Turn this switch on to skip AssetPayments checkout page', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'title'       => array(
                    'title'       => __('Payment method title', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Title that appears on the checkout page', 'wc-assetpayments'),
                    'default'     => __('Card Visa/MasterCard (AssetPayments)'),
                    'desc_tip'    => true,
                ),

                'advance'       => array(
                    'title'       => __('Advance amount or %', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('The abount of advance payment', 'wc-assetpayments'),
                    'default'     => __(''),
                    'desc_tip'    => true,
                ),

                'advance_title'       => array(
                    'title'       => __('Advance product title', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Set title for advance payment', 'wc-assetpayments'),
                    'default'     => __(''),
                    'desc_tip'    => true,
                ),

                'description' => array(
                    'title'       => __('Card Visa/MasterCard (AssetPayments)', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Description that appears on the checkout page', 'wc-assetpayments'),
                    'default'     => __('Pay using the payment system AssetPayments::Pay with AssetPayments', 'wc-assetpayments'),
                    'desc_tip'    => true,
                ),

                'lang'         => array(
                    'title'       => __('Lang', 'wc-assetpayments'),
                    'type'        => 'select',
                    'description' => __('Select your language', 'wc-assetpayments'),
                    'default'     => 'en',
                    'desc_tip'    => true,
                    'options'     => array(
                        'en'    => __('English', 'wc-assetpayments'),
                        'uk'    => __('Ukrainian', 'wc-assetpayments'),
                        'ru'    => __('Russian', 'wc-assetpayments'),
                        'uk/en' => __('In depending on current language', 'wc-assetpayments'),
                    ),
                ),

                'lang_function' => array(
                    'title'       => __('Current language function', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('Function that returns current language code (if you select "In depending on current language")', 'wc-assetpayments'),
                    'default'     => 'get_locale',
                    'desc_tip'    => true,
                ),

                'alternative_callback'     => array(
                    'title'       => __('Alternative callback URL', 'wc-assetpayments'),
                    'label'       => __('Enable alternative callback URL', 'wc-assetpayments'),
                    'type'        => 'checkbox',
                    'description' => __('Enable to use your custom callback URL', 'wc-assetpayments'),
                    'default'     => 'no',
                    'desc_tip'    => true,
                ),

                'callback_url' => array(
                    'title'       => __('Callback URL', 'wc-assetpayments'),
                    'type'        => 'text',
                    'description' => __('If alternative callback enabled, set callback URL here', 'wc-assetpayments'),
                    'default'     => '',
                    'desc_tip'    => true,
                ),

                // Order status mapping (used by webhook StatusCode)
                'successful_payment_status' => array(
                    'title'       => __('Successful payment status', 'wc-assetpayments'),
                    'type'        => 'select',
                    'description' => __('WooCommerce order status to set when AssetPayments StatusCode = 1 (Approved).', 'wc-assetpayments'),
                    'desc_tip'    => true,
                    'default'     => 'wc-processing',
                    'options'     => wc_get_order_statuses(),
                ),

                'declined_payment_status' => array(
                    'title'       => __('Declined payment status', 'wc-assetpayments'),
                    'type'        => 'select',
                    'description' => __('WooCommerce order status to set when AssetPayments StatusCode = 2 (Declined/Failed).', 'wc-assetpayments'),
                    'desc_tip'    => true,
                    'default'     => 'wc-failed',
                    'options'     => wc_get_order_statuses(),
                ),

                'refunded_payment_status' => array(
                    'title'       => __('Refunded payment status', 'wc-assetpayments'),
                    'type'        => 'select',
                    'description' => __('WooCommerce order status to set when AssetPayments StatusCode = 5 (Refunded).', 'wc-assetpayments'),
                    'desc_tip'    => true,
                    'default'     => 'wc-refunded',
                    'options'     => wc_get_order_statuses(),
                )
        );
    }

    public function admin_options() {

        echo '<h3>' . __('AssetPayments Payment Gateway', 'wc-assetpayments') . '</h3>';
        echo '<p>' . __('AssetPayments payment gateway works by sending the user to AssetPayments to enter their payment information.', 'wc-assetpayments') . '</p>';

        echo '<table class="form-table">';
        $this->generate_settings_html();
        echo '</table>';
    }

    function process_payment($order_id) {
        $order = new WC_Order($order_id);
        return array(
            'result'   => 'success',
            'redirect' => add_query_arg('order-pay', $order->id, add_query_arg('key', $order->order_key, $order->get_checkout_payment_url(true)))
        );

    }

    public function receipt_page($order) {

        echo '<p>' . esc_html($this->pay_description) . '</p>';

        echo $this->generate_form($order);

    }

    public function generate_form($order_id) {

        global $woocommerce;
        $order = new WC_Order($order_id);
        // $result_url = add_query_arg('wc-api', 'wc_gateway_' . $this->id, home_url('/'));
        $result_url = add_query_arg('wc-api', 'WC_Gateway_Assetpayments', home_url('/'));
        $orderdata = wc_get_order( $order_id );

        // Advance amount logic: empty => order total, number => fixed, percent (e.g. 20%) => percent of total
        $advance_raw = trim((string) $this->get_option('advance'));
        $advance_amount = (float) $orderdata->get_total();
        if ($advance_raw !== '') {
            $normalized = str_replace(' ', '', $advance_raw);
            if (preg_match('/^([0-9]+(?:[\.,][0-9]+)?)%$/', $normalized, $m)) {
                $pct = (float) str_replace(',', '.', $m[1]);
                $advance_amount = round($advance_amount * ($pct / 100), 2);
            } elseif (preg_match('/^[0-9]+(?:[\.,][0-9]+)?$/', $normalized)) {
                $advance_amount = (float) str_replace(',', '.', $normalized);
            }
        }

        $address = $orderdata->get_billing_address_1().','.$orderdata->get_billing_city().','.$orderdata->get_billing_state().','.$orderdata->get_shipping_postcode().','.$orderdata->get_billing_country();

        $country = $orderdata->get_billing_country();
        $currency = $orderdata->get_currency();
        $total = $orderdata->get_total();
        $result_url = add_query_arg('order_id', $order_id, $result_url);
        $redirect_page_url = add_query_arg('assetpayments_redirect', '1', add_query_arg('order_id', $order_id, add_query_arg('key', $order->order_key, home_url('/'))));
        $redirect_page_url = str_replace('/?assetpayments_redirect=1', '/?assetpayments_redirect=1', $redirect_page_url);

            $request_cart = array();
            $request_cart['Products'] = array();
  			$request_cart['Products'] = apply_filters( 'woocommerce_assetpayments_cart', $request_cart['Products'], $order_id );
  			foreach ($orderdata->get_items() as $product) {
    			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product['product_id'] ), 'single-post-thumbnail' );
    			$request_cart['Products'][] = array(
            "ProductId" => $product['product_id'],
  					"ProductSku" => $product['product_id'],
  					"ProductName" => $product['name'],
  					"ProductPrice" => $product['line_total'] / $product['quantity'],
  					"ProductItemsNum" => $product['quantity'],
  					"ImageUrl" => $image[0],
  				);
  			}

        $deliveryName = ($orderdata->get_shipping_method() == '') ? 'Достава' : $orderdata->get_shipping_method();

  			//****Adding shipping method****//
  			$request_cart['Products'][] = array(
          "ProductId" => '12345',
  				"ProductSku" => '12345',
  				"ProductName" =>  $deliveryName,
  				"ProductPrice" => $orderdata->get_shipping_total(),
  				"ImageUrl" => 'https://assetpayments.com/img/delivery.png',
  				"ProductItemsNum" => 1,
  			);

        // If advance_title is set, replace Products with a single item using the advance amount
        $advance_title = trim((string) $this->get_option('advance_title'));
        if ($advance_title !== '') {
            $request_cart['Products'] = array();
            $request_cart['Products'][] = array(
                "ProductId" => '12345',
                "ProductSku" => '12345',
                "ProductName" => $advance_title,
                "ProductPrice" => $advance_amount,
                "ProductItemsNum" => 1,
            );
        }

  			$phone = preg_replace('/[^\d]+/', '', $orderdata->get_billing_phone());

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $hostname = $_SERVER['HTTP_HOST'];
        $websiteURL = $protocol . '://' . $hostname;

        $statusUrl = ($this->alternative_callback == 'yes' && $this->callback_url != '') ? esc_attr($this->callback_url) : esc_attr($result_url);
        $skipCheckout = ($this->skip_checkout == 'yes') ? true : false;

        $html = $this->cnb_form(array(
            'TemplateId' => intval($this->template_id),
            'MerchantInternalOrderId' => esc_attr($order_id),
            'StatusURL' => $statusUrl,
            'ReturnURL' => esc_url($redirect_page_url),
            'SkipCheckout' => $skipCheckout,
            'FirstName' => $orderdata->get_billing_first_name(),
            'LastName' => $orderdata->get_billing_last_name(),
            'Email' => $orderdata->get_billing_email(),
            'Phone' => preg_replace('/[^\d]+/', '', $orderdata->get_billing_phone()),
            'Address' => esc_attr($address),
            'CountryISO' => esc_attr($country),
            'Amount' => $advance_amount,
            'Currency' => esc_attr($currency),
            'AssetPaymentsKey' => esc_attr($this->public_key),
            'ProcessingId' => intval($this->processing_id),
            'TemplateId' => intval($this->template_id),
            'IpAddress' => $orderdata->get_customer_ip_address(),
            'CustomMerchantInfo' => "Order# " . $order_id,
            'Products' => $request_cart['Products'],
            'Lang'    => $this->lang
        ));

        return $html;

    }

    public function check_ipn_response() {

        // Read raw JSON body
        $raw  = file_get_contents('php://input');
        $json = json_decode($raw, true);

        if (empty($json) || !is_array($json)) {
            status_header(400);
            exit('No data');
        }

        // Extract required fields from webhook format
        $transactionId = isset($json['Payment']['TransactionId']) ? (string) $json['Payment']['TransactionId'] : '';
        $signature     = isset($json['Payment']['Signature']) ? (string) $json['Payment']['Signature'] : '';
        $statusCode    = isset($json['Payment']['StatusCode']) ? intval($json['Payment']['StatusCode']) : 0;
        $paymentAmount    = isset($json['Order']['Amount']) ? $json['Order']['Amount'] : 0;
        $paymentCurrency    = isset($json['Order']['Currency']) ? $json['Order']['Currency'] : 'UAH';
        $refundedAmount    = isset($json['Payment']['RefundedAmount']) ? $json['Payment']['RefundedAmount'] : 0;

        // OrderId may be in JSON and/or query string (?order_id=123)
        $orderIdFromJson = isset($json['Order']['OrderId']) ? (string) $json['Order']['OrderId'] : '';
        $orderIdFromGet  = isset($_GET['order_id']) ? (string) sanitize_text_field($_GET['order_id']) : '';
        $order_id        = $orderIdFromJson !== '' ? $orderIdFromJson : $orderIdFromGet;

        if ($order_id === '' || $transactionId === '' || $signature === '') {
            status_header(400);
            exit('Invalid request');
        }

        // Compute signature exactly as in your working PHP example:
        // requestSign = key:transactionId:UPPER(secret)
        // sign = HMAC-MD5(requestSign, secret)
        $key    = (string) $this->public_key;   // AssetPaymentsKey
        $secret = (string) $this->private_key;  // AssetPaymentsSecret

        $requestSign = $key . ':' . $transactionId . ':' . strtoupper($secret);
        $sign        = hash_hmac('md5', $requestSign, $secret);

        // Compare signatures (case-insensitive to avoid casing differences)
        if (strtolower($sign) !== strtolower($signature)) {
            status_header(403);
            exit('Hash mismatch');
        }

        // Load order
        $order = wc_get_order((int) $order_id);
        if (!$order) {
            status_header(404);
            exit('Order not found');
        }

        // Helper: map 'wc-processing' => 'processing'
        $normalize_wc_status = function($status_key) {
            $status_key = (string) $status_key;
            if (strpos($status_key, 'wc-') === 0) {
                return substr($status_key, 3);
            }
            return $status_key;
        };

        // Pull configured statuses, validate against Woo status list
        $all_statuses = wc_get_order_statuses();

        $success_key  = isset($all_statuses[$this->successful_payment_status]) ? $this->successful_payment_status : 'wc-processing';
        $declined_key = isset($all_statuses[$this->declined_payment_status])   ? $this->declined_payment_status   : 'wc-failed';
        $refund_key   = isset($all_statuses[$this->refunded_payment_status])   ? $this->refunded_payment_status   : 'wc-refunded';

        $success_status  = $normalize_wc_status($success_key);
        $declined_status = $normalize_wc_status($declined_key);
        $refund_status   = $normalize_wc_status($refund_key);

        /**
         * StatusCode mapping
         * 1 = Approved
         * 2 = Failed/Declined
         * 4 = Authorized (hold)
         * 5 = Refunded
         */

        if ($statusCode === 1) {

            // Record payment + transaction id
            if (!$order->is_paid()) {
                $order->payment_complete($transactionId);
            }

            // Also enforce selected status (processing/completed/etc)
            if ($order->get_status() !== $success_status) {
                $order->update_status(
                    $success_status,
                    __('AssetPayments: Payment ' . $paymentAmount . ' ' . $paymentCurrency . ' approved. TransactionId: ', 'wc-assetpayments') . $transactionId
                );
            } else {
                $order->add_order_note(
                    __('AssetPayments: Payment ' . $paymentAmount . ' ' . $paymentCurrency . ' approved. TransactionId: ', 'wc-assetpayments') . $transactionId
                );
            }

            exit('OK');

        } elseif ($statusCode === 2) {

            $order->update_status(
                $declined_status,
                __('AssetPayments: Payment ' . $paymentAmount . ' ' . $paymentCurrency . ' declined. TransactionId: ', 'wc-assetpayments') . $transactionId
            );

            exit('OK');

        } elseif ($statusCode === 5) {

            $order->update_status(
                $refund_status,
                __('AssetPayments: Payment ' . $refundedAmount . ' ' . $paymentCurrency . ' refunded. TransactionId: ', 'wc-assetpayments') . $transactionId
            );

            exit('OK');

        } elseif ($statusCode === 4) {

            // Authorized/holded. Keep as on-hold (or you can add a setting later)
            $order->update_status(
                'on-hold',
                __('AssetPayments: Payment ' . $refundedAmount . ' ' . $paymentCurrency . ' authorized. TransactionId: ', 'wc-assetpayments') . $transactionId
            );

            exit('OK');

        } else {

            // Unknown status codes should not break webhook delivery.
            $order->add_order_note(
                __('AssetPayments: Webhook received with unknown StatusCode: ', 'wc-assetpayments') .
                $statusCode . '. TransactionId: ' . $transactionId
            );

            exit('OK');
        }
    }

    public function thankyou_page() {
        echo '<p>' . __('Thank you for your purchase.', 'wc-assetpayments') . '</p>';
    }

    function cnb_form($data) {

        $form_args = array(
            'action' => esc_url($this->_checkout_url),
            'method' => 'POST',
            'id'     => 'assetpayments_checkout_form',
        );

        // Build Base64(JSON) payload
        // Use wp_json_encode for correct UTF-8 handling in WP.
        $json    = wp_json_encode($data, JSON_UNESCAPED_UNICODE);
        $payload = base64_encode($json);

        $form  = '<form action="' . $form_args['action'] . '" method="' . $form_args['method'] . '" id="' . $form_args['id'] . '" accept-charset="utf-8">';
        $form .= '<input type="hidden" name="data" value="' . esc_attr($payload) . '" />';
        $form .= '<input type="submit" class="button alt" id="submit_assetpayments_payment_form" value="' . esc_attr__('Pay via AssetPayments', 'wc-assetpayments') . '" />';
        $form .= '</form>';

        $form .= '<script type="text/javascript">
                    document.getElementById("assetpayments_checkout_form").submit();
                  </script>';

        return $form;
    }

}
