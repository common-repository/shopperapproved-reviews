<?php

/**
 * Fires during plugin activation
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 */

/**
 * Fires during plugin activation
 * The class contains all the necessary code for plugin activation
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 * @author     Shopper Approved
 */
class Shopper_Approved_Activator {

    /**
     * @return void
     */
    public static function activate()
    {
        add_option('sa_site_id', '');
        add_option('sa_survey_token', '');
        add_option('sa_api_token','');
        add_option('sa_seal_status', false);
        add_option('sa_seal_status2', false);
        add_option('sa_seal_excluded',[]);
        add_option('sa_category_code','');
        add_option('sa_product_stars_code','');
        add_option('sa_product_widget_code','');
        add_option('sa_review_page_code','');
        add_option('sa_thankyou_code','');
        add_option('sa_seal_code','');
        add_option('sa_survey_status',false);
        add_option('sa_cstars_status',false);
        add_option('sa_pwidgets_status',false);
        add_option('sa_rp_status',false);
        add_option('rp_already_exists',false);
        add_option('sa_pf_history', (string) json_encode([]));
        add_option('sa_rotating_widget_code','');
        add_option('sa_rotating_widget_status',false);
        add_option('sa_domain','');
    }
}