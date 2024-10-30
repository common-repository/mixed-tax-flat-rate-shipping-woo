<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Shipping_Flat_Rate_Inc_VAT extends WC_Shipping_Method
{

    /** @var string cost passed to [fee] shortcode */
    protected $fee_cost = '';

    /**
     * Constructor.
     *
     * @param int $instance_id
     */
    public function __construct($instance_id = 0)
    {
        $this->id = 'flat_rate_inc_vat';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('Flat rate including VAT', 'woo-mixed_tax_flat_rate_shipping');
        $this->method_description = __('Lets you charge a fixed rate for shipping, the rate includes VAT', 'woo-mixed_tax_flat_rate_shipping');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        );
        $this->init();

        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * init user set variables.
     */
    public function init()
    {
        $this->instance_form_fields = include plugin_dir_path(__FILE__) . 'settings-mixed-tax-flat-rate-shipping-woo.php';
        $this->title = $this->get_option('title');
        $this->tax_status = $this->get_option('tax_status');
        $this->cost = $this->get_option('cost');
        $this->type = $this->get_option('type', 'class');
    }

    /**
     * Work out fee (shortcode).
     * @param  array $atts
     * @return string
     */
    public function fee($atts)
    {
        $atts = shortcode_atts(array(
            'percent' => '',
            'min_fee' => '',
            'max_fee' => '',
        ), $atts, 'fee');

        $calculated_fee = 0;

        if ($atts['percent']) {
            $calculated_fee = $this->fee_cost * (floatval($atts['percent']) / 100);
        }

        if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']) {
            $calculated_fee = $atts['min_fee'];
        }

        if ($atts['max_fee'] && $calculated_fee > $atts['max_fee']) {
            $calculated_fee = $atts['max_fee'];
        }

        return $calculated_fee;
    }

    /**
     * calculate_shipping function.
     *
     * @param array $package (default: array())
     */
    public function calculate_shipping($package = array())
    {
        $rate = array(
            'id' => $this->get_rate_id(),
            'label' => $this->title,
            'package' => $package,
        );

        // Calculate the costs
        $has_costs = false; // True when a cost is set. False if all costs are blank strings.
        $max_cost = $this->get_option('cost');

        if ('' !== $max_cost) {
            $has_costs = true;
            $rate = array_merge($this->calculate_vat_rows($package, $max_cost), $rate);
        }
        // Add the rate
        if ($has_costs) {
            $this->add_rate($rate);
        }

    }

    /**
     * Get items in package.
     * @param  array $package
     * @return int
     */
    public function get_package_item_qty($package)
    {
        $total_quantity = 0;
        foreach ($package['contents'] as $item_id => $values) {
            if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
                $total_quantity += $values['quantity'];
            }
        }
        return $total_quantity;
    }

    /**
     * Finds and returns shipping classes and the products with said class.
     * @param mixed $package
     * @return array
     */
    public function find_shipping_classes($package)
    {
        $found_shipping_classes = array();

        foreach ($package['contents'] as $item_id => $values) {
            if ($values['data']->needs_shipping()) {
                $found_class = $values['data']->get_shipping_class();

                if (!isset($found_shipping_classes[$found_class])) {
                    $found_shipping_classes[$found_class] = array();
                }

                $found_shipping_classes[$found_class][$item_id] = $values;
            }
        }

        return $found_shipping_classes;
    }

    private function calculate_vat_rows($package, $max_cost)
    {

        $tax_sums = array();
        foreach ($package['contents'] as $contents) {
            isset($contents['data']) ? $product = $contents['data'] : $product = $contents->get_product();
            $tax_class = $product->get_tax_class();
            $tax_sums[$tax_class] = isset($tax_sums[$tax_class]) ? $tax_sums[$tax_class] + $contents['line_subtotal'] : $contents['line_subtotal'];
        }

        $net_divider = 0;
        foreach ($tax_sums as $tax_class => $tax_sum) {
            $rates = WC_Tax::get_rates($tax_class);
            $multiplier = 1 + (reset($rates)['rate'] / 100);
            $net_sum = ($tax_sum / $package['contents_cost']) * $multiplier;
            $net_sums[$tax_class] = $net_sum;
            $net_divider += $net_sum;
        }

        $net_cost = round($max_cost / $net_divider, 2);

        $taxes = array();
        foreach ($tax_sums as $tax_class => $tax_sum) {
            $cost_share = ($tax_sum / $package['contents_cost']) * $net_cost;
            $cost_inc = WC_Tax::calc_shipping_tax($cost_share, WC_Tax::get_rates($tax_class));
            foreach (array_keys($cost_inc) as $key) {
                $taxes[$key] = round(isset($cost_inc[$key]) ? $cost_inc[$key] : 0, 2);
            }
            $cost[] = $cost_share;
        }

        $total_tax = 0;
        foreach ($taxes as $tax) {
            $total_tax += $tax;
        }

        $rate = array(
            'cost' => $max_cost - $total_tax,
            'taxes' => $taxes,
            'meta_data' => array(),
        );

        return $rate;
    }
}
