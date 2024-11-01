<?php
/**
 * The product page code on product pages.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 */

/**
 * This class adds shopper approved product page code on product pages.
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 * @author     Shopper Approved
 */
class Shopper_Approved_Product {

    /**
     * @return void
     */
    public function shopperapproved_product_stars() {

        $code = get_option('sa_product_stars_code');

        echo $code;

        /*
        global $product;
        $productId = $product->get_id();
        $code = explode('PRODUCT_ID', get_option('sa_product_stars_code'));

        echo $code[0].$productId.$code[1];
        */
    }

    /**
     * @return void
     */
    public function shopperapproved_product_widget() {

        global $product;

        // using product id
        $productId = $product->get_id();

        $code = explode('PRODUCT_ID', get_option('sa_product_widget_code'));
        echo $code[0].$productId.$code[1];
    }
}