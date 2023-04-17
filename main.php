<?php
/*
Plugin Name: Auto Register Users
Plugin URI: https://lampeire.com
Description: Automatically registers users after they place an order in WooCommerce and sends them a confirmation message.
Version: 1.0
Author: Muneza Dixon
Author URI: https://kaizoku010.github.io/firebrand/
*/

add_action('woocommerce_thankyou', 'auto_register_users');

function auto_register_users($order_id)
{
    $order = wc_get_order($order_id);
    $user_email = $order->get_billing_email();

    // Check if user with the email already exists
    $existing_user = get_user_by('email', $user_email);

    if (!$existing_user) {
        // Generate a random password
        $password = wp_generate_password(12, false);

        // Create a new user with the billing information
        $new_user_id = wp_insert_user(array(
            'user_email' => $user_email,
            'user_login' => $user_email,
            'user_pass' => $password,
            'first_name' => $order->get_billing_first_name(),
            'last_name' => $order->get_billing_last_name(),
            'nickname' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'display_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'role' => 'customer'
        ));

        // Update user meta data with billing and shipping information
        $posted_data = $_POST;
        update_user_meta($new_user_id, 'billing_first_name', $order->get_billing_first_name());
        update_user_meta($new_user_id, 'billing_last_name', $order->get_billing_last_name());
        update_user_meta($new_user_id, 'billing_company', $order->get_billing_company());
        update_user_meta($new_user_id, 'billing_address_1', $order->get_billing_address_1());
        update_user_meta($new_user_id, 'billing_address_2', $order->get_billing_address_2());
        update_user_meta($new_user_id, 'billing_city', $order->get_billing_city());
        update_user_meta($new_user_id, 'billing_state', $order->get_billing_state());
        update_user_meta($new_user_id, 'billing_postcode', $order->get_billing_postcode());
        update_user_meta($new_user_id, 'billing_country', $order->get_billing_country());
        update_user_meta($new_user_id, 'billing_phone', $order->get_billing_phone());
        update_user_meta($new_user_id, 'shipping_first_name', $order->get_shipping_first_name());
        update_user_meta($new_user_id, 'shipping_last_name', $order->get_shipping_last_name());
        update_user_meta($new_user_id, 'shipping_company', $order->get_shipping_company());
        update_user_meta($new_user_id, 'shipping_address_1', $order->get_shipping_address_1());
        update_user_meta($new_user_id, 'shipping_address_2', $order->get_shipping_address_2());
        update_user_meta($new_user_id, 'shipping_city', $order->get_shipping_city());
        update_user_meta($new_user_id, 'shipping_state', $order->get_shipping_state());
        update_user_meta($new_user_id, 'shipping_postcode', $order->get_shipping_postcode());
        // Send confirmation email to the user
        $to = $user_email;
        $subject = 'Welcome to Our Website';
        $body = 'Thank you for your order. Your account has been created. Your username is ' . $user_email . ' and your password is ' . $password . '.';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $body, $headers);
    }
}
