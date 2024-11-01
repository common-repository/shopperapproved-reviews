<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    Shopper_Approved
 * @subpackage Shopper_Approved/admin/partials
 */

/**
 * This function is used to handle step navigation (on left side) and add/remove "active" and "fill" class
 * This will tell the user which step they are at
 *
 * @param int $step_num
 * @return void
 */
function get_left_side_menu(int $step_num = 0) {

    $sa_progress = !empty(get_option( 'sa_step_progress' )) ? (int) get_option( 'sa_step_progress' ) : 0;

    $plugin_pages = [];
    $plugin_pages[] = $product_feed = esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false ));
    $plugin_pages[] = $thank_you = esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu', false ));
    $plugin_pages[] = $surveys = esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_surveys_menu', false ));
    $plugin_pages[] = $seal_widgets = esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgets_menu', false ));
    $plugin_pages[] = $congratulations = esc_url( menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_congrats_menu', false ));

    // check if user has clicked from outside plugin on main SA plugin button
    $referer = wp_get_referer();
    $referer = strtok($referer, '&'); //get string before first occurence of & (reference: https://stackoverflow.com/questions/6969645/how-to-remove-the-querystring-and-get-only-the-url)

    if ($step_num == 2 && ! in_array($referer , $plugin_pages) && $sa_progress > 2) {

        wp_safe_redirect( $plugin_pages[$sa_progress - 2] ); // skip to step ahead
    }

    ?>

    <div class="sa-app-menu">
        <div class="sa-app-logo">
            <img src="<?php echo SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/sa-logo.svg';?>" alt="" />
        </div>
        <ul class="sidebar-selection merchant-product d-block" id="sa-merchant">

            <?php

            $step1_status = get_option( 'sa_step1_status');

            $step_status = '';
            $failed_class = '';

            if ($step_num > 0) {
                if ($step_num == 1) $step_status = 'active'; else if ($step1_status) $step_status = 'fill';

                if (empty($step_status)) $failed_class = 'sa-product-feed faild-feed';
            }
            ?>

            <li class="<?php echo $failed_class;?>">
                <a href="<?php echo $product_feed . '&action=login'; ?>" class="<?php echo $step_status;?>">Setup</a>
            </li>

            <?php

            $step2_status = get_option('sa_step2_status');

            $step_status = '';
            $failed_class = '';

            if ($step_num > 1) {
                if ($step_num == 2) $step_status = 'active'; else if ($step2_status) $step_status = 'fill';

                if (empty($step_status)) $failed_class = 'sa-product-feed faild-feed';
            }
            ?>

            <li class="<?php echo $failed_class;?>">
                <a href="<?php echo $product_feed; ?>" class="<?php echo $step_status;?>">Product Feed</a>
            </li>

            <?php

            $step3_status = get_option('sa_step3_status');

            $step_status = '';
            $failed_class = '';

            if ($step_num > 2) {
                if ($step_num == 3) $step_status = 'active'; else if ($step3_status) $step_status = 'fill';

                if (empty($step_status)) $failed_class = 'sa-product-feed faild-feed';
            }
            ?>

            <li class="<?php echo $failed_class;?>">
                <a href="<?php echo $thank_you; ?>" class="<?php echo $step_status;?>">Thank You Page Survey</a>
            </li>

            <?php

            $step4_status = get_option('sa_step4_status');

            $step_status = '';
            $failed_class = '';

            if ($step_num > 3) {
                if ($step_num == 4) $step_status = 'active'; else if ($step4_status) $step_status = 'fill';

                if (empty($step_status)) $failed_class = 'sa-product-feed faild-feed';
            }
            ?>

            <li class="<?php echo $failed_class;?>">
                <a href="<?php echo $surveys; ?>" class="<?php echo $step_status;?>">Surveys</a>
            </li>

            <?php

            $step5_status = get_option('sa_step5_status');

            $step_status = '';
            $failed_class = '';
            if ($step_num > 4) {
                if ($step_num == 5) $step_status = 'active'; else if ($step5_status) $step_status = 'fill';

                if (empty($step_status)) $failed_class = 'sa-product-feed faild-feed';
            }
            ?>

            <li class="<?php echo $failed_class;?>">
                <a href="<?php echo $seal_widgets; ?>" class="<?php echo $step_status;?>">Seals & Widgets</a>
            </li>

            <?php

            $step6_status = get_option('sa_step6_status');

            $step_status = '';
            if ($step_num > 5){
                if ($step_num == 6) $step_status = 'active'; else if ($step6_status) $step_status = 'fill';
            }
            ?>

            <li>
                <a href="<?php echo $congratulations; ?>" class="<?php echo $step_status;?>">Confirmation</a>
            </li>

            <li class="d-none">
                <a href="#" class="<?php echo $step_status;?>">Confirmation Feed</a>
            </li>
        </ul>
    </div>
    <div id="sa-loader-container"></div>
    <?php
}

/**
 * This function is used to add whatever you like to the footer e.g. Javascript for Intercom functionality
 *
 * @return void
 */
