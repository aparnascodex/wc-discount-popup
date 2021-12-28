<?php
/**
 * Display discount popup on frontend
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Popup' ) ) {

    class Popup
    {
        /**
         * The unique instance of the plugin.
         *
         * @var Instance variable
         */
        private static $instance;

        /**
         * Gets an instance of our plugin.
         * 
         * @return class instance
         */
        public static function get_instance() {

            if ( null === self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;

        }

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'wp_enqueue_scripts', [$this, 'load_scripts'] );
            add_action( 'woocommerce_before_cart', [$this, 'reset_session'] );
            add_action( 'woocommerce_after_cart', [$this, 'include_popup_content'] );
            add_action( 'wp_ajax_get_cart_details_for_popup', [$this, 'get_cart_details_for_popup'] );
            add_action( 'wp_ajax_nopriv_get_cart_details_for_popup', [$this, 'get_cart_details_for_popup'] );
            add_action( 'wp_ajax_apply_discount', [$this, 'apply_discount'] );
            add_action( 'wp_ajax_nopriv_apply_discount', [$this, 'apply_discount'] );
            add_action( 'woocommerce_cart_calculate_fees', [$this, 'implement_session_discount'], 20, 1 );
        }

        /**
         * Get pop up settings
         * 
         * @return array $options
         */
        public function get_popup_settings() {
            $defaults = ['display'  => '',
                        'type'      => '',
                        'condition' => '',
                        'value'     => '',
                        'products'  => ''
                    ];
            $options = wp_parse_args( get_option( 'wc_popup_setting'), $defaults );
            return $options;
        }

        /**
         * Check following two conditions and include scripts
         * 1. Check if the current page is cart page 
         * 2. Check if the pop up should be displayed or not 
         * 
         */
        public function load_scripts() {
            if( WC_ACTIVE && is_cart() ) {

                $options = $this->get_popup_settings();
                $render_popup = $this->is_discount_applicable() && !WC()->session->__isset( 'custom_discount' );
            
                wp_enqueue_style( 'popup-css', POPUP_PLUGIN_URL. 'assets/css/styles.css' );
                wp_enqueue_script( 'popup-js', POPUP_PLUGIN_URL. 'assets/js/popup.js', ['jquery'] );
                wp_localize_script( 'popup-js', 'popup', [ 'options' => $options, 'display' => $render_popup, 'url' => admin_url( 'admin-ajax.php' ), 'popup_nonce' => wp_create_nonce( 'discount_nonce' )] );
                
            }
        }

        /**
         * If pop up is enabled then render hidden modal html after cart content
         * 
         */
        public function include_popup_content() {
            ?>
            <section class="modal container">
            <div class="modal__container" id="popup-container">
                <div class="modal__content">
                    
                    <img src="<?php echo POPUP_PLUGIN_URL. 'assets/img/gift.png';?>" alt="" class="modal__img">
                    <h1 class="modal__title">Get 15% coupon now</h1>
                    <p class="modal__description">Enjoy our amazing products for a 15% discount code</p>

                    <button class="modal__button apply-code modal__button-width">
                        Apply Code
                    </button>

                    <button class="modal__button-link close-modal">
                        Close
                    </button>
                </div>
            </div>
            </section>
            <?php
        }

        /**
         * Remove discount variable from WC session if the discount is no longer valid
         *
         */
        public function reset_session() {
            $render_popup = $this->is_discount_applicable();
            if($render_popup == 0 && WC()->session->__isset( 'custom_discount' ) ) {
                WC()->session->__unset( 'custom_discount' );
            }
        }


        /**
         * Check whether cart items satisfies the pop up condition and return pop up flag
         * 
         * @return int $render_popup
        */
        public function is_discount_applicable() {
           
            $options = $this->get_popup_settings();

            $render_popup = 0;

            $type = $options['type'];
            $condition =  $options['condition'];
            $value =  $options['value'];
            $is_valid = 0;
            if( $options['type'] == 0 ) {

                // discount type is cart totals. Get cart total and compare it with settings value.

                $cart_total =  WC()->cart->subtotal;

                if( $condition == 0 && $cart_total >= $value) {
                    $is_valid = 1;
                }
                elseif( $condition == 1 && $cart_total == $value) {
                    $is_valid = 1;
                }
                elseif( $condition == 2 && $cart_total < $value) {
                    $is_valid = 1;
                }
            }
            elseif( $options['type'] == 1 ) {

                // discount type is number of cart items. Get total number of products in cart and compare it with settings value.

                $cart_product_count =  WC()->cart->get_cart_contents_count();
                if( $condition == 0 && $cart_product_count >= $value) {
                    $is_valid = 1;
                }
                elseif( $condition == 1 && $cart_product_count == $value) {
                    $is_valid = 1;
                }
                elseif( $condition == 2 && $cart_product_count < $value) {
                    $is_valid = 1;
                }
            }
            elseif( $options['type'] == 2 ) {

                // discount type is products in cart. Match items in cart with selected products in settings.

                $products = $options['products'];
                $product_ids = wp_list_pluck( WC()->cart->get_cart_contents(), 'product_id' );
                $common = array_intersect( $products, $product_ids );
                if( is_array($common) && count($common) >= 1 ){
                    $is_valid = 1;
                }

            }
            if( ( $options['display'] == 1 && $is_valid == 1 ) ||  ($options['display'] == 0 && $is_valid == 0 ) ) {
                $render_popup = 1;
            }
            return $render_popup;
        }

        /**
         * Ajax call after cart items are updated
         */
        public function get_cart_details_for_popup() {
            check_ajax_referer( 'discount_nonce', 'nonce' );

            $render_popup = $this->is_discount_applicable();


            if( $render_popup == 1 && ! WC()->session->__isset( 'custom_discount' ) ) {

                //the cart is valid and discount is not already applied. Then show discount popup.
                echo 1;
            }
            else {
                //Don't show popup.
                echo -1;
            }
            die();
        }

        /**
         * Ajax call to apply discount. It adds discount variable in WC session which later will be used in WC hook
         */
        public function apply_discount() {
            check_ajax_referer( 'discount_nonce', 'nonce' );

            //Calculate discount on cart total and set discount in WC session.
            global $woocommerce;
            $options = $this->get_popup_settings();
            $discount = $woocommerce->cart->cart_contents_total  * 0.15;
           
            WC()->session->set( 'custom_discount', $discount );
            
            echo $options['type'];

            die();
        }

        /**
         * Check if discount variable is set in WC session and add line item for discount
         * 
         */
        public function implement_session_discount( $cart ) {
            if ( is_admin() && ! defined( 'DOING_AJAX' ) )
                return;

            //If discount variable is set in session then add discount line item in cart.
            if (  WC()->session->__isset( 'custom_discount' ) )
                $discount = (float) WC()->session->get( 'custom_discount' );

            if( isset($discount) && $discount > 0 ) {
                $cart->add_fee( __( '15% discount', 'wc-popups' ), -$discount );
            }
        }

        
        
    }
    Popup::get_instance();
}
