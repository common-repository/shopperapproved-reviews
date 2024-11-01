<?php

/**
 * Fires during plugin deactivation
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 */

/**
 * Fires during plugin deactivation
 * The class contains all the necessary code for plugin deactivation
 * @package    shopperapproved
 * @subpackage shopperapproved/includes
 * @author     Shopper Approved
 */
class Shopper_Approved_Deactivator {

    /**
     * @return void
     */
    public static function deactivate()
    {
        // delete WooCommerce app on SA side first
        $siteId = get_option('sa_site_id');
        $token = get_option('sa_api_token');

        if( !empty($siteId) && !empty($token)) {

            use_shopperapproved_api('configure', array('action' => 'deactivate'));
        }

        delete_option('sa_site_id');
        delete_option('sa_survey_token');
        delete_option('sa_api_token');
        delete_option('sa_seal_status');
        delete_option('sa_seal_status2');
        delete_option('sa_seal_excluded');
        delete_option('sa_survey_status');
        delete_option('sa_pwidgets_status');
        delete_option('sa_rp_status');
        delete_option('rp_already_exists');
        delete_option('sa_pf_history');
        delete_option('sa_cstars_status');
        delete_option('sa_rotating_widget_status');
        delete_option('sa_domain');
        delete_option('sa_days_to_delivery');
        delete_option('sa_feed_gtin');

        delete_option('sa_step_progress');
        delete_option('sa_step1_status');
        delete_option('sa_step2_status');
        delete_option('sa_step2b_status');
        delete_option('sa_step3_status');
        delete_option('sa_step4_status');
        delete_option('sa_step5_status');
        delete_option('sa_step6_status');

        $timestamp = wp_next_scheduled( 'generate_feeds_hook' );
        wp_unschedule_event( $timestamp, 'generate_feeds_hook' );
    }
}