function get_sa_footer()
{
    ?>
    <div id="snackbar"></div>
    <script>
        window.intercomSettings = {
            "app_id": "taxyni8e",
            "email": "<?php echo wp_get_current_user()->data->user_email; ?>"
        };
    </script>

    <script>
        (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/taxyni8e';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
    </script>

    <?php
}

/**
 * This function is used to display the Thank You Page Survey
 *
 * @param $sa_survey_status
 *
 * @return void
 */
function get_thankyou_settings_view( $sa_survey_status )
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';

    $previous_feed_step = esc_url(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false));

    if ( empty(get_option('sa_step2b_status')) ) {
        $previous_feed_step = $previous_feed_step . '&tab=show_feed_history';
    }

    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(3); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-2" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img; ?>" alt="" />
                    </div>
                    <div class="pt-36 pb-24 mb-1">
                        <h3 class="sa-primary-blue">Thank You Page Survey</h3>
                        <h4>Install the Thank You Page Survey Code:</h4>
                        <p class="f14 mb-0">When the Thank You Page Survey is enabled, a survey will pop up after checkout on the order confirmation page. The customer can leave a rating and a review of their initial experience.</p>
                    </div>
                    <div class="sa-alert-custom mb-24 sa-red-bar" id="sa_alertBox">
                        <img src="<?php echo $info_img; ?>" alt="" />
                        <p>
                            Thank you page code <span class="sa-alert-text">enabled</span>
                        </p>
                    </div>

                    <form method="post" action="">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="f18">
                                    Enable Thank You Page Survey
                                </label>
                                <p class="f14">Be sure to place a test order to make sure the survey is working.</p>
                            </div>
                            <div id="sa_thanku_survay">
                                <div class="custom-switch default-switch normal" >

                                    <input type="checkbox" <?php if ( !empty( $sa_survey_status ) && $sa_survey_status == true ) echo 'checked'; ?> id="sa_survey_status" class="switch-input review-page-switch" name="sa_survey_status" value="sa_survey_status">
                                    <label for="sa_survey_status" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="sa-note-custom mt-32 mb-0">
                            <img src="<?php echo $info_img;?>" alt="" />
                            <div>
                                <p>
                                    <span>Note:</span>
                                    Enabling this script will begin sharing your customer data with Shopper Approved for the purpose of collecting reviews.
                                </p>
                            </div>
                        </div>
                        <div class="sa-note-custom mt-32 mb-0 p-24">
                            <div class="w-100">
                                <h4 class="mb-16 light-gray-400">The data collected is as follows:</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered sa-data-type-table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Data Type</th>
                                            <th scope="col">Reason</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Customer Name</td>
                                            <td>To be able to address the customer by their name and also used in displaying the review on your site</td>
                                        </tr>
                                        <tr>
                                            <td>Customer Email</td>
                                            <td>To be able to send the order fulfillment follow-up survey</td>
                                        </tr>
                                        <tr>
                                            <td>Order Id</td>
                                            <td>To allow you to understand which order a review is about, especially in situations where a customer opts not to publicly disclose their name</td>
                                        </tr>
                                        <tr>
                                            <td>State/Providence</td>
                                            <td>To add relevance to review display</td>
                                        </tr>
                                        <tr>
                                            <td>Country</td>
                                            <td>To add relevance to review display</td>
                                        </tr>
                                        <tr>
                                            <td>Products Purchased <br>(Product Name & Product ID)</td>
                                            <td>Used to allow the survey to ask the customer about the products they purchased.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="f14">
                                    This data will be kept and protected as described in our <a href="https://results.shopperapproved.com/privacy" target="_blank" class="btn-link fw-400">Privacy Policy</a>. You may need to make adjustments to your own privacy policy to denote this collection.
                                </p>
                            </div>
                        </div>
                        <div class="pt-36">
                            <hr class="mt-0">
                        </div>
                        <div class="sa-app-buttons">
                            <a href="<?php echo $previous_feed_step; ?>" class="btn-cancel">Previous
                                Step</a>
                            <button onclick="toggleLoading()" type="submit" class="sa-btn-primary" id="sa_continue_survey" <?php if ( empty( $sa_survey_status ) || $sa_survey_status == false ) echo 'disabled'; ?>>Continue to Survey Settings</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <?php
}

/**
 * This function is used to display the Login page where user is asked to enter their Site ID and API token
 *
 * @param string $sa_settings_result
 *
 * @return void
 */
function get_settings_view( string $sa_settings_result = '' )
{
    /**
     * Check if WooCommerce is active
     **/
    if (!
    in_array(
        'woocommerce/woocommerce.php',
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
    )
    ) {
        echo '<div class="error"><p>Woocommerce plugin not installed/active. For installation and activation <a href="'.admin_url('plugins.php').'">click here</a>.</p></div>';
        exit;
    }

    $action = !empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

    if ( $action == 'login' ):

        update_option('sa_step_progress', 1 ); // user is on step 1

        $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
        $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';
        ?>

        <div class="sa-step-app">

            <?php get_left_side_menu(1); ?>

            <div class="sa-app-steps">
                <div class="sa-steps-content d-block">
                    <div class="sa-app-step" id="step-0" style="display: block;">
                        <div class="sa-inner-box mt-50">
                            <form method="post" class="" action="">
                                <div class="sa-woocommerce-login-form">

                                    <div class="text-center">
                                        <img src="<?php echo $woo_img; ?>" alt="">
                                        <h2>
                                            Please enter your Shopper Approved Site Id and Token
                                        </h2>
                                    </div>

                                    <?php if ( $sa_settings_result ) { ?>
                                        <div class="text-center">
                                            <h4>
                                                <?php echo wp_kses_post( $sa_settings_result ); ?>
                                            </h4>
                                        </div>
                                    <?php } ?>

                                    <div class="sa-input sa-user">
                                        <label>Site ID</label>
                                        <input type="text" class="sa-form-control login_input" id="site_id" name="sa_site_id" >
                                    </div>
                                    <div class="sa-input sa-token">
                                        <label>API Token</label>
                                        <input type="text" class="sa-form-control login_input" id="api_token" name="sa_survey_token">
                                    </div>
                                    <button onclick="showSpinner()" type="submit" class="sa-btn-primary sa-disable-btn" id="sa_btn_submit" disabled="" name="sa_update_settings" value="Continue">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" class="sa-spinner" id="sa_spinner" style="display: none;">
                                            <path d="M12.0195 2.54297V6.54297M12.0195 18.543V22.543M6.01953 12.543H2.01953M22.0195 12.543H18.0195M19.098 19.6214L16.2695 16.793M19.098 5.54291L16.2695 8.37134M4.9411 19.6214L7.76953 16.793M4.9411 5.54291L7.76953 8.37134" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        Continue
                                    </button>

                                    <div class="sa-note-custom mb-0">
                                        <img src="<?php echo $info_img; ?>" alt="">
                                        <p>
                                            <span>Note:</span>
                                            To find your Site ID and API token, log in to your <a href="https://www.shopperapproved.com/account" target="_blank">Shopper Approved account</a>.
                                            Once you have logged in, click on your name in the upper right hand corner, Choose <a href="JavaScript:void(0);" class="sa-trigger">Settings</a>,
                                            and retrieve your <b>Site ID and API token</b> and enter those in the fields above.
                                            <br>
                                            <b>View our full <a href="https://help.shopperapproved.com/en/articles/8775779-woocommerce-installation-guide" class="sa-underline" target="_blank">Woo Commerce Installation Guide</a>.</b>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    else:
        get_registration_view();
    endif;

    walk_modal();
}

