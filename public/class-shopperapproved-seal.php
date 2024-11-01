<?php
/**
 * The Seal code on frontend pages.
 *
 * @link       http://shopperapproved.com
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 */

/**
 * This class adds shopper approved seal code on frontend pages.

 * @package    shopperapproved
 * @subpackage shopperapproved/public
 * @author     Shopper Approved
 */

class Shopper_Approved_Seal {

    /**
     * @return void
     */
    public function shopperapproved_seal_code(){

        // getting current page URL
        $url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $current_page = trim($url,"/"); // remove slashes on both ends

        // check for excluded URLs
        $excluded_pages = get_option('sa_seal_excluded') ? get_option('sa_seal_excluded') : [];

        $is_excluded = false;
        foreach ($excluded_pages as $excluded_page) {

            $excluded_page = trim($excluded_page,"/"); // remove slashes on both ends

            if ($excluded_page == $current_page) {
                $is_excluded = true;
                break;
            }
        }

        if (! $is_excluded) {
            echo get_option('sa_seal_code'); // show seal if current page is not excluded
        }
    }
}