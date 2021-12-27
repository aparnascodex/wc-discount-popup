<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Popup_Loader' ) ) {

    class Popup_Loader
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
            
            add_action( 'admin_init', [$this, 'check_dependency'] );
            $this->files_loader();

        }

        /**
         * Show WooCommerce missing notice if the WooCommerce plugin is not active.
         */
        public function check_dependency() {
            if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                add_action( 'admin_notices', [$this, 'wdp_wc_missing_error'] );
            }

            //We have registed the pop up setting menu under WooCommerce menu. If the WooCommerce plugin is not active then don't redirect user, as it will throw the page not found error.
            
            if( get_option( 'popup_redirect_after_activation', false ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

                delete_option( 'popup_redirect_after_activation' );
                exit( wp_redirect( admin_url( 'admin.php?page=wc-popup-settings' ) ) );

            }
        }

        public function wdp_wc_missing_error() {
            ?>
            <div class="error">
                <p>
                    <?php 
                    printf( __( '<b>"WooCommerce Discount Popup" </b> requires WooCommerce to be active and installed. Please ensure that WooCommerce version %1$s or higher is active', 'wc-popups' ), WC_VERSION_REQUIRED ); 
                    ?>
                </p>
              </div>
            <?php
        }

        /**
        * Include plugin files.
        */
        public function files_loader() {
            require_once 'class-popup-settings.php';
            require_once 'class-popup.php';
        }
    }
    Popup_Loader::get_instance();
}
