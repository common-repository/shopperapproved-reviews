<?php
/**
 * Product CSV generation.
 *
 * @link       http://www.shopperapproved.com
 * @since      1.0.1
 *
 * @package    shopperapproved
 * @subpackage shopperapproved/admin
 */

/**
 * This class generates and creates a csv of all products.
 * @package    shopperapproved
 * @subpackage shopperapproved/admin
 * @author     Shopper Approved
 */
class Shopper_Approved_Products {
    private static $directory = SHOPPER_APPROVED_MAIN_UPLOADS_DIR;
    private static $filename = 'products_feed.csv';

    /**
     *
     * @return bool
     */
    public static function upload_products_csv(): bool
    {
        $args = array(
            'orderby'  => 'name',
            'posts_per_page' => - 1
        );
        $products = wc_get_products( $args );

        return self::generate_csv( $products );
    }

    /**
     * @return string
     */
    public static function get_csv_url(): string
    {
        $filename = self::get_csv_filename();

        $file_path = self::$directory . $filename;
        if ( file_exists( $file_path ) ) {
            return SHOPPER_APPROVED_MAIN_UPLOADS_URL . $filename;
        }
        return false;
    }

    /**
     * @param int $count
     *
     * @return bool
     */
    public static function record_history(int $count): bool
    {
        $history = self::get_history();
        $history[] = array(
            'number' => count( $history ) + 1,
            'products_count' => $count,
            'feed_date' => wp_date('Y-m-d H:i:s')
        );
        return update_option('sa_pf_history', json_encode($history));
    }

    /**
     * @param bool $desc
     *
     * @return array
     */
    public static function get_history( bool $desc = false ): array
    {
        $history = json_decode(
            get_option( 'sa_pf_history', (string) json_encode([]) ), true
        );
        if ( $desc ) {
            rsort( $history );
        }
        return $history;
    }

    /**
     * @param $products
     *
     * @return bool
     */
    public static function generate_csv( $products ): bool
    {
        try {
            $filename = self::get_csv_filename();
            $fp      = fopen( self::$directory . $filename, 'w' );

            $headers = array( 'WC_Product ID', 'WC_Product Name', 'WC_Product URL', 'WC_Image URL', 'WC_MPN', 'WC_GTIN', 'WC_Parent ID', 'WC_Cart ID', 'WC_Google ID');
            fputcsv( $fp, $headers );

            // get GTIN option
            $gtin = get_option('sa_feed_gtin');

            foreach ( $products as $product ) {

                $product_id = $product->get_id(); // Product ID i.e. WC_Product ID
                $sku = $product->get_data()['sku']; // Product SKU i.e. WC_MPN

                // getting custom Product GTIN Attribute which was set by user in step 2 before clicking "Generate Product Feed"
                if ( !empty($gtin) && $gtin != 'none') {
                    $custom_gtin = $product->get_data()[ $gtin ];
                } else {
                    $custom_gtin = '';
                }

                fputcsv( $fp, array(
                    $product_id,
                    $product->get_name(),
                    get_permalink( $product_id ),
                    wp_get_attachment_image_url( $product->get_image_id(), 'full' ),
                    $sku,
                    $custom_gtin,
                    $product->get_parent_id() ?: $product_id,
                    $product_id,
                    'woocommerce_gpf_'.$product_id
                ) );
            }
            self::record_history( count( $products ) );
            fclose( $fp );
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * this function is used to return the Product Feed CSV file name
     *
     * @return string
     */
    private static function get_csv_filename(): string
    {
        if ( !empty(get_option('sa_site_id')) ) {
            $filename = 'sa_wc_'.get_option('sa_site_id').'.csv';
        } else {
            $filename = self::$filename;
        }

        return $filename;
    }
}