<?php
/**
 * Plugin Name: Shopper Approved Reviews
 * Plugin URI: https://shopperapproved.com/
 * Description: A toolkit that helps you integrate Shopper Approved with WooCommerce.
 * Version: 2.0
 * Author: Shopper Approved
 * Author URI: https://shopperapproved.com
 * Domain Path: /languages/
 * Requires at least: 6.4
 * Requires PHP: 7.0
 *
 * @package shopperapproved
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

const SHOPPER_APPROVED_VERSION = '2.0';
const SHOPPER_APPROVED_PLUGIN_NAME = 'shopperapproved-reviews';
const SHOPPER_APPROVED_PLUGIN_PREFIX = "shopperapproved";
const SHOPPER_APPROVED_PLUGIN_ROOT_PATH = WP_PLUGIN_DIR . '/' . SHOPPER_APPROVED_PLUGIN_NAME . '/';
$uploads_info = wp_upload_dir();
if($uploads_info['error']){
    echo '<b style="color:#ff0000"> Your uploads directory is not writable </b>';exit;
}
define( 'SHOPPER_APPROVED_MAIN_UPLOADS_DIR', $uploads_info['path'] . '/');
define( 'SHOPPER_APPROVED_MAIN_UPLOADS_URL', $uploads_info['url'] . '/' );
define( 'SHOPPER_APPROVED_PLUGIN_ROOT_URL', plugins_url() . '/' . SHOPPER_APPROVED_PLUGIN_NAME . '/');
const SHOPPERAPPROVED_URI = 'shopperapproved.com';
const SHOPPER_APPROVED_APP_INTEGRATION_API = 'https://api.shopperapproved.com/integration';
const SHOPPER_APPROVED_MODE = 'live';

/**
 * The code that runs during plugin activation.
 */
function activate_shopperapproved()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-shopperapproved-activator.php';
    Shopper_Approved_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_shopperapproved()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-shopperapproved-deactivator.php';
    Shopper_Approved_Deactivator::deactivate();
}

/**
 * This registers the activation and deactivation functions
 */
register_activation_hook( __FILE__, 'activate_shopperapproved' );
register_deactivation_hook( __FILE__, 'deactivate_shopperapproved' );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-shopperapproved.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-shopperapproved-products.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_shopperapproved()
{
    $plugin = new Shopper_Approved();
    $plugin->add_admin_menus();

    if (get_option('sa_survey_status')){ // check status of Thank you page survey setting
        $plugin->add_survey_code();
    }

    if (get_option('sa_seal_status') && get_option('sa_seal_status2')){ // check status of Seal setting
        $plugin->add_seal_code();
    }

    if (get_option('sa_pwidgets_status')) {
        $plugin->add_product_code(); // handle Product Page Widget AND Product Stars Widget code
    }

    if (get_option('sa_cstars_status')){ // check status of Category page stars status
        $plugin->add_category_code();
    }

    $plugin->run();
}
run_shopperapproved();

/*    Set up WP-Crons for product feeds     */
/* Cron Job to generate Product Feed is not needed at the moment!
function generate_feeds_exec()
{
    $directory = SHOPPER_APPROVED_MAIN_UPLOADS_DIR;
    $filename = 'products_feed.csv';
    $filepath = $directory . $filename;
    Shopper_Approved_Products::upload_products_csv($filepath);
}

add_action( 'generate_feeds_hook', 'generate_feeds_exec' );

// schedule uploading of Products CSV once daily
// reference: https://developer.wordpress.org/reference/functions/wp_schedule_event/
if ( ! wp_next_scheduled( 'generate_feeds_hook' ) ) {
    wp_schedule_event( time(), 'daily', 'generate_feeds_hook' );
}
*/

