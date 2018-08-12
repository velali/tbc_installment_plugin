<?php
/*
Plugin Name: TBC - installment
Plugin URI:
Description: TBC BANK online installment
Version: 1.0
Author: Levan Qerdikashvili
Author URI: https://fb.com/levan.qerdikashvili
*/
 
add_action( 'plugins_loaded', 'tbc_init', 0 );
function tbc_init() 
	{ 
		include 'tbc_class.php';     
		add_filter( 'woocommerce_payment_gateways', 'tbc_installment' );
		function tbc_installment( $methods ) {
		$methods[] = 'WC_tbc_installment';
		return $methods;
		}
	}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'tbc_action_links' );


function tbc_action_links( $links ) 
	{
		$plugin_links = array(
		 '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'პარამეტრები', 'card' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}
	
	
 function tbc_func( $atts ) 
	{
		if( isset($_GET['success']) ){
			$success = (int)$_GET['success'];
			if( $success == 1 ){
				echo '<p>Thank you, for your payment.</p>';
			} else {
				echo '<p>Payment failed.</p>';
			}
		}
	}
	
add_shortcode( 'card', 'tbc_func' );