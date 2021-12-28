<?php
/**
 * Plugin Name: WooCommerce Popups
 * Plugin URI: https://github.com/aparnascodex/wc-discount-popup
 * Description: Display conditional discount pop up on WooCommerce cart page
 * Version: 1.0.1
 * Author: Aparna
 * Text Domain: wc-popups
 */

// Don't do anything if called directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'POPUP_PLUGIN_FILE', __FILE__ );
define( 'POPUP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'POPUP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_VERSION_REQUIRED', '5.2.1' );
define( 'POPUP_VERSION', '1.0.1' );
define( 'WC_ACTIVE' , is_plugin_active( 'woocommerce/woocommerce.php' ) );
require_once POPUP_PLUGIN_DIR.'/inc/class-popup-loader.php';

//Save redirection value on plugin activation.
register_activation_hook( POPUP_PLUGIN_FILE, 'redirect_to_settings_page' );
function redirect_to_settings_page() {
    add_option( 'popup_redirect_after_activation', true );
}
