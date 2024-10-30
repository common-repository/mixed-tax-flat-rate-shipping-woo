<?php

if (!defined('ABSPATH')) {
    exit;
}

$cost_desc = __('Enter a cost (incl. tax).', 'woo-mixed_tax_flat_rate_shipping');

/**
 * Settings for flat rate shipping.
 */
$settings = array(
    'title' => array(
        'title' => __('Method title', 'woo-mixed_tax_flat_rate_shipping'),
        'type' => 'text',
        'description' => __('This controls the title which the user sees during checkout.', 'woo-mixed_tax_flat_rate_shipping'),
        'default' => __('Flat rate', 'woo-mixed_tax_flat_rate_shipping'),
        'desc_tip' => true,
    ),
    'tax_status' => array(
//        'title'         => __( 'Tax status', 'woo-mixed_tax_flat_rate_shipping' ),
        'type' => 'hidden',
//        'class'         => 'wc-enhanced-select',
        'default' => 'taxable',
        'options' => array(
            'taxable' => __('Taxable', 'woo-mixed_tax_flat_rate_shipping'),
            'none' => _x('None', 'Tax status', 'woo-mixed_tax_flat_rate_shipping'),
        ),
    ),
    'cost' => array(
        'title' => __('Cost', 'woo-mixed_tax_flat_rate_shipping'),
        'type' => 'text',
        'placeholder' => '',
        'description' => $cost_desc,
        'default' => '0',
        'desc_tip' => true,
    ),
);

return $settings;
