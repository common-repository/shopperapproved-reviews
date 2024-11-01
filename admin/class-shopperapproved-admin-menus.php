<?php
/**
 * The admin menus functionality of the plugin.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/admin
 */

/**
 * This class adds admin menus.
 * @package    shopperapproved
 * @subpackage shopperapproved/admin
 * @author     Shopper Approved
 */
class Shopper_Approved_Admin_Menus
{
    /**
     * @var mixed
     */
    private $reviewpage_code;

    public function __construct()
    {
        require_once plugin_dir_path(__FILE__) . 'partials/shopperapproved-admin-display.php';

        // update reviews shortcode
        $this->reviewpage_code = '['.SHOPPER_APPROVED_PLUGIN_PREFIX.'_reviews]';

        add_shortcode(SHOPPER_APPROVED_PLUGIN_PREFIX.'_seal', array( $this, 'seal_shortcode' ) );
        add_shortcode( SHOPPER_APPROVED_PLUGIN_PREFIX.'_reviews', array( $this, 'reviews_shortcode' ) );
        add_shortcode( SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou', array( $this, 'thankyou_shortcode' ) );
    }

    /**
     * This function is used to display the plugin tabs on Wordpress's left side menu
     *
     * @return void
     */
    public function shopperapproved_options_page()
    {
        add_menu_page(
            'Shopper Approved',
            'Shopper Approved',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_main_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_html'),
            SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/sa-icon.png'
        );

        add_submenu_page(
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_main_menu',
            get_option('sa_site_id') ? 'Product Feed' : 'Setup',
            'Product Feed',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_html')
        );

        add_submenu_page(
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_main_menu',
            'Thank You Page Survey',
            'Thank You Page Survey',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_html')
        );

        add_submenu_page(
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_main_menu',
            'Surveys',
            'Surveys',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_surveys_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_surveys_html')
        );

        add_submenu_page(
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_main_menu',
            'Seals & Widgets',
            'Seals & Widgets',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgets_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgetmenu_html')
        );

        add_submenu_page(
            'options.php',
            'Congratulations',
            'Congratulations',
            'manage_options',
            SHOPPER_APPROVED_PLUGIN_PREFIX.'_congrats_menu',
            array($this, SHOPPER_APPROVED_PLUGIN_PREFIX.'_congrats_html')
        );
    }

    /**
     * @return false|mixed|void
     */
    public function reviews_shortcode()
    {
        if(get_option('sa_review_page_code')){
            return get_option('sa_review_page_code');
        }
    }

    /**
     * @return false|mixed|void
     */
    public function thankyou_shortcode()
    {
        if(get_option('sa_thankyou_code')){
            return get_option('sa_thankyou_code');
        }
    }

    /**
     * @return array|false|mixed|string|string[]|null
     */
    public function seal_shortcode()
    {
        if(get_option('sa_seal_code')){
            return get_option('sa_seal_code');
        }
    }

    /**
     * displays the Product Feed page HTML
     *
     * @return false|void
     */
    public function shopperapproved_productfeed_html()
    {
        $sa_settings_result = $this->check_settings();

        if ( !empty($_POST['generate_feed']) || (!empty($_REQUEST['tab']) && $_REQUEST['tab'] === 'generate_feed_download') ) {

            $result = Shopper_Approved_Products::upload_products_csv();
            if ( ! $result ) {

                update_option('sa_step2_status', false );
                get_feed_progress_view(0); // show failed feed page!
                get_sa_footer();

                // notify SA support
                use_shopperapproved_api('send-email', array('action' => 'notify_about_error', 'error' => 'Unexpected error occurred when generating the Products CSV file'));
                return false;
            }

            $csv_file = esc_url( Shopper_Approved_Products::get_csv_url());

            $response = wp_remote_post( SHOPPER_APPROVED_APP_INTEGRATION_API . '/new-feed/woocommerce/'.get_option('sa_site_id'), array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode(array(
                        'csv_file' =>  $csv_file,
                        'token' => get_option('sa_api_token')
                    )
                ) ));

            if (is_wp_error($response)) {

                update_option('sa_step2_status', false );
                get_feed_progress_view(0); // show failed feed page!
                get_sa_footer();

                // notify SA support about Error from WordPress
                use_shopperapproved_api('send-email', array('action' => 'notify_about_error', 'error' => 'Error from WordPress: '.$response->get_error_message()));

                return false;

            } else if ($response['response']['code'] !== 200) {

                update_option('sa_step2_status', false );
                get_feed_progress_view(0); // show failed feed page!
                get_sa_footer();

                // notify SA support about Python Client API error
                use_shopperapproved_api('send-email', array('action' => 'notify_about_error', 'error' => 'Python Client API error: '.json_decode($response['body'])->message));

                return false;
            }

            // step 2 is completed successfully
            update_option('sa_step2_status', true );
            update_option('sa_step2b_status', false );

            // show successful feed page!
            get_feed_progress_view();
            get_sa_footer();

        } else {

            if ((isset($_REQUEST['action']) && $_REQUEST['action'] == 'login-again')) {

                // clear both site ID and token
                delete_option('sa_site_id') ;
                delete_option('sa_survey_token');

                // also clear all progress as a different Site ID will require all steps to repeated!
                delete_option('sa_step_progress');
                delete_option('sa_step1_status');
                delete_option('sa_step2_status');
                delete_option('sa_step2b_status');
                delete_option('sa_step3_status');
                delete_option('sa_step4_status');
                delete_option('sa_step5_status');
                delete_option('sa_step6_status');
            }

            if ( ! get_option('sa_site_id') || ! get_option('sa_survey_token') ) {
                get_settings_view($sa_settings_result); // go to Login page in case of no Site ID and Token stored
            } else {

                if (isset($_POST['generate_feed']) || (isset($_REQUEST['tab']) && $_REQUEST['tab'] != 'generate_feed_home' )) {
                    get_product_feed_view(); // display Product Feed URL and history page
                } else {

                    get_main_home_view(); // display Generate Product Feed page
                }
            }
            get_sa_footer();

            update_option('sa_step_progress', 2 ); // user is on step 2
        }
    }

