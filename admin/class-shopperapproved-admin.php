<?php
/**
 * Adds styles and scripts for admin-specific functionality of the plugin.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/admin
 * @author     Shopper Approved
 */
class Shopper_Approved_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string $plugin_name The ID of this plugin i.e. 'shopper_approved'
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    0.0.1
     */
    public function __construct( string $plugin_name, string $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.0.1
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shopperapproved-admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts() {

        wp_register_script( "{$this->plugin_name}_admin", plugin_dir_url( __FILE__ ) . 'js/shopperapproved-admin.js', array(), $this->version, true );

        wp_enqueue_script( "{$this->plugin_name}_admin" );
        wp_localize_script( "{$this->plugin_name}_admin", "{$this->plugin_name}_admin", array(
            'integrationAPI' => array(
                'newAPI' => SHOPPER_APPROVED_APP_INTEGRATION_API
            ),
            'domain' => get_option( 'sa_domain', rtrim(str_replace( ['http://', 'https://'], '', get_site_url() ), '/' ) ),
            'adminAjax' => admin_url( 'admin-ajax.php' ),
            'redirectURI' => esc_url( menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false ) . '&action=login' ),
            'widgetsURI' => esc_url( menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgets_menu', false ) )
        ));
    }
}