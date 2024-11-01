<?php
/**
 * Adds styles and scripts for public-facing functionality of the plugin.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 * @author     Shopper Approved
 */
class Shopper_Approved_Public {

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
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     *
     * @since    0.0.1
     */
    public function __construct( string $plugin_name, string $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        add_shortcode( SHOPPER_APPROVED_PLUGIN_PREFIX.'_reviews_widget', array( $this, 'reviews_shortcode' ) );
    }

    public function reviews_shortcode() {
        global $product;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function enqueue_styles()
    {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shopperapproved-public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts()
    {
        if ( is_page( 'reviews' ) ) {
            wp_register_script( SHOPPER_APPROVED_PLUGIN_NAME . "_reviews_page",  plugin_dir_url( __FILE__ ) . 'js/sa-reviews.js', array(), false, true );
            wp_enqueue_script( SHOPPER_APPROVED_PLUGIN_NAME . "_reviews_page" );
            wp_localize_script( SHOPPER_APPROVED_PLUGIN_NAME . "_reviews_page", SHOPPER_APPROVED_PLUGIN_PREFIX, array(
                'url' => SHOPPERAPPROVED_URI,
                'siteId' => get_option( 'sa_site_id' )
            ) );
        }

        wp_register_script( SHOPPER_APPROVED_PLUGIN_NAME . "_category", plugin_dir_url( __FILE__ ) . 'js/sa-category.js', array(), false, true );
    }
}