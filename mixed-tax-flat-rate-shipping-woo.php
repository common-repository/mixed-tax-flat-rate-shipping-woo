<?php
/**
 * The main plugin file for Woo mixed tax flat rate shipping.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Woo_Mixed_Tax_Flat_Rate_Shipping
 * @author    BjornTech - BjornTech AB <hello@bjorntech.com>
 * @license   GPL-3.0
 * @link      https://bjorntech.com
 * @copyright 2018-2019 BjornTech AB
 *
 * @wordpress-plugin
 * Plugin Name:       Mixed Tax Flat Rate Shipping Woo
 * Plugin URI:        https://www.bjorntech.com/mixedtaxtlattateshipping
 * Description:       Creates a new shipping method that enables a fixed price shipping that works with a basket of products with mixed taxes.
 * Version:           1.0.3
 * Author:            BjornTech
 * Author URI:        https://bjorntech.com
 * Text Domain:       woo-mixed_tax_flat_rate_shipping
 * Domain Path:       /languages
 *
 * WC requires at least: 3.3
 * WC tested up to: 4.0
 *
 * Copyright:         2017-2019 BjornTech AB
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') || exit;

class WC_Mixed_Tax_Flat_Rate_Shipping
{

    /**
     * Plugin data
     */
    const NAME = 'Mixed Tax Flat Rate Shipping Woo';
    const VERSION = '1.0.2';
    const SCRIPT_HANDLE = 'mixed-tax-flat-rate-shipping-woo';
    const PLUGIN_FILE = __FILE__;

    public $plugin_basename;
    public $includes_dir;
    public $assets_url;

    /**
     *    $instance
     *
     * @var    mixed
     * @access public
     * @static
     */
    public static $instance = null;

    /**
     * Plugin helper classes
     */

    public function __construct()
    {
        $this->plugin_basename = plugin_basename(self::PLUGIN_FILE);
        $this->includes_dir = plugin_dir_path(self::PLUGIN_FILE) . 'includes/';
        $this->external_dir = plugin_dir_path(self::PLUGIN_FILE) . 'external/';
        $this->assets_url = trailingslashit(plugins_url('assets', self::PLUGIN_FILE));

        add_action('plugins_loaded', array($this, 'maybe_load_plugin'));
    }

    public function maybe_load_plugin()
    {
        // Don't load anything if WooCommerce isn't installed/active
        if (!class_exists('WooCommerce')) {
            return;
        }

        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_filter('woocommerce_shipping_methods', array($this, 'add_shipping_method'));
    }

    public function add_shipping_method($shipping_methods)
    {
        $shipping_methods['flat_rate_inc_vat'] = 'WC_Shipping_Flat_Rate_Inc_VAT';
        require_once $this->includes_dir . 'class-mixed-tax-flat-rate-shipping-woo.php';
        return $shipping_methods;
    }

    /**
     * Returns a new instance of self, if it does not already exist.
     *
     * @access public
     * @static
     * @return WC_Mixed_Tax_Flat_Rate_Shipping
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
/**
 * Make the object available for later use
 *
 * @return WC_Mixed_Tax_Flat_Rate_Shipping
 */
function WC_MT()
{
    return WC_Mixed_Tax_Flat_Rate_Shipping::instance();
}

/**
 * Instantiate
 */
WC_MT();
