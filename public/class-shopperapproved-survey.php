<?php
/**
 * This class adds survey code on thank you pages.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/public
 * @author     Shopper Approved
 */

class Shopper_Approved_Survey {

    /**
     * @param $order_id
     *
     * @return void
     */
    public function shopperapproved_add_survey($order_id)
    {
        $order = new WC_Order($order_id);
        $items = $order->get_items();
        if($items){
            $sa_products=array();

            foreach ( $items as $item_id => $item_data ) {

                // using WC Product ID and name for products array
                $sa_products[$item_data['product_id']] = $item_data['name'];
            }

            $site_id = get_option('sa_site_id');
            $api_token = get_option('sa_survey_token');
            $thankyou_script = get_option('sa_thankyou_code');
            $order_number = $order->get_order_number();
            $customer_email = $order->get_billing_email();
            $customer_name = $order->get_formatted_billing_full_name();
            $customer_state = $order->get_billing_state();
            $customer_country = $order->get_billing_country();

            // Days to delivery. The full survey email must be set to go out for X days after the customer's purchase. Default is 30
            $days_to_delivery = get_option('sa_days_to_delivery') ? (int) get_option('sa_days_to_delivery') : 30;

            $script = "var sa_products = " . wp_json_encode($sa_products) . "; ";

            $script .= "var sa_values = { 'site':" . esc_js($site_id) .
                ", 'token':'" . esc_js($api_token) .
                "', 'orderid':'" . esc_js($order_number) .
                "', 'name':'" . esc_js($customer_name) .
                "', 'email':'" . esc_js($customer_email) .
                "', 'days':'" . esc_js($days_to_delivery) .
                "', 'country':'" . esc_js($customer_country) .
                "', 'state':'" . esc_js($customer_state) . "' };";

            echo "<script type='text/javascript'>".
                $script.' '.
                $thankyou_script.
                "</script>";
        }
    }
}
