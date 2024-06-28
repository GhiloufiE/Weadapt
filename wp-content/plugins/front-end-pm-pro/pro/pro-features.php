<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Fep_Pro_Features {

	private static $instance;
	
	private function __construct() {

		$this->constants();
		$this->includes();
		$this->actions();
		$this->filters();

	}
	
	public static function init()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }
            return self::$instance;
        }
	
	private function constants()
    	{
    	}
	
	private function includes()
    	{
			require( FEP_PLUGIN_DIR . 'pro/functions.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-pro-to.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-email-beautify.php' );
			require( FEP_PLUGIN_DIR . 'pro/fep-email-parser/class-fep-email-parser.php' );
			require( FEP_PLUGIN_DIR . 'pro/fep-email-parser/class-fep-email-process.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-email-piping.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-email-pop3.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-read-receipt.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-role-to-role-block.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-group-message.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-pro-cron.php' );
			require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-pro-rest-api.php' );
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				require( FEP_PLUGIN_DIR . 'pro/includes/class-fep-pro-ajax.php' );
			}
    	}
	
	private function actions()
    	{
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts' ) );
			
			add_filter( 'fep_require_manual_update', array( $this, 'req_manual_update' ), 10, 2 );
			add_action( 'admin_init', array($this, 'update' ) );
			add_action( 'fep_pro_plugin_update', array($this, 'pro_update' ) );
    	}
		
    function filters()
    	{
    	}
	
	function enqueue_scripts()
    {
		// fep-tokeninput-style & fep-tokeninput-script already registered in functions.php
		wp_register_script( 'fep-pro-to', plugins_url( '/assets/js/pro-to.js', __FILE__ ), array( 'jquery' ), FEP_PLUGIN_VERSION, true );
		
    }
	
	function admin_enqueue_scripts()
    {
		wp_register_script( 'fep-oa-script', plugins_url( '/assets/js/oa-script.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), '5.2', true );
		wp_enqueue_script( 'fep-pro-admin', plugins_url( '/assets/js/admin.js', __FILE__ ), array( 'jquery' ), FEP_PLUGIN_VERSION, true );
    }
	
	function req_manual_update( $require, $prev_ver ) {
		if ( version_compare( $prev_ver, '11.1.1', '<' ) ) {
			$require = true;
		}
		return $require;
	}
	
	function update(){
	
		$prev_ver = fep_get_option( 'plugin_pro_version', '4.4' );
		
		if( version_compare( $prev_ver, FEP_PLUGIN_VERSION, '<' ) ) {
			
			do_action( 'fep_pro_plugin_update', $prev_ver );
			
			fep_update_option( 'plugin_pro_version', FEP_PLUGIN_VERSION );
		}
	
	}
	
	function pro_update( $prev_ver ){
	
		if( version_compare( $prev_ver, '7.2', '<' ) ){
			delete_option('api_calls');
		}
		if( version_compare( $prev_ver, '10.1.3.rc1', '<' ) ){
			if ( fep_get_option( 'ep_enable', '' ) ) {
				fep_update_option( 'ep_enable', 'piping' );
			}
		}
		if ( version_compare( $prev_ver, '11.1.1', '<' ) ) {
			wp_clear_scheduled_hook( 'fep_eb_ann_email_interval_event' );
			wp_schedule_event( time(), 'fep_15_min', 'fep_email_interval_event' );
			fep_update_option( 'email_per_interval', fep_get_option( 'eb_announcement_email_per_interval', 50 ) );
			
			if ( 'hourly' === wp_get_schedule( 'fep_pop3_event' ) ) {
				wp_clear_scheduled_hook( 'fep_pop3_event' );
				wp_schedule_event( time(), 'fep_15_min', 'fep_pop3_event' );
			}
		}
	}
	
} //End Class

add_action('plugins_loaded', array( 'Fep_Pro_Features', 'init' ), 20 );

