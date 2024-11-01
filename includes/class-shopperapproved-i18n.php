<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 */
class Shopper_Approved_i18n {


    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.0.1
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            SHOPPER_APPROVED_PLUGIN_PREFIX,
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}