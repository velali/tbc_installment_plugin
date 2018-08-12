<?php

class WC_tbc_installment extends WC_Payment_Gateway {

    function __construct() {
		
		
		 
			$this->id                 = 'tbcinstallment';
			$this->has_fields         =  false;
			$this->order_button_text  =  __( 'განვადებით შეძენა', 'tbcinstallment' );
			$this->method_title       =  __( 'თიბისი ბანკის ონლაინ განვადების მოდული', 'tbcinstallment' );
			$this->method_description =  __( 'მოდული საშუალებას იძლევა ვებ გვერდიდან ონლაინ მოხდეს განვადების შევსება', 'tbcinstallment' );
			
			 $this->supports          =  array(
				'products'
			);
			$this->title = __( "მოდული საშუალებას იძლევა ვებ გვერდიდან ონლაინ მოხდეს განვადების შევსება", 'tbcinstallment' );
			$this->icon = plugin_dir_url(__FILE__).'/tbc.png';
 
			$this->init_form_fields();
			$this->init_settings();
		
		foreach ( $this->settings as $setting_key => $value ) 
		{
            $this->$setting_key = $value; 
        }
		
		
		 
			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->debug       = 'yes' === $this->get_option( 'debug', 'no' );
			$this->cert_path   = $this->get_option( 'cert_path' );
			$this->cert_pass   = $this->get_option( 'cert_pass' );
			$this->ok_slug     = $this->get_option( 'ok_slug' );
			$this->fail_slug   = $this->get_option( 'fail_slug' );
			
			 
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
			add_action( 'admin_notices', array( $this, 'do_ssl_check' ) );
			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'order_details' ) );
			add_action( 'woocommerce_api_redirect_to_payment_form', array( $this, 'redirect_to_payment_form' ) );
			add_action( 'woocommerce_api_' . $this->ok_slug, array( $this, 'return_from_payment_form_ok' ) );
			add_action( 'woocommerce_api_' . $this->fail_slug, array( $this, 'return_from_payment_form_fail' ) );
			add_action( 'woocommerce_api_close_business_day', array( $this, 'close_business_day' ) );
			add_action( 'woocommerce_api_is_wearede', array( $this, 'is_wearede_plugin' ) );
       
	   
        if ( is_admin() )
			{          
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			}
    }


	public function init_form_fields() 
	{ 
        $this->form_fields = array(
            'enabled' => array(
                'title'     => __( 'ჩართვა / გამორთვა', ''),                
                'type'      => 'checkbox',
                'default'   => 'no',
            ),

			 'api_url' => array(
                'title'     => __( 'TBC განვადების ვებ მისამართი', '' ),
                'type'      => 'text',                 
                'default'   => __( 'http://www.tbcbank.ge/web/ka/web/guest/apply-online-for-installment', '' )
            ),
			
            'source' => array( 
                'title'     => __( 'საიტის მისამართი', '' ),
                'type'      => 'text',
                'desc_tip'  => __( 'მიუთითეთ თქვენი ვებ გვერდი რომელიც გინდა რომ დაფიქსირდეს გამყიდველად', '' ),
                'default'   => __( site_url(), '' )
            )
          
        );
    }
	
	 public function process_payment( $order_id ) {
     global $woocommerce;
 
$count = $woocommerce->cart->cart_contents_count;
if ($count > 0)
{
  $icount = $count; 
}
		
		 
			$this->init_form_fields();
			$this->init_settings();
			
			$order    = wc_get_order( $order_id );
			$currency = $order->get_order_currency() ? $order->get_order_currency() : get_woocommerce_currency();
			$amount   = $order->order_total;
			
			
			 
			$this->api_url   = $this->get_option( 'api_url' );
			$this->source    = $this->get_option( 'source' );
		
		 
			


       
        $customer_order = new WC_Order( $order_id );
        $customer_order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));
        $backurl_s = urlencode(get_site_url().'/?page_id=935&success=1');
        $backurl_f = urlencode(get_site_url().'/?page_id=936&success=0');
        $amount = (int)$customer_order->order_total;
        $options = array(
            'soap_version'    => SOAP_1_1,
            'exceptions'      => true,
            'trace'           => 1,
            'wdsl_local_copy' => true
        );
		
		 
			$order = new WC_Order($order_id);
			$items = $order->get_items();
			$order_meta = get_post_meta($_GET["o_order_id"]); 			
			$order_user_id=$order_meta["_customer_user"][0];
			 
			if(!empty($items)){
				 $count = $woocommerce->cart->cart_contents_count;
				foreach ( $items as $item ) {
				$name = $name.', '.$item['name'];
				 }
				$name=substr($name,1,125);
				
			}
			 
		$redirect_url = $this->api_url.'?&productAmount='.$name.'&utm_source='.$this->source.'&productName='.$name.'&totalAmount='.$amount;
		 
		return array(
            'result'   => 'success',
            'redirect' => $redirect_url,
        );
        die;
		
    

    }
}