<?php
/**
 * The Category code on collection pages.
 *
 * @link       http://shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 */

/**
 * This class adds shopper approved category code on collection pages.

 * @package    shopperapproved
 * @subpackage shopperapproved/public
 * @author     Shopper Approved
 */

class Shopper_Approved_Category {

    /**
     * @return void
     */
    public function shopperapproved_category_code(){
        global $product;
        $productId = $product->get_id(); // using WC product ID

        echo str_replace("PRODUCT_ID", $productId, get_option('sa_category_code'));
    }

    /**
     * @return void
     */
    public function shopperapproved_category_script()
    {
        echo get_option('sa_category_script');
    }
}