<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 * @author     Shopper Approved
 */
class Shopper_Approved {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var $loader    // Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $plugin_prefix    The string used to uniquely identify this plugin.
     */
    protected $plugin_prefix;

    /**
     * The current version of the plugin.
     *
     * @since    0.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function __construct()
    {
        if ( defined( 'SHOPPER_APPROVED_VERSION' ) ) {
            $this->version = SHOPPER_APPROVED_VERSION;
        } else {
            $this->version = '0.0.1';
        }
        $this->plugin_name = SHOPPER_APPROVED_PLUGIN_NAME;
        $this->plugin_prefix = SHOPPER_APPROVED_PLUGIN_PREFIX;
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     *
     * @since    0.0.1
     * @access   private
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shopperapproved-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shopperapproved-i18n.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shopperapproved-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shopperapproved-public.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shopperapproved-admin-menus.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shopperapproved-survey.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shopperapproved-category.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shopperapproved-product.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shopperapproved-seal.php';
        $this->loader = new Shopper_Approved_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Shopper_Approved_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.0.1
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Shopper_Approved_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.0.1
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Shopper_Approved_Admin( $this->get_plugin_prefix(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.0.1
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Shopper_Approved_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
    }

    /**
     * A utility function that is used to add admin menus.
     *
     * @since    0.0.1
     * @access   private
     */
    public function add_admin_menus()
    {
        $admin_menus = new Shopper_Approved_Admin_Menus();
        $this->loader->add_action('admin_menu',$admin_menus,SHOPPER_APPROVED_PLUGIN_PREFIX.'_options_page');
    }

    /**
     * A utility function that is used to add survey code on Thank you page
     *
     * @since    0.0.1
     * @access   private
     */
    public function add_survey_code()
    {
        $sa_survey = new Shopper_Approved_Survey();
        $this->loader->add_action('woocommerce_thankyou',$sa_survey,SHOPPER_APPROVED_PLUGIN_PREFIX.'_add_survey');
    }

    /**
     * A utility function that is used to add seal code.
     *
     * @return void
     */
    public function add_seal_code()
    {
        $sa_seal = new Shopper_Approved_Seal();
        $this->loader->add_action('wp_footer',$sa_seal,SHOPPER_APPROVED_PLUGIN_PREFIX.'_seal_code', 11);
    }

    /**
     * A utility function that is used to add product page code.
     *
     * @since    0.0.1
     * @access   private
     */
    public function add_product_code()
    {
        $sa_product = new Shopper_Approved_Product();

        $this->loader->add_action('woocommerce_single_product_summary', $sa_product,SHOPPER_APPROVED_PLUGIN_PREFIX.'_product_stars',11);

        $this->loader->add_action( 'woocommerce_after_single_product', $sa_product, SHOPPER_APPROVED_PLUGIN_PREFIX.'_product_widget', 11 );
    }

    /**
     * A utility function that is used to add category page code.
     *
     * @since    0.0.1
     * @access   private
     */
    public function add_category_code()
    {
        $sa_category = new Shopper_Approved_Category();
        $this->loader->add_action('woocommerce_after_shop_loop_item',$sa_category,SHOPPER_APPROVED_PLUGIN_PREFIX.'_category_code');
        $this->loader->add_action('wp_footer',$sa_category,SHOPPER_APPROVED_PLUGIN_PREFIX.'_category_script');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.0.1
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.0.1
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name(): string
    {
        return $this->plugin_name;
    }

    /**
     * The prefix of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.0.1
     * @return    string    The name of the plugin.
     */
    public function get_plugin_prefix(): string
    {
        return $this->plugin_prefix;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     0.0.1
     * @return    //Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.0.1
     * @return    string    The version number of the plugin.
     */
    public function get_version(): string
    {
        return $this->version;
    }
}