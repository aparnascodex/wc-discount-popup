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
            
            add_action( 'admin_init', [$this, 'redirect_user_to_settings_page'] );
            $this->files_loader();

        }

        /**
         * Show WooCommerce missing notice if the WooCommerce plugin is not active.
         */
        public function redirect_user_to_settings_page() {
            
            //We have registed the pop up setting menu under WooCommerce menu. If the WooCommerce plugin is not active then don't redirect user, as it will throw the page not found error.
            
            if( get_option( 'popup_redirect_after_activation', false ) ) {

                update_option( 'popup_redirect_after_activation', false );
                exit( wp_redirect( admin_url( 'admin.php?page=wc-popup-settings' ) ) );

            }
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