add_filter( 'plugin_action_links_shopperapproved/'.SHOPPER_APPROVED_PLUGIN_NAME.'.php', function( $actions ) {
    $sa_actions = [];
    $sa_actions['settings'] = '<a href="' . esc_url( menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu',  false ) ) . '">' . __('Dashboard', SHOPPER_APPROVED_PLUGIN_NAME) . '</a>';
    return array_merge($sa_actions, $actions);
} );

/**
 * function to make calls to Shopper Approved API
 *
 * @param string $name
 * @param array $body_array
 * @return array|WP_Error
 */
function use_shopperapproved_api(string $name, array $body_array = [])
{
    // All SA API calls need Site ID and Token to work properly!
    $body_array['siteId'] = get_option('sa_site_id');
    $body_array['siteToken'] = get_option('sa_api_token');

    $api_url = SHOPPERAPPROVED_URI.'/account/api/woocommerce';
    if (SHOPPER_APPROVED_MODE == 'local' || SHOPPER_APPROVED_MODE == 'dev') {
        $api_url = 'http://'.SHOPPER_APPROVED_MODE.'.'.$api_url;
    } else {
        $api_url = 'https://'.$api_url;
    }

    $response = wp_remote_post( $api_url . '/'.$name, array(
        'method' => 'POST',
        'headers' => array('Content-Type' => 'application/json'),
        'body' => json_encode($body_array)
    ));

    return $response;
}

// allow ajax to update SA options
// reference: https://wordpress.stackexchange.com/questions/183307/update-option-in-wordpress-ajax
function ajax_callback_update_sa_option()
{
    // get the post data
    $option_key = $_POST["option_key"];
    $option_value = sanitize_text_field($_POST["option_value"]);

    // create the array we send back to javascript here
    $array_to_send_back = array( 'msg' => 'Option updated successfully!' );

    if (empty($_POST["not_boolean"])) {
        // dealing with boolean value (true or false)
        $option_value = filter_var($option_value, FILTER_VALIDATE_BOOLEAN);
    }

    // if user disabled Thank you page then step 3 status should show failed
    if ($option_key == 'sa_survey_status' && ! $option_value) {
        update_option('sa_step3_status', false);
    }

    // check if something additional needs to be done before updating option
    if ($option_key == 'sa_rp_status') {
        $admin_menus = new Shopper_Approved_Admin_Menus();

        // handle Review page widget if needed
        $admin_menus->handle_review_page_widget($option_value); // add or remove Reviews page based on user's choice
        $array_to_send_back['show_rp_code'] = get_option('rp_already_exists');
    }

    // update SA option
    update_option( $option_key, $option_value );

    // json encode the output because that's what it is expecting
    echo json_encode( $array_to_send_back );

    // die when finished doing ajax output.
    die();
}

// allow ajax to update SA seal excluded URL's
function ajax_callback_update_sa_seal_excluded()
{
    // Get the post data
    $option_value = sanitize_text_field($_POST["option_value"]);

    // Create the array we send back to javascript here
    $array_to_send_back = array( 'msg' => 'Option updated successfully!' );

    $arr = get_option( 'sa_seal_excluded');
    $add_seal = filter_var($_POST["add_seal"], FILTER_VALIDATE_BOOLEAN);
    if ($add_seal) {

        // add value to Seal Excluded URL array
        array_push($arr, $option_value);

    } else {
        // remove value to Seal Excluded URL array
        // reference: https://stackoverflow.com/questions/4120589/remove-string-from-php-array
        $index = array_search($option_value, $arr);
        if($index !== FALSE){
            unset($arr[$index]);
        }
    }

    // update Seal excluded URL's
    update_option( 'sa_seal_excluded', $arr );

    // json encode the output because that's what it is expecting
    echo json_encode( $array_to_send_back );

    // die when finished doing ajax output.
    die();
}

add_action( 'wp_ajax_' . 'update_sa_option', 'ajax_callback_update_sa_option' );
add_action( 'wp_ajax_' . 'update_sa_seal_excluded', 'ajax_callback_update_sa_seal_excluded' );