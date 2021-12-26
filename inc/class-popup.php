<?php

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

            add_action( 'woocommerce_after_cart', [$this, 'include_popup_content'] );
            add_action( 'wp_ajax_get_cart_details_for_popup', [$this, 'get_cart_details_for_popup'] );
            add_action( 'wp_ajax_nopriv_get_cart_details_for_popup', [$this, 'get_cart_details_for_popup'] );

        }

        public function include_popup_content() {
            $defaults = ['display'  => '',
                        'type'      => '',
                        'condition' => '',
                        'value'     => '',
                        'products'  => ''
                    ];
            $options = wp_parse_args( get_option( 'wc_popup_setting'), $defaults );
            $render_popup = $this->should_render_popup();
            
            wp_enqueue_style( 'popup-css', POPUP_PLUGIN_URL. 'assets/css/styles.css' );
            wp_enqueue_script( 'popup-js', POPUP_PLUGIN_URL. 'assets/js/popup.js', ['jquery'] );
            wp_localize_script( 'popup-js', 'popup', [ 'options' => $options, 'display' => $render_popup, 'url' => admin_url( 'admin-ajax.php' )] );
            ?>
            <section class="modal container">
            <div class="modal__container" id="popup-container">
                <div class="modal__content">
                    <div class="modal__close close-modal" title="Close">
                        <i class='bx bx-x'></i>
                    </div>

                    <h1 class="modal__title">Good Job!</h1>
                    <p class="modal__description">Click the button to close</p>

                    <button class="modal__button modal__button-width">
                        View status
                    </button>

                    <button class="modal__button-link close-modal">
                        Close
                    </button>
                </div>
            </div>
            </section>
            <?php
            
        }

        public function should_render_popup() {
            $defaults = ['display'  => '',
                        'type'      => '',
                        'condition' => '',
                        'value'     => '',
                        'products'  => ''
                    ];
            $options = wp_parse_args( get_option( 'wc_popup_setting'), $defaults );

            $render_popup = 0;
            if( $options['display'] == 1) {
               
                $type = $options['type'];
                $condition =  $options['condition'];
                $value =  $options['value'];
                if( $options['type'] == 0 ) {
                    $cart_total =  WC()->cart->total;
                    if( $condition == 0 && $cart_total >= $value) {
                        $render_popup = 1;
                    }
                    elseif( $condition == 1 && $cart_total == $value) {
                        $render_popup = 1;
                    }
                    elseif( $condition == 2 && $cart_total < $value) {
                        $render_popup = 1;
                    }
                }
                elseif( $options['type'] == 1 ) {
                    $cart_product_count =  WC()->cart->get_cart_contents_count();
                    if( $condition == 0 && $cart_product_count >= $value) {
                        $render_popup = 1;
                    }
                    elseif( $condition == 1 && $cart_product_count == $value) {
                        $render_popup = 1;
                    }
                    elseif( $condition == 2 && $cart_product_count < $value) {
                        $render_popup = 1;
                    }
                }
                elseif( $options['type'] == 2 ) {
                    $products = $options['products'];
                    $product_ids = wp_list_pluck( WC()->cart->get_cart_contents(), 'product_id' );
                    $common = array_intersect( $products, $product_ids );
                    if( is_array($common) && count($common) >= 1 ){
                        $render_popup = 1;
                    }

                }
            }
            return $render_popup;
        }

        public function get_cart_details_for_popup() {
            echo $render_popup = $this->should_render_popup();
            die();
        }
    }
    Popup::get_instance();
}