    /**
     * displays the Thank you page survey menu HTML
     *
     * @return void
     */
    public function shopperapproved_thankyou_html()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $sa_settings_result = $this->check_settings();

        $sa_survey_status = get_option('sa_survey_status') ? get_option('sa_survey_status') : false;

        if (!get_option('sa_site_id') || !get_option('sa_survey_token')) {
            get_settings_view($sa_settings_result);
        } else {

            update_option('sa_step_progress', 3); // user is on step 3

            if (!empty($_GET['skip_feed'])) {
                // step 2B is completed successfully
                update_option('sa_step2_status', true );
                update_option('sa_step2b_status', true );
            }

            if (!empty($_POST['sa_survey_status'])) {

                if (get_option('sa_survey_status')) {
                    // step 3 is completed successfully
                    update_option('sa_step3_status', true);
                }

                // continue to Surveys Setting page
                wp_redirect(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_surveys_menu' ));
                exit;
            }

            get_thankyou_settings_view($sa_survey_status);
        }
        get_sa_footer();
    }

    /**
     * displays the Surveys settings page HTML
     *
     * @return void
     */
    public function shopperapproved_surveys_html()
    {
        // check if user is authenticated or not
        if (!current_user_can('manage_options')) {
            return;
        }
        $sa_settings_result = $this->check_settings();

        if (!get_option('sa_site_id') || !get_option('sa_survey_token')) {
            get_settings_view($sa_settings_result);
            get_sa_footer();
            exit;
        }

        update_option('sa_step_progress', 4 ); // user is on step 4

        $delivery_days = isset($_POST['sa_survey_delivery_days']) ? (int) $_POST['sa_survey_delivery_days'] : '';

        if (!empty($delivery_days)) {

            update_option('sa_days_to_delivery', $delivery_days);

            // step 4 is completed successfully
            update_option('sa_step4_status', true);

            // update days to send followup survey after transaction
            wp_remote_post( SHOPPER_APPROVED_APP_INTEGRATION_API . '/smart-surveys/activate?shipping_days='.$delivery_days, array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode(
                    array(
                        'site_id' => get_option('sa_site_id'),
                        'api_token' => get_option('sa_api_token'),
                        'days' => -1
                    )
                ) )); // -1 days is for Smart Survey Days as we don't want to update it at this point!

            // continue to Seals & Widgets
            wp_redirect(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgets_menu' ));
            exit;

        } else {

            // display Survey Setting page
            $days_to_delivery = get_option('sa_days_to_delivery') ? (int) get_option('sa_days_to_delivery') : 30; // default is 30 days
            get_survey_settings_view($days_to_delivery);
            get_sa_footer();
        }
    }