/**
 * This function is used to show the Index page which shows the user 2 options Create account or Login
 *
 * @return void
 */
function get_registration_view()
{
    $iconImgUrl = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woo-icon.png';
    $gearImgUrl = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/gear-setup.svg';

    ?>
    <div class="sa-woocommerce-wrap">
        <div class="app-marketplace-banner">
            <div class="">
                <h1 class="poppins">
                    <span class="sa-primary-blue">Shopper Approved</span><br class="sa-d-none">
                    <span class="sa-primary-orange">+ <span class="appM-name">WooCommerce</span></span>
                </h1>
                <p class="f16 mw-60">
                    Welcome to Shopper Approved! We'll have you collecting verified reviews in no time! Let's get you set up!
                </p>
            </div>
            <div class="">
                <img src="<?php echo $iconImgUrl; ?>" alt="Shopper Approved &amp; WooCommerce Connect">
            </div>
        </div>
        <div id="woocommerce-mbody-form">
            <div class="box-custom-new">
                <div class="sa-lets-start">
                    <h4 class="mb-0">Let's get started <br>collecting reviews! </h4>
                    <img src="<?php echo $gearImgUrl; ?>" alt="" />
                </div>
                <div>
                    <p>
                        This step-by-step guide will take you the through setup process,
                        including account configuration, settings, and collecting and displaying high
                        quality Ratings and Reviews on your WooCommerce website.
                    </p>
                </div>
                <hr class="sa-line-break">
                <div class="m-auto">
                    <a class="sa-btn-primary" target="_blank" href="https://results.shopperapproved.com/freetrial-woo">Create Account</a>
                </div>
                <p class="f18 m-auto">Already have an account? <a href="<?php echo esc_url( menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false ) . '&action=login' ); ?>">Login</a></p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * This function is used to display the Instructions modal on Login page / shows popup to guide user
 *
 * @return void
 */
function walk_modal()
{
    $step1ImgUrl = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/account-setting.jpg';
    $step2ImgUrl = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/site-id-token.jpg';

    ?>
    <div class="sa-modal">
        <div class="sa-modal-content">
                <span class="sa-close-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                        <path d="M15 5.54297L5 15.543M5 5.54297L15 15.543" stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            <h4>1. Shopper Approved Account Settings Page</h4>
            <div class="pt-16 pb-16">
                <img src="<?php echo $step1ImgUrl; ?>" alt="" />
            </div>
            <h4>2. Shopper Approved Site ID and API Token</h4>
            <div class="pt-16">
                <img src="<?php echo $step2ImgUrl; ?>" alt="" />
            </div>
        </div>
    </div>
    <?php
}

/**
 * This function is used to display the initial step of Product Feed page (before clicking Generate Product Feed button)
 *
 * @return void
 */
function get_main_home_view()
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';
    $feed_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/product-feed-icon.svg';

    $product_feed = esc_url(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false) . '&tab=generate_feed_home');
    $thank_you = esc_url(menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu', false) . '&skip_feed=1');

    $gtin_options = ['none' => 'GTIN Not Used',
        'id' => 'Product ID',
        'sku' => 'SKU',
        'name' => 'Product Name',
        'slug' => 'Slug',
        'date_created' => 'Date',
        'status' => 'Status',
        'description' => 'Description',
        'short_description' => 'Short Description',
        'price' => 'Price'];

    $gtin_text = !empty(get_option('sa_feed_gtin')) ? $gtin_options[get_option('sa_feed_gtin')] : 'Select an attribute';

    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(2); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img;?>" alt="">
                    </div>
                    <div class="sa-feed sa-feed-step1" style="display: block;">
                        <div class="d-flex align-items-center justify-content-between pt-36">
                            <div>
                                <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                                <h4 class="mb-0 mw-auto">A product feed consists of your entire product collection.</h4>
                            </div>
                            <div>
                                <img src="<?php echo $feed_img; ?>" alt="">
                            </div>
                        </div>
                        <p class="pt-24 mb-0">
                            In order for us to collect product reviews from your customers, we need to know what products you sell so we can attach the correct products at checkout, associate the customer product review to the correct product, and display your product reviews on your website. To set this up, we need to generate a product feed. The product feed will include unique identifiers such as product name, product ID, and product URL - which helps us differentiate products from each other.
                        </p>

                        <div class="sa-feed-step1a">
                            <div class="pt-24 pb-24">
                                <div class="sa-google-listing">
                                    <h4>Do you use the "Google Listings & Ads" plugin on your Woo Commerce site to send your product data to Google Merchant Center?</h4>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="sa-app-buttons">

                                <!--<a href="<?php echo $product_feed; ?>" class="btn-cancel feedstep-back">Previous Step</a>-->
                                <a href="<?php echo esc_url( menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false ) . '&action=login-again' ); ?>" class="btn-cancel">Previous Step</a>

                                <div class="d-flex align-items-center justify-content-between sa-yes-no-woo-btn">
                                    <button type="button" class="sa-btn-primary create-feed-btn" onclick="showYesStep()">
                                        Yes
                                    </button>

                                    <button type="button" class="sa-btn-primary" onclick="showNoStep()">
                                        No
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="sa-feed-step2a" style="display: none;">

                            <div class="pt-24">
                                <div class="sa-product-select">
                                    <h4>GTIN Product Attribute Selection</h4>
                                    <p>Before generating your Product Feed, please select which product attribute you are using as your Product GTIN in Woo Commerce. If you aren't using a GTIN attribute, select "GTIN Not Used"</p>
                                </div>
                            </div>

                            <div class="pt-24 pb-24 d-flex align-items-center sa-set-gtin">
                                <div class="mw-40" style="width: 100%;">
                                    <select id="sa_feed_gtin_attribute" name="sa_feed_gtin_attribute" class="sa-custom-select">
                                        <option value="" data-display-text="<?php echo $gtin_text;?>">Select an attribute</option>

                                        <?php

                                        foreach ($gtin_options as $key => $gtin_option) {
                                            $selected = ($gtin_text == $gtin_option) ? 'selected' : '';
                                            ?>
                                            <option <?php echo $selected;?> value="<?php echo $key;?>"><?php echo $gtin_option;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="button" class="sa-btn-primary" id="gtin_button">Set GTIN Attribute</button>
                            </div>

                            <div class="pt-24 pb-24">
                                <div class="sa-note-custom mb-0">
                                    <img src="<?php echo $info_img; ?>" alt="">
                                    <p>
                                        <span>Note:</span>
                                        Click the <strong>Generate Product Feed</strong>
                                        button to begin the process of creating your Product Feed.
                                    </p>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="sa-app-buttons">

                                <button type="button" class="btn-cancel" onclick="stepBack()">Previous Step</button>

                                <form method="post" action="">

                                    <input type="hidden" name="tab" value="generate_feed_download">
                                    <button <?php if (empty(get_option('sa_feed_gtin'))) echo 'disabled'; ?> id="generate_feed" name="generate_feed" type="submit" class="sa-btn-primary create-feed-btn" onclick="showFeedProgressBar()">Generate Product Feed</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    <div class="sa-feed sa-feed-step2" style="display: none;">
                        <div class="pt-36 pb-36">
                            <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                            <h4>Great news! Your product feed is currently processing.</h4>
                        </div>

                        <div class="sa-progress-container sa-complete-feed">
                            <div class="sa-counter-wrap"><span class="sa-counter"></span> %</div>
                            <div class="sa-counter-bar">
                                <div class="sa-counter-progress"></div>
                            </div>
                        </div>
                        <div class="sa-app-buttons">
                            <a href="<?php echo esc_url(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false) . '&tab=generate_feed_home' ); ?>" class="btn-cancel feedstep-back">Previous Step</a>
                        </div>
                    </div>

                    <div class="sa-feed sa-feed-step5">
                        <div class="d-flex align-items-center justify-content-between pt-36">
                            <div>
                                <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                                <h4 class="mb-0 mw-auto">A product feed consists of your entire product collection.</h4>
                            </div>
                            <div>
                                <img src="<?php echo $feed_img; ?>" alt="" />
                            </div>
                        </div>
                        <p class="pt-24 mb-0">
                            In order for us to collect product reviews from your customers, we need to know what products
                            you sell so we can attach the correct products at checkout, associate the customer product review
                            to the correct product, and display your product reviews on your website. To set this up,
                            we need to generate a product feed. The product feed will include unique identifiers such as
                            product name, product ID, and product URL - which helps us differentiate products from each other.
                        </p>
                        <div class="pt-24 pb-24">
                            <div class="sa-google-listing">
                                <h4>You likely require a custom feed.</h4>
                                <h4>Please reach out to our client support team for assistance: <a href="mailto:support@shopperapproved.com">support@shopperapproved.com</a> with a Subject Line of "Custom Woo Commerce Product Feed request".</h4>
                            </div>
                        </div>
                        <hr class="mt-0">
                        <div class="sa-app-buttons">
                            <button type="button" class="btn-cancel " onclick="stepBack()">Previous Step</button>
                            <a href="<?php echo $thank_you; ?>" class="sa-btn-primary sa-skip-thank">Skip &amp; Continue to Thank You Page Survey</a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
}

/**
 * This function is used to display the progress bar step of Product Feed page (after clicking Generate Product Feed button)
 *
 * @param int $success
 * @return void
 */
function get_feed_progress_view(int $success = 1)
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';

    $product_feed = esc_url(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false) . '&tab=generate_feed_home');
    $thank_you = esc_url(menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu', false));
    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(2); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-1" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img;?>" alt="">
                    </div>
                    <div class="sa-feed sa-feed-step2" style="display: block;">
                        <div class="pt-36 pb-36">
                            <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                            <h4>Great news! Your product feed is currently processing.</h4>
                        </div>
                        <?php if ($success == 1) { ?>

                            <div class="sa-progress-container sa-complete-feed">
                                <div class="sa-counter-wrap"><span class="sa-counter">100</span> %</div>
                                <div class="sa-counter-bar">
                                    <div class="sa-counter-progress" style="width: 100%;"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="sa-app-buttons">
                                <a href="<?php echo $product_feed . '&tab=generate_feed_home'; ?>" class="btn-cancel feedstep-back">Previous Step</a>
                                <a href="<?php echo $product_feed . '&tab=show_feed_history'; ?>" class="sa-btn-primary create-feed-btn">Continue to Product Feed URL</a>
                            </div>

                        <?php } else { ?>
                            <div class="sa-note-custom danger sa-rescan-feed d-flex" id="sa_rescan">
                                <div class="d-flex gap-2 align-items-start">
                                    <img src="<?php echo $info_img; ?>" alt="">
                                    <p class="mw-50">
                                        An error has been detected while pulling your feed. Our support team has been notified and will reach out to you.
                                    </p>
                                </div>
                                <div>
                                    <form method="post" action="">

                                        <input type="hidden" name="tab" value="generate_feed_download">
                                        <button name="generate_feed" type="submit" class="btn btn-outline-primary border-2" onclick="showFeedProgressBar()">Re-scan Feed</button>
                                    </form>

                                </div>
                            </div>
                            <div class="sa-progress-container sa-faild-feed sa-complete-feed">
                                <div class="sa-counter-wrap"><span class="sa-counter">70</span> %</div>
                                <div class="sa-counter-bar">
                                    <div class="sa-counter-progress" style="width: 70%;"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="sa-app-buttons">
                                <a href="<?php echo $product_feed; ?>" class="btn-cancel feedstep-back">Previous Step</a>
                                <a href="<?php echo $thank_you; ?>" class="sa-btn-primary sa-skip-thank">Skip &amp; Continue to Thank You Page Survey</a>
                            </div>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
}

/**
 * This function is used to show the last step of Product Feed page (after Product Feed is successfully generated) along with Product Feed History
 *
 * @return void
 */
function get_product_feed_view()
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';

    $product_feed = esc_url(menu_page_url( SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu', false) . '&tab=generate_feed_home');
    $thank_you = esc_url(menu_page_url(SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu', false));
    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(2); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-1" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img;?>" alt="">
                    </div>

                    <div class="sa-feed sa-feed-step3" style="display: block;">
                        <div class="pt-36">
                            <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                            <h4>Your Product Feed has been sent to our support team!</h4>
                        </div>

                        <?php show_product_feed_history(); ?>

                        <hr>
                        <div class="sa-app-buttons">
                            <a href="<?php echo $product_feed; ?>" class="btn-cancel feedstep-back">Previous Step</a>
                            <a href="<?php echo $thank_you; ?>" class="sa-btn-primary">Continue to Thank You Page Survey</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

/**
 * display the Product Feed History part
 *
 * @return void
 */
function show_product_feed_history()
{
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';
    $copy_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/content_copy.svg';
    $download_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/file_download.svg';

    $csv_file =  esc_url(Shopper_Approved_Products::get_csv_url());
    ?>

    <div class="sa-note-custom mt-24 mb-0">
        <img src="<?php echo $info_img; ?>" alt="">
        <p>
            <span>Note:</span>
            Please allow up to 48 hours for the feed to be available in our system. You can download the feed for your records using the link below.
        </p>
    </div>
    <div class="pt-36">
        <h5 class="mb-2 f18 roboto">Product Feed URL</h5>
        <div class="sa-input-group sa-feed-url">
            <input readonly id="csv-text" onclick="copyText('csv-text')" type="text" value="<?php echo $csv_file; ?>" class="sa-form-control">
            <button onclick="copyText('csv-text')" class="btn btn-outline-secondary" type="button" id="button-addon2" fdprocessedid="m093zk"><img src="<?php echo $copy_img; ?>" alt=""></button>
        </div>

        <div class="pt-24">
            <a href="<?php echo $csv_file; ?>" id="sadowncsv" class="f14 f700">
                <img id="downloadIcon" src="<?php echo $download_img; ?>" alt="" class="align-center"> Download CSV
            </a>

        </div>
    </div>
    <div class="pt-36">
        <div id="prod-history">
            <h5 class="mb-0 pb-20 f18 roboto">View Product Feed History</h5>
            <table class="table sa-feed-history mb-5">
                <thead>
                <tr class="border-top">
                    <th scope="col">Feed Batch #</th>
                    <th scope="col">Total Products</th>
                    <th scope="col">Feed Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach( Shopper_Approved_Products::get_history( true ) as $record ) : $record = (object) $record; ?>
                    <tr>
                        <td><?php echo $record->number; ?></td>
                        <td><?php echo $record->products_count; ?></td>
                        <td><?php echo $record->feed_date; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

/**
 * This function is used to show the Surveys Settings page
 *
 * @param int $days_to_delivery
 * @return void
 */
function get_survey_settings_view(int $days_to_delivery)
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';

    $thank_you = SHOPPER_APPROVED_PLUGIN_PREFIX.'_thankyou_menu';

    if ($days_to_delivery == 1) {
        $days_text = '1 Day';
    } else {
        $days_text = $days_to_delivery.' Days';
    }

    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(4); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-3" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img;?>" alt="" />
                    </div>
                    <div class="pt-36">
                        <h3 class="sa-primary-blue">Survey Settings</h3>
                        <h4>Adjust survey preferences and configurations with survey settings.</h4>
                        <p class="f14 mb-0 pt-2">
                            Set the number of days after a transaction on your site occurs that you would like Shopper Approved to send a follow-up "Order Fulfillment" survey. It is recommended to set this to a value that ensures most customers will have received their orders before receiving the survey.
                        </p>
                    </div>

                    <form method="post" action="">
                        <div class="mw-40 mt-3">
                            <select id="sa_survey_delivery_days" name="sa_survey_delivery_days" class="sa-custom-select">
                                <option value="" data-display-text="<?php echo $days_text;?>">None</option>

                                <?php for ($i = 1; $i <= 85; $i++) {

                                    $selected = ($days_to_delivery == $i) ? 'selected' : '';

                                    if ($i == 1) { ?>
                                        <option <?php echo $selected; ?> value="1">1 Day</option>
                                    <?php } else { ?>
                                        <option <?php echo $selected; ?> value="<?php echo $i;?>"><?php echo $i;?> Days</option>
                                    <?php }
                                }?>
                            </select>
                        </div>
                        <div class="sa-note-custom mt-36 mb-36">
                            <img src="<?php echo $info_img; ?>" alt="" />
                            <p>
                                <span>Note:</span>
                                If you are using a third-party such as Klaviyo, Attentive, or Postscript to send your customers the post purchase survey, those settings will be controlled in those apps rather than within Shopper Approved.
                            </p>
                        </div>

                        <div><hr> </div>
                        <div class="sa-app-buttons">
                            <a href="<?php echo esc_url( menu_page_url( $thank_you , false) ); ?>" class="btn-cancel">Previous Step</a>
                            <button onclick="toggleLoading()" type="submit" class="sa-btn-primary">Continue to Seals & Widgets</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
}

/**
 * This function is used to show the Seals & Widgets page
 *
 * @param array $data
 *
 * @return void
 */
function get_seals_and_widgets_view( array $data = [] )
{
    $seal_status = $data['sa_seal_status'];
    $seal_status2 = $data['sa_seal_status2'];
    $seal_excluded =  $data['sa_seal_excluded'];

    $review_page_status = $data['sa_rp_status'];
    $product_widget_status = $data['sa_pwidgets_status'];
    $rotating_widget_status = $data['sa_rotating_widget_status'];
    $category_stars_status = $data['sa_cstars_status'];

    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';
    $seals_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/seals-widgets-icon.svg';
    $question_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/ques-circle.svg';

    $preview_img1 = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/seals-widgets-preview.jpg';
    $preview_img2 = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/install-review.jpg';
    $preview_img3 = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/rotating-widgets.jpg';
    $preview_img4 = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/install-product.jpg';
    $preview_img5 = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/install-category.jpg';

    $surveys = SHOPPER_APPROVED_PLUGIN_PREFIX.'_surveys_menu';

    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(5); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-4" style="display: block;">
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img; ?>" alt="" />
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-36">
                        <div>
                            <h3 class="sa-primary-blue">Seals & Widgets</h3>
                            <h4>Easily tailor seals & widgets to match your website's unique style.</h4>
                        </div>
                        <div>
                            <img src="<?php echo $seals_img; ?>" alt="" />
                        </div>
                    </div>
                    <div class="sa-seal sa-seal-step1 active">
                        <div class="pt-36">
                            <div class="sa-note-custom mb-0">
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    <span>Note:</span>
                                    You may want to have a developer help you with styling and placement if you aren't familiar with CSS, HTML or Javascript.
                                    See our <a href="https://help.shopperapproved.com/en/articles/8775779-woocommerce-installation-guide" target="_blank" class="sa-underline">Installation Guide</a> for directions on customizing your Shopper Approved assets, i.e. the seal or widgets.
                                </p>
                            </div>
                        </div>
                        <!-- <hr class="mt-36"> -->
                        <div class="sa-app-buttons">
                            <a href="<?php echo esc_url( menu_page_url( $surveys , false) ); ?>" type="button" class="btn-cancel">Previous Step</a>
                            <button type="button" class="sa-btn-primary seal-countine-btn">Continue</button>
                        </div>
                    </div>
                    <form method="post" action="">
                        <div class="sa-seal sa-seal-step2">
                            <div class="pt-36">
                                <div class="sa-note-custom mb-0">
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        <span>Note:</span>
                                        Here you can add any or all of our Widgets and Seal. Hover over the
                                        <img src="<?php echo $question_img; ?>" alt="" /> to learn more about each asset.
                                    </p>
                                </div>
                            </div>
                            <div class="sa-alert-custom mt-24 mb-0" >
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    Floating Seal setting <span class="sa-alert-text">enabled</span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-36">
                                <label class="f18 f500">
                                    Install the Floating Seal
                                    <span class="sa-tooltip">What's this <img src="<?php echo $question_img; ?>" alt="" />
                                        <span class="sa-tooltip-content">
                                            <h5>Floating Seal</h5>
                                            <p>
                                                The Shopper Approved Seal acts as a stamp of social proof displaying your review count and overall rating. When clicked, it takes a user to your Shopper Approved-hosted certificate page, helping this page rank higher in Google.
                                            </p>
                                            <p>
                                                Note that the Seal will not display until you have collected at least 5 seller reviews.
                                            </p>
                                            <img src="<?php echo $preview_img1; ?>" alt="" />
                                        </span>
                                    </span>
                                </label>
                                <div class="custom-switch  default-switch normal">
                                    <input <?php if ( !empty( $seal_status ) ) echo 'checked'; ?> type="checkbox" id="Seal1" class="switch-input review-page-switch switch-input-seal" name="Seal1" value="sa_seal_status">
                                    <label for="Seal1" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                            <div class="sa-floatSeal-notes mt-16 <?php if ( empty( $seal_status ) ) echo 'd-none'; ?>">
                                <div class="sa-note-custom mb-0">
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        <span>Note:</span>
                                        We recommend not putting any third party code on a checkout page where credit card information may be entered, as it can be a security risk.
                                        Please enter any Woo Commerce Checkout URL(s) to exclude the Shopper Approved Seal Code from.
                                    </p>
                                </div>
                                <div class="sa-alert-custom-new success mt-16 mb-0" id="sa_sealbar_code" >
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        Shopper approved Floating Seal Code will be excluded from the URL.
                                    </p>
                                </div>
                                <div class="sa-input-group gap-2 mt-16">
                                    <input type="text" value="" class="sa-form-control" placeholder="http://www.examplestore.com/checkoutpage" id="sa_sealUrl">
                                    <button class="sa-btn-primary py-8" type="button" id="sa_addButton">Add URL</button>
                                </div>
                                <div class="sa-alert-custom-new success mt-16 mb-0" id="sa_sealbar_floating" >
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        The seal has been enabled
                                    </p>
                                </div>

                                <div class="sa-add-seal-wrap mt-16">
                                    <div class="sa-add-seal-url visibilty-none">
                                        <label class="f18 f500 d-block">Excluded URL's</label>
                                        <ul id="sa_sealitemList">
                                            <?php foreach ($seal_excluded as $excluded) { ?>
                                                <li><a class="sa-remove-SealUrl">[ - ]</a><span><?php echo $excluded; ?></span></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <button class="sa-btn-primary py-8" type="button" id="sa_installSeal" disabled value="sa_seal_status2">Install Seal</button>
                                </div>
                            </div>
                            <div class="sa-alert-custom mt-24 mb-0" >
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    Review Page Widget setting <span class="sa-alert-text">enabled</span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-36">
                                <label class="f18 f500">
                                    Install the Review Page Widget
                                    <span class="sa-tooltip">What's this <img src="<?php echo $question_img; ?>" alt="" />
                                        <span class="sa-tooltip-content">
                                            <h5>Reviews Page Widget</h5>
                                            <p>
                                                The Reviews Page Widget displays all of the seller reviews. This page typically ranks very highly in searches for "yourdomain.com reviews" in Google. The widget is linked to the certificate page. Installing the widget through this plugin will create a new page, <b>yourdomain.com/reviews</b>.
                                            </p>
                                            <p>
                                                Note: If you currently have a Reviews page, you can add our reviews page widget to it manually. Simply go to your WordPress dashboard, and click on Pages and add an HTML block and paste in the Reviews Page Widget code (which you can find here in the Shopper Approved dashboard.)
                                            </p>
                                            <p>
                                                To add a link to the Reviews page link in your footer, go to <b>Appearance > Menus > Select a menu</b> to edit. Then find and select <b>"Reviews"</b> and click <b>"Add to menu"</b>. Finally, click <b>"Save Menu"</b>.
                                            </p>
                                            <p>
                                                Note that the Reviews Page Widget will not display until you have collected 5 seller reviews.
                                            </p>
                                            <img src="<?php echo $preview_img2; ?>" alt="" />
                                        </span>
                                    </span>
                                </label>
                                <div class="custom-switch  default-switch normal">
                                    <input <?php if ( !empty( $review_page_status ) ) echo 'checked'; ?> type="checkbox" id="Seal2" class="switch-input review-page-switch" name="Seal2" value="sa_rp_status">
                                    <label for="Seal2" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                            <div id="sa-notes-block" class="sa-reviewPage-notes mt-16 <?php if ( empty( $review_page_status ) ) echo 'd-none'; ?>">
                                <div id="sa-rp-note" class="sa-note-custom">
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        <span>Note:</span>
                                        Copy and paste the Review Page Widget Code below into your <b>/reviews</b> page and save.
                                    </p>
                                </div>
                                <div id="sa-rp-code" class="sa-note-custom">
                                    <div class="">
                                        <h4>Review Page Widget Code</h4>
                                        <div class="sa-code-copy">
                                            <code id="review-page-text"><?php echo htmlentities($data['reviewpage_code']) ; ?></code>
                                        </div>
                                    </div>
                                    <button onclick="copyText('review-page-text')" class="sa-btn-primary py-8 miw-200 px-8 sa-copy-btn" type="button" id="copy_clipboard">Copy to Clipboard</button>
                                </div>
                            </div>
                            <div class="sa-alert-custom mt-24 mb-0" >
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    Rotating Widget setting <span class="sa-alert-text">enabled</span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-36">
                                <label class="f18 f500">
                                    Install the Rotating Widget
                                    <span class="sa-tooltip">What's this <img src="<?php echo $question_img; ?>" alt="" />
                                        <span class="sa-tooltip-content">
                                            <h5>Rotating widget</h5>
                                            <p>
                                                The rotating widget allows you to highlight and display your best reviews, such as 4- and 5-star reviews, 5-star reviews only, or your favorite reviews. This widget adds social proof to your homepage and will be installed just above the footer.
                                            </p>
                                            <img src="<?php echo $preview_img3; ?>" alt="" />
                                        </span>
                                    </span>
                                </label>
                                <div class="custom-switch  default-switch normal">
                                    <input <?php if ( !empty( $rotating_widget_status ) ) echo 'checked'; ?> type="checkbox" id="Seal3" class="switch-input review-page-switch" name="Seal3" value="sa_rotating_widget_status">
                                    <label for="Seal3" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                            <div class="sa-rotatingPage-notes mt-16 <?php if ( empty( $rotating_widget_status ) ) echo 'd-none'; ?>">
                                <div class="sa-note-custom">
                                    <img src="<?php echo $info_img; ?>" alt="" />
                                    <p>
                                        <span>Note:</span>
                                        Add the Rotating Widget Code below to your Woo Commerce site anywhere you would like the Rotating Review Widget to appear.
                                    </p>
                                </div>
                                <div class="sa-note-custom">
                                    <div class="">
                                        <h4>Rotating Widget Code</h4>
                                        <div class="sa-code-copy">
                                            <code id="rotating-widget-text"><?php echo htmlentities($data['sa_rotating_widget_code']) ; ?></code>
                                        </div>
                                    </div>
                                    <button onclick="copyText('rotating-widget-text')" class="sa-btn-primary py-8 miw-200 px-8 sa-copy-btn" type="button" id="copy_clipboard">Copy to Clipboard</button>
                                </div>
                            </div>
                            <div class="sa-alert-custom mt-24 mb-0" >
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    Product Reviews Widgets setting <span class="sa-alert-text">enabled</span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-36">
                                <label class="f18 f500">
                                    Install the Product Reviews Widgets
                                    <span class="sa-tooltip">What's this <img src="<?php echo $question_img; ?>" alt="" />
                                        <span class="sa-tooltip-content">
                                            <h5>Product Page Widget</h5>
                                            <p>
                                                Product Page Widgets display your product reviews on your product pages. This widget includes the star rating and review count that will appear next to the product name and price. The widget itself will display lower on the product page, and will display your full product reviews where your customers can read about other customers' experiences with your products.
                                            </p>
                                            <p>
                                                If you don't have any product reviews for a particular product, we will display your seller reviews as a backup until you collect product reviews.
                                            </p>
                                            <img src="<?php echo $preview_img4; ?>" alt="" />
                                        </span>
                                    </span>
                                </label>
                                <div class="custom-switch  default-switch normal">
                                    <input <?php if ( !empty( $product_widget_status ) ) echo 'checked'; ?> type="checkbox" id="Seal4" class="switch-input review-page-switch" name="Seal4" value="sa_pwidgets_status">
                                    <label for="Seal4" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                            <div class="sa-alert-custom mt-24 mb-0" >
                                <img src="<?php echo $info_img; ?>" alt="" />
                                <p>
                                    Category Page Stars setting <span class="sa-alert-text">enabled</span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-36 pb-36">
                                <label class="f18 f500">
                                    Install the Category Page Stars
                                    <span class="sa-tooltip">What's this <img src="<?php echo $question_img; ?>" alt="" />
                                        <span class="sa-tooltip-content">
                                            <h5>Category Page Stars</h5>
                                            <p>
                                                Category Page Stars allow you to display star ratings and review counts below each product listing on collection pages.This adds a touch of social proof throughout your site and will easily attract customers to give your products a closer look.
                                            </p>
                                            <img src="<?php echo $preview_img5; ?>" alt="" />
                                        </span>
                                    </span>
                                </label>
                                <div class="custom-switch  default-switch normal">
                                    <input <?php if ( !empty( $category_stars_status ) ) echo 'checked'; ?> type="checkbox" id="Seal5" class="switch-input review-page-switch" name="Seal5" value="sa_cstars_status">
                                    <label for="Seal5" class="switch-label switch-label-no">
                                    </label>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="sa-app-buttons">

                                <a href="<?php echo esc_url( menu_page_url( $surveys , false) ); ?>" type="button" class="btn-cancel sa-seal-back">Previous Step</a>

                                <button onclick="toggleLoading()" type="submit" class="sa-btn-primary" id="sa_continueButton" <?php if ( empty( $seal_status ) || empty( $seal_status2 ) ) echo 'disabled'; ?> name="sa_continue_congrats">Continue</button>

                                <!--
                                <button onclick="toggleLoading()" type="submit" class="sa-btn-primary" id="sa_continue_survey" <?php if ( empty( $sa_survey_status ) || $sa_survey_status == false ) echo 'disabled'; ?>>Continue to Survey Settings</button>
                                -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * This function is used to show the Congratulations page
 *
 * @return void
 */
function get_congratulations_view()
{
    $woo_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/woocommerce-logo.svg';
    $info_img = SHOPPER_APPROVED_PLUGIN_ROOT_URL . 'assets/images/info-circle.svg';

    $seal_widgets = SHOPPER_APPROVED_PLUGIN_PREFIX.'_widgets_menu';

    ?>
    <div class="sa-step-app">

        <?php get_left_side_menu(6); ?>

        <div class="sa-app-steps">
            <div class="sa-steps-content d-block">
                <div class="sa-app-step" id="step-5" <?php if (! get_option('sa_step6_status')) echo 'style="display: block;"'?>>
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img; ?>" alt="" />
                    </div>
                    <div class="pt-36 mw-40">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="47" viewBox="0 0 48 47" fill="none" class="mt-5">
                            <path d="M24 0C19.2533 0 14.6131 1.35544 10.6663 3.89492C6.71954 6.4344 3.6434 10.0439 1.8269 14.2669C0.0103988 18.4899 -0.464881 23.1367 0.461164 27.6199C1.38721 32.103 3.67299 36.221 7.02945 39.4531C10.3859 42.6853 14.6623 44.8864 19.3178 45.7781C23.9734 46.6699 28.799 46.2122 33.1844 44.463C37.5698 42.7138 41.3181 39.7515 43.9553 35.9509C46.5924 32.1503 48 27.6821 48 23.1111C48 16.9817 45.4714 11.1033 40.9706 6.76909C36.4697 2.43491 30.3652 0 24 0ZM21.9996 33.9733L12 23.0533L15.1677 19.4133L21.9464 26.9825L34.8 13.1968L38.0004 16.9867L21.9996 33.9733Z" fill="#4CA30D"/>
                        </svg>
                        <h2 class="sa-primary-blue mb-0 pt-24">Congratulations, your setup is now complete!</h2>
                    </div>
                    <div class="pt-36 pb-36">
                        <div class="sa-note-custom mb-0">
                            <img src="<?php echo $info_img; ?>" alt="">
                            <p>
                                <span>Note:</span>
                                If you need to make any changes, use the menu on the left to navigate to the settings.
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="sa-app-buttons">
                        <a href="<?php echo esc_url( menu_page_url( $seal_widgets , false) ); ?>" type="button" class="btn-cancel">Previous Step</a>
                        <button type="button" id="congrats-continue" class="sa-btn-primary">Finish & Close</button>
                    </div>
                </div>
                <div class="sa-app-step" id="step-6" <?php if ( get_option('sa_step6_status')) echo 'style="display: block;"'?>>
                    <div class="sa-shopify-app-heading">
                        <img src="<?php echo $woo_img; ?>" alt="" />
                    </div>
                    <div class="sa-regenerate sa-regenerate-step1 active sa-feed">
                        <div id="sa-congrats-note" class="sa-note-custom mt-36 mb-24 align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="33" viewBox="0 0 35 33" fill="none">
                                <path d="M17.0391 0.542969C13.6768 0.542969 10.39 1.48135 7.59437 3.23945C4.79874 4.99756 2.61981 7.49641 1.33312 10.42C0.0464283 13.3436 -0.290228 16.5607 0.36572 19.6644C1.02167 22.7681 2.64076 25.619 5.01825 27.8567C7.39575 30.0943 10.4249 31.6182 13.7225 32.2355C17.0202 32.8529 20.4383 32.536 23.5447 31.325C26.651 30.114 29.3061 28.0633 31.174 25.4321C33.042 22.8009 34.0391 19.7075 34.0391 16.543C34.0391 12.2995 32.248 8.22984 29.0599 5.22926C25.8718 2.22868 21.5477 0.542969 17.0391 0.542969ZM15.6221 24.063L8.53906 16.503L10.7828 13.983L15.5844 19.2232L24.6891 9.67922L26.956 12.303L15.6221 24.063Z" fill="#4CA30D"/>
                            </svg>
                            <p>
                                <b>Congratulations, your setup is now complete!</b> If you need to make any changes, use the menu on the left to navigate to the settings.
                            </p>
                            <a href="#" class="sa-cong-close" onclick="hideCongrats(); return false;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                    <path d="M15 5.54297L5 15.543M5 5.54297L15 15.543" stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                        <div class="pt-24">
                            <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                        </div>

                        <?php show_product_feed_history(); ?>
                        <hr>
                        <div class="sa-app-buttons">
                            <div></div>

                            <form method="post" action="<?php echo 'admin.php?page='.SHOPPER_APPROVED_PLUGIN_PREFIX.'_productfeed_menu'; ?>">
                                <input type="hidden" name="tab" value="generate_feed_download">
                                <button name="generate_feed" type="submit" class="sa-btn-primary sa-regenerate-btn" onclick="showFeedProgressBar()">Regenerate Product Feed</button>
                            </form>
                        </div>
                    </div>

                    <div class="sa-feed sa-feed-step2" style="display: none;">
                        <div class="pt-36 pb-36">
                            <h3 class="sa-primary-blue">WooCommerce Product Feed</h3>
                            <h4>Great news! Your product feed is currently processing.</h4>
                        </div>

                        <div class="sa-progress-container sa-complete-feed">
                            <div class="sa-counter-wrap"><span class="sa-counter"></span> %</div>
                            <div class="sa-counter-bar">
                                <div class="sa-counter-progress"></div>
                            </div>
                        </div>
                        <div class="sa-app-buttons">
                            <a href="<?php echo esc_url(menu_page_url($seal_widgets, false) ); ?>" class="btn-cancel feedstep-back">Previous Step</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>