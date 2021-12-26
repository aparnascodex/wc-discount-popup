<?php

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Popup_Settings' ) ) {

    class Popup_Settings
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
            
            add_action( 'admin_menu', [$this, 'register_popup_settings_menu'], 99 );
            add_action( 'admin_enqueue_scripts', [$this, 'include_popup_settings_script'], 10, 1 );
            add_action( 'admin_init', [$this, 'register_popup_settings'] );
        }

        public function register_popup_settings() {
        	register_setting( 'wc_popup_setting', 'wc_popup_setting' );
        }
        public function register_popup_settings_menu() {

        	add_submenu_page( 'woocommerce', 
        		__( 'Popup Settings', 'wc-popups' ),
        		__( 'Popup Settings', 'wc-popups' ),
        		'manage_options', 
        		'wc-popup-settings', 
        		[$this, 'wc_popup_settings'] );

        }

        public function include_popup_settings_script( $hook ) {
        	if( $hook === 'woocommerce_page_wc-popup-settings' ) {
        		wp_enqueue_style( 'popup-admin-css', POPUP_PLUGIN_URL. 'assets/css/admin.css' );
        	}
        }
        public function wc_popup_settings() {
        	$defaults = ['display'  => '',
        				'type'      => '',
        				'condition' => '',
        				'value'     => '',
        				'products'  => ''
        			];
        	$options = wp_parse_args( get_option( 'wc_popup_setting'), $defaults );
        	
        	?>
        	<div class='popup-settings'>
        		<div class='heading'>
        			<h3><?php _e( 'Popup Settings', 'wc-popups' ); ?></h3>
        		</div>
        		<div class='content'>
        			<form method="post" action="options.php">
        				<?php settings_fields( 'wc_popup_setting' ); ?>
						<?php do_settings_sections( 'wc_popup_setting' ); ?>
	        			<div class='field-section'>
	        				<label><?php _e( 'Display Popup', 'wc-popups' ) ?></label>
	        				
	    					<select name="wc_popup_setting[display]">
	    						
	    						<option value='1' <?php echo selected( 1, $options['display'], false ); ?>>
	    							<?php _e( 'Show', 'wc-popups' ) ?>
	    						</option>
	    						<option value='0' <?php echo selected( 0, $options['display'], false ); ?>>
	    							<?php _e( 'Don\'t Show', 'wc-popups' ) ?>
	    						</option>
	    					</select>
	        				
	        			</div>
	        			<div class='field-section'>
	        				<label><?php _e( 'Popup Type', 'wc-popups' ) ?></label>
	        				
	    					<select name="wc_popup_setting[type]">
	    						
	    						<option value='0' <?php echo selected( 0, $options['type'], false ); ?>>
	    							<?php _e( 'Cart Totals', 'wc-popups' ) ?>
	    						</option>
	    						<option value='1' <?php echo selected( 1, $options['type'], false ); ?>>
	    							<?php _e( 'Number of items in Cart', 'wc-popups' ) ?>
	    						</option>
	    						<option value='2' <?php echo selected( 2, $options['type'], false ); ?>>
	    							<?php _e( 'Products in Cart', 'wc-popups' ) ?>
	    						</option>
	    					</select>
	        				
	        			</div>
	        			<div class='field-section'>
	        				<label><?php _e( 'Condition', 'wc-popups' ) ?></label>
	        				
	    					<select name="wc_popup_setting[condition]">
	    						
	    						<option value='0' <?php echo selected( 0, $options['condition'], false ); ?>>	
	    							<?php _e( 'Greater than or equal to', 'wc-popups' ) ?>
	    						</option>
	    						<option value='1' <?php echo selected( 1, $options['condition'], false ); ?>>
	    							<?php _e( 'Equal to', 'wc-popups' ) ?>
	    						</option>
	    						<option value='2' <?php echo selected( 2, $options['condition'], false ); ?>>
	    							<?php _e( 'Less than', 'wc-popups' ) ?>
	    						</option>
	    					</select>
	        				
	        			</div>
	        			<div class='field-section'>
	        				<label><?php _e( 'Value', 'wc-popups' ) ?></label>
	        				
	    					<input type='number' name="wc_popup_setting[value]" value='<?php echo $options['value']; ?>' />
	        				
	        			</div>
	        			<div class='field-section'>
	        				<label><?php _e( 'Product', 'wc-popups' ) ?></label>
	        				
	    					<select name="wc_popup_setting[products]">
	    						<option value=''><?php _e( 'Select Product', 'wc-popups' ) ?></option>
	    						
	    					</select>
	        				
	        			</div>
	        			<div class='field-section'>
	        				<input type='submit' value='Save' name='save' class='button button-primary' /> 
	        			</div>
	        		</div>
        		</form>
        	</div>
        	<?php
        }
    }

    Popup_Settings::get_instance();
}