    /**
     * displays the Seals & Widgets page HTML
     *
     * @return false|void
     */
    public function shopperapproved_widgetmenu_html()
    {
        // check if user is authenticated or not

        if (!current_user_can('manage_options')) {
            return;
        }

        $sa_settings_result = $this->check_settings();
        if (!get_option('sa_site_id') || !get_option('sa_survey_token')) {
            get_settings_view($sa_settings_result);
            get_sa_footer();
            exit;
        }

        update_option('sa_step_progress', 5 ); // user is on step 5

        $data = [];

        // get Seal status related data
        $data['sa_seal_status'] = get_option('sa_seal_status') ? get_option('sa_seal_status') : false;
        $data['sa_seal_status2'] = get_option('sa_seal_status2') ? get_option('sa_seal_status2') : false;
        $data['sa_seal_excluded'] = get_option('sa_seal_excluded') ? get_option('sa_seal_excluded') : [];

        // get Review Page Widget related data
        $data['sa_rp_status'] = get_option('sa_rp_status') ? get_option('sa_rp_status') : false;
        $data['reviewpage_code'] = get_option('sa_review_page_code');

        // get Rotating Widget related data
        $data['sa_rotating_widget_status'] = get_option('sa_rotating_widget_status') ? get_option('sa_rotating_widget_status') : false;
        $data['sa_rotating_widget_code'] = get_option('sa_rotating_widget_code');

        // get Product Reviews Widgets status
        $data['sa_pwidgets_status'] = get_option('sa_pwidgets_status') ? get_option('sa_pwidgets_status') : false;

        // get Category Stars status
        $data['sa_cstars_status'] = get_option('sa_cstars_status') ? get_option('sa_cstars_status') : false;

        // continue to Congratulations page
        if ( isset($_POST['sa_continue_congrats']) ) {

            if ($data['sa_seal_status']  && $data['sa_seal_status2'] && $data['sa_rp_status'] && $data['sa_rotating_widget_status'] && $data['sa_pwidgets_status'] && $data['sa_cstars_status']) {
                // step 5 is completed successfully
                update_option('sa_step5_status', true);
            } else {
                update_option('sa_step5_status', false);
            }

            wp_redirect(esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_congrats_menu' , false) ));
            exit;
        }

        get_seals_and_widgets_view($data);
        get_sa_footer();
    }

    /**
     * displays the Congratulations page HTML
     *
     * @return void
     */
    public function shopperapproved_congrats_html()
    {
        // check if user is authenticated or not
        if (!current_user_can('manage_options')) {
            return;
        }
        $sa_settings_result = $this->check_settings();

        if (!get_option('sa_site_id') || !get_option('sa_survey_token')) {
            get_settings_view($sa_settings_result);

        } else {

            update_option('sa_step_progress', 6 ); // user is on step 6
            get_congratulations_view();
        }

        get_sa_footer();
    }

    /**
     * @return string
     */
    private function check_settings()
    {
        $sa_settingsupdate_result = '';
        if (!empty($_POST['sa_update_settings'])) {
            if ( is_numeric($_POST['sa_site_id']) && $_POST['sa_survey_token'] ) {
                if ($this->update_settings()) {
                    return $sa_settingsupdate_result;
                } else {
                    $sa_settingsupdate_result = '<b style="color:red">Invalid Credentials.</b>';
                }
            } else {
                $sa_settingsupdate_result = '<b style="color:red">Site id and API Token are required</b>';
            }
        }
        return $sa_settingsupdate_result;
    }

    /**
     * @param $site_id
     * @param $token
     *
     * @return void
     */
    private function update_thankyou_page_code($site_id, $token)
    {
        // getting the latest thank you page code
        $thankyou_code = "function saLoadScript(src) { var js = window.document.createElement('script'); js.src = src; js.type = 'text/javascript'; document.getElementsByTagName('head')[0].appendChild(js); } saLoadScript('https://www.shopperapproved.com/thankyou/rate/{$site_id}.js');";

        update_option('sa_thankyou_code', $thankyou_code);

        /*
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/thankyou-code/woocommerce/'.$site_id.'/'.$token;
        $response = wp_remote_get($request_url);
        if (!is_wp_error($response) and !empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $code = str_replace('</script>', '', $response['code']);
            update_option('sa_thankyou_code', $code);
        }*/
    }

    /**
     * gets the Review page code
     *
     * @param $site_id
     * @return void
     */
    private function update_review_page_code($site_id)
    {
        // getting the latest review page code
        $reviews_html = '<div id="SA_review_wrapper"></div>';
        $reviews_js = "var sa_interval = 5000;function saLoadScript(src) { var js = window.document.createElement('script'); js.src = src; js.type = 'text/javascript'; document.getElementsByTagName('head')[0].appendChild(js); } if (typeof(shopper_first) == 'undefined') saLoadScript('https://www.shopperapproved.com/widgets/{$site_id}/merchant/review-page/default.js'); shopper_first = true; ";
        $code = $reviews_html.'<script type="text/javascript">'.$reviews_js.'</script>';

        update_option('sa_review_page_code', $code);

        /* this gets the OLD review page code!
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/review-page-code/'.$site_id;
        $response = wp_remote_get($request_url);
        if(is_wp_error($response)) {
            return false;
        }
        elseif ($response['response']['code'] !== 200) {
            return false;
        }
        if (!empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $code = $response['html']."<script>".$response['js']."</script>";
            update_option('sa_review_page_code', $code);
        }
        */
    }

    /**
     * @param $site_id
     *
     * @return void
     */
    private function update_product_star_code($site_id)
    {
        // getting the latest product stars code
        $code = '<div id="product_just_stars" class="reg aside"></div>';

        update_option('sa_product_stars_code', $code);

        /*
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/product-stars-code/woocommerce/'.$site_id;
        $response = wp_remote_get($request_url);
        if (!is_wp_error($response) and $response['response']['code'] == 200 and !empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $code = $response['html']."<script>".$response['js']."</script>";
            update_option('sa_product_stars_code', $code);
        }
        */
    }

    /**
     * @param $site_id
     *
     * @return void
     */
    private function update_widget_code($site_id)
    {
        // getting the latest product widget code
        $widget_html = '<div id="SA_review_wrapper"></div>';
        $widget_js = "var sa_product = 'PRODUCT_ID'; var sa_interval = 5000;function saLoadScript(src) { var js = window.document.createElement('script'); js.src = src; js.type = 'text/javascript'; document.getElementsByTagName('head')[0].appendChild(js); } if (typeof(shopper_first) == 'undefined') saLoadScript('//www.shopperapproved.com/widgets/{$site_id}/product/'+sa_product+'/product-widget/default.js'); ";

        $code = $widget_html.'<script type="text/javascript">'.$widget_js.'</script>';
        update_option('sa_product_widget_code', $code);

        /*
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/product-widget-code/woocommerce/'.$site_id;
        $response = wp_remote_get($request_url);
        if (!is_wp_error($response) and $response['response']['code'] == 200 and !empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $code = $response['html']."<script>".$response['js']."</script>";
            update_option('sa_product_widget_code', $code);
        }*/
    }

    /**
     * @param $site_id
     *
     * @return void
     */
    private function update_rotating_widget_code($site_id)
    {
        // getting the latest rotating widget code
        $code = "<div id='SA_wrapper_default' class='SA__wrapper'></div><script type='text/javascript'>var sa_interval = 5000;function saLoadScript(src) { var js = window.document.createElement('script'); js.src = src; js.type = 'text/javascript'; document.getElementsByTagName('head')[0].appendChild(js); } if (typeof(shopper_first) == 'undefined') saLoadScript('https://www.shopperapproved.com/widgets/{$site_id}/merchant/rotating-widget/default.js?v=1'); </script>";

        update_option('sa_rotating_widget_code', $code);
    }

    /**
     * @param $site_id
     *
     * @return void
     */
    private function update_category_code($site_id)
    {
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/category-code/woocommerce/'.$site_id;
        $response = wp_remote_get($request_url);
        if (!is_wp_error($response) and $response['response']['code'] == 200 and !empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $html = $response['html'];

            $js = "<script>"
                . $response['js'] .
                "</script>";
            update_option('sa_category_code', $html);
            update_option('sa_category_script', $js);
        }
    }

    /**
     * @param $site_id
     * @param $site_name
     * @return void
     */
    private function update_seal_code($site_id, $site_name)
    {
        // getting the latest seal code

        $html= '<a href="https://www.shopperapproved.com/reviews/'.$site_name.'" class="shopperlink new-sa-seals placement-default"><img src="//www.shopperapproved.com/seal/'.$site_id.'/default-sa-seal.gif" style="border-radius: 4px;" alt="Customer Reviews" oncontextmenu="'."var d = new Date(); alert('Copying Prohibited by Law - This image and all included logos are copyrighted by Shopper Approved \\251 '+d.getFullYear()+'.'); return false;".'" /></a>';

        $var = 'var js = window.document.createElement("script"); js.innerHTML = '."'";
        $inner = 'function openshopperapproved(o){ var e="Microsoft Internet Explorer"!=navigator.appName?"yes":"no",n=screen.availHeight-90,r=940;return window.innerWidth<1400&&(r=620),window.open(this.href,"shopperapproved","location="+e+",scrollbars=yes,width="+r+",height="+n+",menubar=no,toolbar=no"),o.stopPropagation&&o.stopPropagation(),!1}!function(){for(var o=document.getElementsByClassName("shopperlink"),e=0,n=o.length;e<n;e++)o[e].onclick=openshopperapproved}()'.";'; ";

        $remaining = 'js.type = "text/javascript"; document.getElementsByTagName("head")[0].appendChild(js);var link = document.createElement("link");link.rel = "stylesheet";link.type = "text/css";link.href = "//www.shopperapproved.com/seal/default.css";document.getElementsByTagName("head")[0].appendChild(link);';
        $script = $var.$inner.$remaining;

        $code = $html."<script type='text/javascript'> (function() { " .$script. "})();</script>";

        update_option('sa_seal_code', $code);

        /*
        $request_url = SHOPPER_APPROVED_APP_INTEGRATION_API . '/seal-code/'.$site_id.'/'.$token;
        $response = wp_remote_get($request_url);
        if (!is_wp_error($response) and $response['response']['code'] == 200 and !empty($response['body'])) {
            $response = json_decode($response['body'], true);
            $code = $response['html']."<script>".$response['js']."</script>";
            update_option('sa_seal_code', $code);
        }
        */
    }

    /**
     * This function is used to verify the user's credentials (Site Id and Api Token) on Login page
     *
     * @return bool
     */
    private function update_settings()
    {
        $site_id = sanitize_text_field($_POST['sa_site_id']);
        $api_token = $_POST['sa_survey_token'];
        $token = sanitize_text_field($api_token);

        if ( ! isset( $_POST['sa_domain'] ) && is_numeric($site_id)) {
            $response = wp_remote_post( SHOPPER_APPROVED_APP_INTEGRATION_API . '/app-login', array(
                'method' => 'POST',
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode(array(
                        'site' => $site_id,
                        'token' => $token
                    )
                ) ));

            if(is_wp_error($response)) {
                return false;
            }
            elseif ($response['response']['code'] !== 200) {
                return false;
            }
            $survey_token = json_decode( $response['body'] )->initial_token;
            update_option( 'sa_survey_token', $survey_token );
            $domain = json_decode( $response['body'] )->initial_token;

        } else {
            $domain = sanitize_url($_POST['sa_domain']);
        }

        if(is_numeric($site_id)){

            $this->update_product_star_code($site_id);
            $this->update_widget_code($site_id);
            $this->update_review_page_code($site_id);
            $this->update_thankyou_page_code($site_id, $token);
            $this->update_category_code($site_id);

            $this->update_rotating_widget_code($site_id);
            $today = date('F j, Y  g:i a');
            setcookie('sa_code_update', $today, time()+1815603);
            update_option('sa_site_id', $site_id );
            update_option('sa_api_token', $token );
            update_option('sa_domain', $domain );

            // at this point user is verified so save token in ClientSecretKeys DB table on ShopperApproved (to show "Active" on SA App Marketploce page)
            $resp = use_shopperapproved_api('configure');

            $site_name = '';
            if (!empty(json_decode( $resp['body'])->data)) {

                // getting site name for seal
                $site_name = json_decode( $resp['body'])->data->domainName;
            }
            $this->update_seal_code($site_id, $site_name);

            // step 1 is completed successfully
            update_option('sa_step1_status', true );
        }

        return true;
    }

    /**
     * this function is used to add or remove Reviews page widget according to setting
     *
     * @return void
     */
    public function handle_review_page_widget($addPage)
    {
        if ( $addPage ) {
            if ($this->check_if_review_page_exists()) {

                // Reviews Page already exists so no need to create
                update_option('rp_already_exists', true);
            } else {
                update_option('rp_already_exists', false);
                if ($this->create_review_page()) {
                    // Review Page Successfully Added
                } else {
                    // Unable to create reviews page
                }
            }
        } else {

            if (!get_option('rp_already_exists')) { // do not remove Review page if it already existed before
                if ($this->delete_review_page()) {
                    // Review page removed
                } else {
                    // Unable to remove review page
                }
            }
        }
    }

    /**
     * this function is used to check for existence of Reviews page
     *
     * @return bool
     */
    private function check_if_review_page_exists(): bool
    {
        $page = get_page_by_path('reviews'); // get page with slug 'reviews'
        if ($page) {

            return true;
        } else {
            return false;
        }
    }

    /**
     * this function is used to add the Reviews Page
     *
     * @return bool
     */
    private function create_review_page(): bool
    {
        try {
            $postType    = 'page'; // set to post or page
            $userID      = get_current_user_id(); // set to user id
            $postStatus  = 'publish';  // set to future, draft, or publish
            $leadTitle   = 'Reviews';
            $leadContent = $this->reviewpage_code;
            $timeStamp   = date( 'Y-m-d H:i:s', time() ); // format needed for WordPress
            $new_page = array(
                'post_title'   => $leadTitle,
                'post_content' => $leadContent,
                'post_status'  => $postStatus,
                'post_date'    => $timeStamp,
                'post_author'  => $userID,
                'post_type'    => $postType
            );
            $page_id = wp_insert_post( $new_page );
            if ( $page_id ) {
                return true;
            } else {
                return false;
            }
        } catch(Exception $exception) {
            return false;
        }
    }

    /**
     * this function is used to remove the Reviews Page
     *
     * @return bool
     */
    private function delete_review_page(): bool
    {
        // reference: https://medium.com/@online-web-tutor/how-to-remove-page-using-slug-in-wordpress-tutorial-9fdabc674abe#:~:text=function%20remove_page_by_slug()%20%7B%20%24page_slug%20%3D,'Page%20with%20slug%20%22'%20.

        $page = get_page_by_path('reviews'); // get page with slug 'reviews'
        if ($page) { // Delete the page
            wp_delete_post($page->ID, true);
            // Page has been deleted
            return true;
        } else {
            // Page not found
            return false;
        }
    }
}