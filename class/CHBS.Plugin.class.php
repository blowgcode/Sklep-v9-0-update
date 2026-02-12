<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPlugin
{
	/**************************************************************************/
	
	public $optionDefault;
	public $library;
	public $libraryDefault;

	/**************************************************************************/	
	
	function __construct()
	{
		$BookingWebHook=new CHBSBookingWebHook();
		
		/***/
		
		$this->libraryDefault=array
		(
			'script'=>array
			(
				'use'=>1,
				'inc'=>true,
				'path'=>PLUGIN_CHBS_SCRIPT_URL,
				'file'=>'',
				'in_footer'=>true,
				'dependencies'=>array('jquery'),
			),
			'style'=>array
			(
				'use'=>1,
				'inc'=>true,
				'path'=>PLUGIN_CHBS_STYLE_URL,
				'file'=>'',
				'dependencies'=>array()
			)
		);
		
		/***/
		
		$this->optionDefault=array
		(
			'logo'=>'',
			'google_map_map_id'=>'',
			'google_map_api_key'=>'',
			'google_map_ask_load_enable'=>0,
			'google_map_duplicate_script_remove'=>'0',
			'google_map_server_data_validation_enable'=>'0',
			'google_recaptcha_enable'=>0,
			'google_recaptcha_api_type'=>1,
			'google_recaptcha_site_key'=>'',
			'google_recaptcha_secret_key'=>'',
			'google_recaptcha_score'=>0.5,
			'google_recaptcha_badge_enable'=>1,
			'system_library_on_shortcode_page_enable'=>0,
			'license_license_id'=>'',
			'license_code'=>'',
			'license_refresh_token'=>'',
			'license_support_datetime'=>'',
			'license_last_check_datetime'=>'',
			'license_purchase_code'=>'',
			'license_username'=>'',
			'currency'=>'USD',
			'length_unit'=>'1',
			'date_format'=>'d-m-Y',
			'first_day_week'=>1,
			'time_format'=>'H:i',
			'address_format_type'=>1,
			'sender_default_email_account_id'=>'-1',
			'coupon_generate_count'=>'1',
			'coupon_generate_usage_limit'=>'1',
			'coupon_generate_discount_percentage'=>'0',
			'coupon_generate_active_date_start'=>'',
			'coupon_generate_active_date_stop'=>'',
			'currency_exchange_rate'=>array(),
			'fixer_io_api_key'=>'',
			'file_extension_to_upload'=>'jpg;jpeg;png;gif;ico;pdf;doc;docx;ppt;pptx;pps;ppsx;odt;xls;xlsx;psd;mp3;m4a;ogg;wav;mp4;m4v;mov;wmv;avi;mpg;ogv;3gp;3g2',
			'pricing_rule_return_use_type'=>'1',
			'geolocation_server_id'=>'-1',
			'geolocation_server_id_3_api_key'=>'',
			'salt_1'=>CHBSHelper::createSalt(),
			'salt_2'=>CHBSHelper::createSalt(),
			'booking_driver_acceptance_stage_1_enable'=>1,
			'booking_driver_acceptance_stage_2_enable'=>0,
			'booking_driver_acceptance_confirmation_page'=>'',
			'booking_driver_acceptance_email_recipient'=>'',
			'booking_driver_acceptance_stage_1_interval'=>15,
			'booking_driver_acceptance_stage_2_interval'=>15,
			'booking_driver_acceptance_status_after_accept'=>2,
			'booking_driver_acceptance_status_after_reject'=>1,
			'booking_driver_acceptance_notificaction_booking_status_id'=>array(2),
			'booking_cancel_enable'=>0,
			'booking_cancel_confirmation_page'=>'',
			'booking_cancel_email_recipient'=>'',
			'booking_cancel_cancellable_booking_status_id'=>array(1,5,7),			
			'payment_stripe_webhook_endpoint_id'=>'',
			'booking_status_payment_success'=>'2',
			'booking_status_synchronization'=>'1',
			'email_service_reminder_customer_enable'=>'0',
			'email_service_reminder_customer_duration'=>'30',
			'email_service_reminder_driver_enable'=>'0',
			'email_service_reminder_driver_duration'=>'30',
			'email_service_post_arrival_message_customer_enable'=>'0',
			'email_service_post_arrival_message_customer_duration'=>'7200',
			'email_service_post_arrival_message_customer_duration_unit'=>'1',
			'plugin_version'=>PLUGIN_CHBS_VERSION,
			'woocommerce_order_reduce_item'=>0,
			'woocommerce_template_plugin_enable'=>1,
			'webhook_booking_enable'=>0,
			'webhook_booking_url_address'=>'',
			'webhook_booking_run_event'=>$BookingWebHook->getDefaultRunEvent(),
			'webhook_booking_run_number'=>0,
			'run_code'=>CHBSHelper::createId(),
			'booking_status_nonblocking'=>array(3,6),
			'booking_status_sum_zero'=>'0',
			'product_verificaton_code'=>'',
			'company_detail_name'=>'',
			'company_detail_tax_number'=>'',
			'company_detail_street_name'=>'',
			'company_detail_street_number'=>'',
			'company_detail_state'=>'',
			'company_detail_postal_code'=>'',
			'company_detail_city'=>'',
			'company_detail_country'=>'',
			'company_detail_phone_number'=>'',
			'company_detail_email_address'=>'',
			'company_detail_bank_name'=>'',
			'company_detail_bank_swift'=>'',
			'company_detail_bank_iban'=>'',
			'company_detail_bank_account_number'=>'',
			'copyright_footer_enable'=>'1',
			'product_info_last_check_datetime'=>'',
			'product_info_new_version'=>''
		);
		
		/***/
	}
	
	/**************************************************************************/
	
	public function prepareLibrary()
	{
		$this->library=array
		(
			'script'=>array
			(
				'jquery-ui-core'=>array
				(
					'path'=>''
				),
				'jquery-ui-tabs'=>array
				(
					'use'=>3,
					'path'=>''
				),
				'jquery-ui-button'=>array
				(
					'path'=>''
				),
 				'jquery-ui-slider'=>array
				(
					'path'=>''
				),	
				'jquery-ui-selectmenu'=>array
				(
					'use'=>2,
					'path'=>''
				), 
				'jquery-ui-autocomplete'=>array
				(
					'use'=>2,
					'path'=>''
				), 
				'jquery-ui-sortable'=>array
				(
					'path'=>''
				),
				'jquery-ui-datepicker'=>array
				(
					'use'=>3,
					'path'=>''
				),
				'jquery-colorpicker'=>array
				(
					'file'=>'jquery.colorpicker.js'
				),
				'jquery-actual'=>array
				(
					'use'=>2,
					'file'=>'jquery.actual.min.js'
				),
				'jquery-fileupload'=>array
				(
					'use'=>2,
					'file'=>'jquery.fileupload.js'
				), 
				'jquery-timepicker'=>array
				(
					'use'=>3,
					'file'=>'jquery.timepicker.min.js'
				),
				'jquery-dropkick'=>array
				(
					'file'=>'jquery.dropkick.min.js'
				),
				'jquery-qtip'=>array
				(
					'use'=>3,
					'file'=>'jquery.qtip.min.js'
				),
				'jquery-blockUI'=>array
				(
					'file'=>'jquery.blockUI.js'
				),
				'resizesensor'=>array
				(
					'use'=>2,
					'file'=>'ResizeSensor.min.js'
				),				
				'jquery-theia-sticky-sidebar'=>array
				(
					'use'=>2,
					'file'=>'jquery.theia-sticky-sidebar.min.js'
				),
				'jquery-fancybox'=>array
				(
					'use'=>3,
					'file'=>'jquery.fancybox.js'
				),
				'jquery-fancybox-media' =>array
				(
					'use'=>3,
					'file'=>'jquery.fancybox-media.js'
				),
				'jquery-fancybox-buttons'=>array
				(
					'use'=>3,
					'file'=>'jquery.fancybox-buttons.js'
				),
				'jquery-intlTelInput'=>array
				(
					'use'=>3,
					'file'=>'intlTelInput.min.js'
				),  
				'jquery-intlTelInputUtil'=>array
				(
					'use'=>3,
					'file'=>'intlTelInputUtil.min.js'
				),  
				'jquery-table'=>array
				(
					'file'=>'jquery.table.js'
				),
				'jquery-infieldlabel'=>array
				(
					'file'=>'jquery.infieldlabel.min.js'
				),
				 'jquery-scrollTo'=>array
				(
					'use'=>3,
					'file'=>'jquery.scrollTo.min.js'
				),  
				'clipboard'=>array
				(
					'file'=>'clipboard.min.js'
				),   
				'jquery-themeOption'=>array
				(
					'file'=>'jquery.themeOption.js'
				),
				'jquery-themeOptionElement'=>array
				(
					'file'=>'jquery.themeOptionElement.js'
				),
				'chbs-helper'=>array
				(
					'use'=>3,
					'file'=>'CHBS.Helper.class.js'
				),
				'chbs-googlemaps-api'=>array
				(
					'use'=>3,
					'file'=>'CHBS.GoogleMapsAPI.class.js'
				),				
				'chbs-booking-calendar'=>array
				(
					'use'=>1,
					'file'=>'CHBS.BookingCalendar.class.js'
				),
				'chbs-admin'=>array
				(
					'file'=>'admin.js'
				),
				'chbs-chauffeur-route-admin'=>array
				(
					'file'=>'jquery.chauffeurRouteAdmin.js'
				),
				'chbs-chauffeur-geofence-admin'=>array
				(
					'file'=>'jquery.chauffeurGeofenceAdmin.js'
				),
				'chbs-booking-form-admin'=>array
				(
					'file'=>'jquery.chauffeurBookingFormAdmin.js'
				),   
				'chbs-booking-form-gutenberg-block' =>array
				(  
					'inc'=>0,
					'file'=>'gutenberg/block.build.js',
					'dependencies'=>array('wp-blocks','wp-components','wp-element','wp-i18n','wp-editor')
				),
				'chbs-public'=>array
				(
					'use'=>2,
					'file'=>'public.js'
				),
				'chbs-booking-form'=>array
				(
					'use'=>2,
					'file'=>'jquery.chauffeurBookingForm.js'
				),
				'chbs-tpay-frontend'=>array
				(
					'use'=>2,
					'file'=>'CHBS.PaymentTpayFrontend.js'
				),
				'chbs-google-map'=>array
				(
					'inc'=>false,
					'use'=>3,
					'path'=>'',
					'in_footer'=>false,
					'file'=>add_query_arg(array('key'=>urlencode(CHBSOption::getOption('google_map_api_key')),'callback'=>'Function.prototype','libraries'=>'places,drawing,marker,geometry','language'=>(defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '')),'//maps.google.com/maps/api/js')
				),
				'chbs-recaptcha'=>array
				(
					'use'=>2,
					'path'=>'',
					'file'=>add_query_arg(array('render'=>urlencode(CHBSOption::getOption('google_recaptcha_site_key'))),((int)CHBSOption::getOption('google_recaptcha_api_type')===1 ? '//www.google.com/recaptcha/api.js' : '//www.google.com/recaptcha/enterprise.js')),
				)			
			),
			'style'=>array
			(
				'google-font-open-sans'=>array
				(
					'path'=>'', 
					'file'=>add_query_arg(array('family'=>urlencode('Open Sans:300,300i,400,400i,600,600i,700,700i,800,800i'),'subset'=>'cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese'),'//fonts.googleapis.com/css')
				),
				'google-font-lato'=>array
				(
					'use'=>2,
					'path'=>'', 
					'file'=>add_query_arg(array('family'=>urlencode('Lato:300,400,700'),'subset'=>'latin-ext'),'//fonts.googleapis.com/css')
				),
				'jquery-ui'=>array
				(
					'use'=>3,
					'file'=>'jquery.ui.min.css'
				),
				'jquery-qtip'=>array
				(
					'use'=>3,
					'file'=>'jquery.qtip.min.css'
				),
				'jquery-dropkick'=>array
				(
					'file'=>'jquery.dropkick.css'
				),
				'jquery-dropkick-rtl'=>array
				(
					'inc'=>false,
					'file'=>'jquery.dropkick.rtl.css'
				),
				'jquery-colorpicker'=>array
				(
					'file'=>'jquery.colorpicker.css'
				),
				'jquery-timepicker'=>array
				(
					'use'=>3,
					'file'=>'jquery.timepicker.min.css'
				),
				'jquery-fancybox'=>array
				(
					'use'=>3,
					'file'=>'fancybox/jquery.fancybox.css'
				),
				'jquery-intlTelInput'=>array
				(
					'use'=>3,
					'file'=>'intlTelInput.min.css'
				),
				'jquery-themeOption'=>array
				(
					'file'=>'jquery.themeOption.css'
				),
				'jquery-themeOption2'=>array
				(
					'file'=>'jquery.themeOption2.css'
				),
				'jquery-themeOption-rtl'=>array
				(
					'inc'=>false,
					'file'=>'jquery.themeOption.rtl.css'
				),
				'chbs-themeOption-overwrite'=>array
				(
					'file'=>'jquery.themeOption.overwrite.css'
				),
				'chbs-themeOption2-overwrite'=>array
				(
					'file'=>'jquery.themeOption2.overwrite.css'
				),
				'chbs-public'=>array
				(
					'use'=>2,
					'file'=>'public.css'
				),
				'chbs-public-demo'=>array
				(
					'use'=>2,
					'file'=>'public.demo.css'
				),
				'chbs-public-rtl'=>array
				(
					'use'=>2,
					'inc'=>false,
					'file'=>'public.rtl.css'
				)
			)
		);
	}	
	
	/**************************************************************************/
	
	public function addLibrary($type,$use,$action='register')
	{
		if((CHBSPlugin::isAutoRideTheme()) || ((int)$use===1))
		{
			$this->library['script']['chbs-google-map']['inc']=true;
		}
		
		if(CHBSFile::fileExist(CHBSFile::getMultisiteBlogCSS()))
		{
			$this->library['style']['chbs-public-booking-form-']=array
			(
				'use'=>2,
				'path'=>'',
				'file'=>CHBSFile::getMultisiteBlogCSS('url')
			);
		}
		
		if((int)CHBSOption::getOption('google_recaptcha_enable')!==1)
		{
			unset($this->library['script']['chbs-recaptcha']);
		}
		
		if(function_exists('register_block_type'))
		{
			$this->library['script']['chbs-booking-form-gutenberg-block']['inc']=1;
			wp_set_script_translations('chbs-booking-form-gutenberg-block','chauffeur-booking-system',plugin_dir_path( __FILE__).'languages');
		}
		
		foreach($this->library[$type] as $index=>$value)
			$this->library[$type][$index]=array_merge($this->libraryDefault[$type],$value);
		
		foreach($this->library[$type] as $index=>$data)
		{
			if(!$data['inc']) continue;
			
			if($data['use']!=3)
			{
				if($data['use']!=$use) continue;
			}			
			
			if($type=='script')
			{
				if($action=='register')
					wp_register_script($index,$data['path'].$data['file'],$data['dependencies'],null,$data['in_footer']);
				else
					wp_enqueue_script($index,$data['path'].$data['file'],$data['dependencies'],null,$data['in_footer']);
			}
			else 
			{
				if($action=='register')
					wp_register_style($index,$data['path'].$data['file'],$data['dependencies'],null);
				else
					wp_enqueue_style($index,$data['path'].$data['file'],$data['dependencies'],null);
			}
		}
		
		if((int)CHBSOption::getOption('google_recaptcha_badge_enable')!==1)
		{
			wp_add_inline_style('chbs-public','.grecaptcha-badge {display:none !important;}');
		}
	}
	
	/**************************************************************************/
	
	function addLibrarySingle($type,$name)
	{
		$this->prepareLibrary();
		
		$library=$this->library[$type][$name];

		if(!isset($library)) return;
		
		$library=array_merge($this->libraryDefault[$type],$library);
		
		wp_enqueue_script($name,$library['path'].$library['file'],$library['dependencies'],false,$library['in_footer']);
	}
	
	/**************************************************************************/
	
	public function pluginActivation()
	{	
		CHBSOption::createOption();
		
		$optionSave=array();
		$optionCurrent=CHBSOption::getOptionObject();
		
		/***/
		
		$optionNameChange=array
		(
			'webhook_after_sent_booking_enable'=>'webhook_booking_enable',
			'webhook_booking_url_address'=>'webhook_booking_url_address'		
		);
		
		CHBSOption::changeOptionName($optionNameChange,$optionCurrent);

		/***/
		
		foreach($this->optionDefault as $index=>$value)
		{
			if(!array_key_exists($index,$optionCurrent))
				$optionSave[$index]=$value;
		}
		
		$optionSave=array_merge((array)$optionSave,$optionCurrent);
		foreach($optionSave as $index=>$value)
		{
			if(!array_key_exists($index,$this->optionDefault))
				unset($optionSave[$index]);
		}
		
		$optionSave['plugin_version']=PLUGIN_CHBS_VERSION;
		$optionSave['license_product_id']=self::getProductId();
		
		CHBSOption::resetOption();
		CHBSOption::updateOption($optionSave);
		
		$BookingFormStyle=new CHBSBookingFormStyle();
		$BookingFormStyle->createCSSFile();
				   
		/***/
		
		$argument=array
		(
			'post_type'=>CHBSBooking::getCPTName(),
			'post_status'=>'any',
			'posts_per_page'=>-1
		);
		
		$query=new WP_Query($argument);
		if($query!==false)
		{
			while($query->have_posts())
			{
				$query->the_post();

				$productId=metadata_exists('post',get_the_ID(),PLUGIN_CHBS_CONTEXT.'_woocommerce_product_id');
				
				if($productId===false) CHBSPostMeta::updatePostMeta(get_the_ID(),'woocommerce_product_id',0);
			}
		}
		
		/***/
		
		$argument=array
		(
			'post_type'=>CHBSPriceRule::getCPTName(),
			'post_status'=>'any',
			'posts_per_page'=>-1
		);
		
		$query=new WP_Query($argument);
		if($query!==false)
		{
			while($query->have_posts())
			{
				$query->the_post();

				$priceSourceType=get_post_meta(get_the_ID(),PLUGIN_CHBS_CONTEXT.'_price_source_type',true);
				
				if(!is_array($priceSourceType)) CHBSPostMeta::updatePostMeta(get_the_ID(),'price_source_type',array($priceSourceType));
			}
		}
				
		/***/
	}
	
	/**************************************************************************/
	
	public function pluginDeactivation()
	{

	}
	
	/**************************************************************************/
	
	public function init()
	{	
		$compare=(int)version_compare(PLUGIN_CHBS_VERSION,CHBSOption::getOption('plugin_version'));
		
		if($compare===1)
		{
			$this->pluginActivation();
		}
		
		$Demo=new CHBSDemo();
		
		$License=new CHBSLicense();
		$ProductInfo=new CHBSProductInfo();
		
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		$BookingExtra=new CHBSBookingExtra();
		$BookingCalendar=new CHBSBookingCalendar();

		$Route=new CHBSRoute();
		$Vehicle=new CHBSVehicle();
		$VehicleCompany=new CHBSVehicleCompany();
		$VehicleAttribute=new CHBSVehicleAttribute();
		
		$Location=new CHBSLocation();
		$PriceRule=new CHBSPriceRule();
		$AVRule=new CHBSAVRule();
		
		$Driver=new CHBSDriver();
		$Coupon=new CHBSCoupon();
		
		$TaxRate=new CHBSTaxRate();
		$EmailAccount=new CHBSEmailAccount();
		 
		$Geofence=new CHBSGeofence();
		$LogManager=new CHBSLogManager();
		
		$Currency=new CHBSCurrency();
		
		$BookingReport=new CHBSBookingReport();
		
		$TabCustom=new CHBSTabCustom();
		
		$PaymentTpay=new CHBSPaymentTpay(array());
		
		add_action('chbs_tpay_payment_success',array($PaymentTpay,'processPaymentSuccess'));
		add_action('wp_loaded',array($PaymentTpay,'maybeRunDiagnostics'));
		
		$License->init();
		$ProductInfo->init();
		
		$Booking->init();
		$BookingForm->init();
		$BookingExtra->init();
		
		$Route->init();
		$Vehicle->init();
		$VehicleCompany->init();
		$VehicleAttribute->init();
		
		$Location->init();
		$PriceRule->init();
		$AVRule->init();
		
		$Driver->init();
		$Coupon->init();
		
		$Geofence->init();
		$TaxRate->init();
		$EmailAccount->init();
	
		$Currency->init();
		
		$User=new CHBSUser();
		$User->init();
		
		$ExchangeRateProvider=new CHBSExchangeRateProvider();
		
		$BookingReport->init();
	
		add_filter('custom_menu_order',array($this,'adminCustomMenuOrder'));
		
		add_action('admin_init',array($this,'adminInit'));
		add_action('admin_menu',array($this,'adminMenu'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_option_page_save',array($this,'adminOptionPanelSave'));
				
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_go_to_step',array($BookingForm,'goToStep'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_go_to_step',array($BookingForm,'goToStep'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_file_upload',array($BookingForm,'fileUpload'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_file_upload',array($BookingForm,'fileUpload'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_vehicle_filter',array($BookingForm,'vehicleFilter'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_vehicle_filter',array($BookingForm,'vehicleFilter'));		
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_option_page_import_demo',array($this,'importDemo'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_create_summary_price_element',array($BookingForm,'createSummaryPriceElementAjax'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_create_summary_price_element',array($BookingForm,'createSummaryPriceElementAjax'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_user_sign_in',array($BookingForm,'userSignIn'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_user_sign_in',array($BookingForm,'userSignIn'));		
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_option_page_create_coupon_code',array($Coupon,'create'));

		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_option_page_import_exchange_rate',array($ExchangeRateProvider,'importExchangeRate'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_coupon_code_check',array($BookingForm,'checkCouponCode'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_coupon_code_check',array($BookingForm,'checkCouponCode'));  
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_vehicle_bid_price_check',array($BookingForm,'checkVehicleBidPrice'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_vehicle_bid_price_check',array($BookingForm,'checkVehicleBidPrice')); 
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_gratuity_customer_set',array($BookingForm,'setGratuityCustomer'));
		add_action('wp_ajax_nopriv_'.PLUGIN_CHBS_CONTEXT.'_gratuity_customer_set',array($BookingForm,'setGratuityCustomer'));		  

		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_test_email_send',array($EmailAccount,'sendTestEmail'));
		
		add_action('wp_ajax_'.PLUGIN_CHBS_CONTEXT.'_booking_calendar_ajax',array($BookingCalendar,'ajax'));

		add_action('admin_notices',array($this,'adminNotice'));
	   
		add_action('wp_mail_failed',array($LogManager,'logWPMailError'));
		
		add_action('wp_loaded',array($this,'wpLoaded'));
		
		add_action('chbs_booking_status_change',array($Booking,'changeStatus'),10,3);
		
		if((int)CHBSOption::getOption('google_map_duplicate_script_remove')===1)
			add_action('wp_print_scripts',array($this,'removeMultipleGoogleMap'),100);
	   
		add_theme_support('menus');
		add_theme_support('post-thumbnails');
		
		add_image_size(PLUGIN_CHBS_CONTEXT.'_vehicle',460,306); 
		add_image_size(PLUGIN_CHBS_CONTEXT.'_vehicle_2',800,533); 
		
		add_shortcode('chbs_demo_menu',array($Demo,'createMenu'));
		
		add_shortcode('chbs_tab_custom',array($TabCustom,'createTabCustom'));
		add_shortcode('chbs_tab_custom_item',array($TabCustom,'createTabCustomItem'));
		
		register_nav_menus(array('chbs-demo'=>'CHBS Demo menu'));
		
		if(!is_admin())
		{
			$PaymentStripe=new CHBSPaymentStripe();
			
			add_action('wp_enqueue_scripts',array($this,'publicInit'));
			
			add_action('wp_loaded',array($PaymentStripe,'receivePayment'),0);
			add_action('wp_loaded',array($PaymentTpay,'receivePayment'),0);
		}
			   
		if(function_exists('register_block_type'))
		{
			register_block_type('chbs-booking-form/block',array('editor_script'=>'chbs-booking-form-gutenberg-block'));
			add_filter('block_categories',array($this,'gutenbergBlockCategory'),10,2);
		}
		
		$WooCommerce=new CHBSWooCommerce();
		$WooCommerce->addAction();
	}
		
	/**************************************************************************/
	
	public function publicInit()
	{
		$this->prepareLibrary();
		
		if(is_rtl())
		{
			$this->library['style']['chbs-public-rtl']['inc']=true;
		}
		
		$this->addLibrary('style',2);
		$this->addLibrary('script',2);
		
		if((int)CHBSOption::getOption('system_library_on_shortcode_page_enable')===0)
		{
			$this->addLibrary('style',2,'enqueue');
			$this->addLibrary('script',2,'enqueue');			
		}
		
		$value=(int)CHBSHelper::getGetValue('google_maps_enable');
		
		if(in_array($value,array(-1,1)))
		{
			CHBSCookie::set('google_maps_enable',$value);
		}
	}
	
	/**************************************************************************/
	
	public function adminInit()
	{
		$this->prepareLibrary();
		
		if(is_rtl())
		{
			$this->library['style']['jquery-themeOption-rtl']['inc']=true;
			$this->library['style']['jquery-dropkick-rtl']['inc']=true;
		}
		
		$this->addLibrary('style',1);
		$this->addLibrary('script',1);
		$this->addLibrary('style',1,'enqueue');
		$this->addLibrary('script',1,'enqueue');
	
		/***/
		
		$data=array();
		
		$BookingForm=new CHBSBookingForm();
		$dictionary=$BookingForm->getDictionary();
		
		$data['booking_form_dictionary']=array();
		
		$data['booking_form_dictionary'][]=array('value'=>0,'label'=>__('Select booking form','chauffeur-booking-form'));
		
		foreach($dictionary as $value)
			$data['booking_form_dictionary'][]=array('value'=>$value['post']->ID,'label'=>$value['post']->post_title);
		
		$data['jqueryui_buttonset_enable']=(int)PLUGIN_CHBS_JQUERYUI_BUTTONSET_ENABLE;
		
		$data['time_format']=CHBSOption::getOption('time_format');
		$data['date_format']=CHBSJQueryUIDatePicker::convertDateFormat(CHBSOption::getOption('date_format'));
		$data['first_day_week']=CHBSOption::getOption('first_day_week');
		
		wp_localize_script('jquery-themeOption','chbsData',array('l10n_print_after'=>'chbsData='.json_encode($data).';'));
	}
	
	/**************************************************************************/
	
	public function adminMenu()
	{
		global $submenu;

		add_options_page(__('Chauffeur Booking System','chauffeur-booking-system'),__('Chauffeur Booking<br/>System','chauffeur-booking-system'),'edit_theme_options',PLUGIN_CHBS_CONTEXT,array($this,'adminCreateOptionPage'));
		
		add_submenu_page('edit.php?post_type='.CHBSBooking::getCPTName(),esc_html__('Bookings Calendar','chauffeur-booking-system'),esc_html__('Calendar','chauffeur-booking-system'),'edit_posts',PLUGIN_CHBS_CONTEXT.'_booking_calendar', array($this,'adminCreateBookingCalendarPage'));
		add_submenu_page('edit.php?post_type='.CHBSBooking::getCPTName(),__('Vehicle Types','chauffeur-booking-system'),__('Vehicle Types','chauffeur-booking-system'),'edit_posts','edit-tags.php?post_type='.CHBSBooking::getCPTName().'&taxonomy='.CHBSVehicle::getCPTCategoryName());
	}
	
	/**************************************************************************/
	
	public function adminCreateBookingCalendarPage()
	{
		$data=array();
		
		$BookingCalendar=new CHBSBookingCalendar();
		
		$date=$BookingCalendar->getDate();
		
		$data['header_date']=$BookingCalendar->createHeaderDate($date['booking_calendar_year_number'],$date['booking_calendar_month_name']);
		
		$data['header_booking_status']=$BookingCalendar->createHeaderBookingStatus();
		
		$data['header_vehicle']=$BookingCalendar->createHeaderVehicle();
		
		$data['calendar']=$BookingCalendar->createBookingCalendar($date['booking_calendar_year_number'],$date['booking_calendar_month_number']);
		
		$data['booking_calendar_year_number']=$date['booking_calendar_year_number'];
		$data['booking_calendar_month_number']=$date['booking_calendar_month_number'];
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_booking_calendar','booking_calendar');
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/booking_calendar.php');
		echo $Template->output();
	}
	
	/**************************************************************************/
	
	public function adminCreateOptionPage()
	{
		$data=array();
		
		$Date=new CHBSDate();
		$Length=new CHBSLength();
		$License=new CHBSLicense();
		$Country=new CHBSCountry();
		$Currency=new CHBSCurrency();
		$GeoLocation=new CHBSGeoLocation();
		$EmailAccount=new CHBSEmailAccount();
		$BookingDriver=new CHBSBookingDriver();
		$BookingStatus=new CHBSBookingStatus();
		$BookingWebHook=new CHBSBookingWebHook();
		$ExchangeRateProvider=new CHBSExchangeRateProvider();
		
		$data['option']=CHBSOption::getOptionObject();
		
		$data['dictionary']['currency']=$Currency->getCurrency();
		$data['dictionary']['length_unit']=$Length->getUnit();
		
		$data['dictionary']['email_account']=$EmailAccount->getDictionary();
	 
		$data['dictionary']['exchange_rate_provider']=$ExchangeRateProvider->getProvider();
		
		$data['dictionary']['geolocation_server']=$GeoLocation->getServer();
		
		$data['dictionary']['booking_status']=$BookingStatus->getBookingStatus();
		$data['dictionary']['booking_status_synchronization']=$BookingStatus->getBookingStatusSynchronization();
		
		$data['dictionary']['webhook_booking_run_event']=$BookingWebHook->getRunEvent();
		$data['dictionary']['booking_driver_acceptance_notificaction_send_event']=$BookingDriver->getNotificactionSendEvent();
		
		$data['dictionary']['country']=$Country->getCountry();
		
		$data['dictionary']['day']=$Date->getDay();
		
		$data['option']['license_domain']=self::getDomain();
		
		$data['option']['domain']=self::getDomain();
		$data['option']['server_ip_address']=self::getServerIPAddress();
		
		$data['option']['license_verified']=$License->isVerified();
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_option_page','option_page');
		
		wp_enqueue_media();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/option.php');
		echo $Template->output();	
	}
	
	/**************************************************************************/
	
	public function adminOptionPanelSave()
	{		
		if(CHBSHelper::verifyNonce('option_page')===false) return(false);
		
		$option=CHBSHelper::getPostOption();

		$response=array('global'=>array('error'=>1));

		$Date=new CHBSDate();
		$Notice=new CHBSNotice();
		$Length=new CHBSLength();
		$License=new CHBSLicense();
		$Currency=new CHBSCurrency();
		$Validation=new CHBSValidation();
		$BookingStatus=new CHBSBookingStatus();
		$BookingWebHook=new CHBSBookingWebHook();
		
		$invalidValue=__('This field includes invalid value.','chauffeur-booking-system');
				
		/* General / Main */
		
		if(!$Currency->isCurrency($option['currency']))
			$Notice->addError(CHBSHelper::getFormName('currency',false),$invalidValue);	
		if(!$Length->isUnit($option['length_unit']))
			$Notice->addError(CHBSHelper::getFormName('length_unit',false),$invalidValue);	
		if($Validation->isEmpty($option['date_format']))
			$Notice->addError(CHBSHelper::getFormName('date_format',false),$invalidValue);
		if(!$Date->isDay($option['first_day_week']))
			$Notice->addError(CHBSHelper::getFormName('first_day_week',false),$invalidValue);
		if($Validation->isEmpty($option['time_format']))
			$Notice->addError(CHBSHelper::getFormName('time_format',false),$invalidValue);		
		if(!in_array($option['address_format_type'],array(1,2)))
			$Notice->addError(CHBSHelper::getFormName('address_format_type',false),$invalidValue);  
		if(!in_array($option['pricing_rule_return_use_type'],array(1,2)))
			$Notice->addError(CHBSHelper::getFormName('pricing_rule_return_use_type',false),$invalidValue);  
		
		if(is_array($option['booking_status_nonblocking']))
		{
			foreach($option['booking_status_nonblocking'] as $value)
			{
				if(!$BookingStatus->isBookingStatus($value))
				{
					$Notice->addError(CHBSHelper::getFormName('booking_status_nonblocking',false),$invalidValue);	
					break;
				}
			}
		}
		else $option['booking_status_nonblocking']=array();		
		
		if($License->isVerified())
		{
			if(!$Validation->isBool($option['copyright_footer_enable']))
				$Notice->addError(CHBSHelper::getFormName('copyright_footer_enable',false),$invalidValue);		
		}
		else $option['copyright_footer_enable']=1;
		
		/* General / System */
		
		if(!$Validation->isBool($option['system_library_on_shortcode_page_enable']))
			$Notice->addError(CHBSHelper::getFormName('system_library_on_shortcode_page_enable',false),$invalidValue);			
		
		/* General / Google Maps */
		
		if($Validation->isEmpty($option['google_map_api_key']))
			$Notice->addError(CHBSHelper::getFormName('google_map_api_key',false),$invalidValue);			
		if($Validation->isEmpty($option['google_map_map_id']))
			$Notice->addError(CHBSHelper::getFormName('google_map_map_id',false),$invalidValue);	
		if(!$Validation->isBool($option['google_map_server_data_validation_enable']))
			$Notice->addError(CHBSHelper::getFormName('google_map_server_data_validation_enable',false),$invalidValue);			
		if(!$Validation->isBool($option['google_map_ask_load_enable']))
			$Notice->addError(CHBSHelper::getFormName('google_map_ask_load_enable',false),$invalidValue);			
		if(!$Validation->isBool($option['google_map_duplicate_script_remove']))
			$Notice->addError(CHBSHelper::getFormName('google_map_duplicate_script_remove',false),$invalidValue);

		/* General / reCaptcha */

		if(!$Validation->isBool($option['google_recaptcha_enable']))
			$Notice->addError(CHBSHelper::getFormName('google_recaptcha_enable',false),$invalidValue);	
		if(!in_array($option['google_recaptcha_api_type'],array(1,2)))
			$Notice->addError(CHBSHelper::getFormName('google_recaptcha_api_type',false),$invalidValue);	
		if(!$Validation->isFloat($option['google_recaptcha_score'],0,1,false,1))
			$Notice->addError(CHBSHelper::getFormName('google_recaptcha_score',false),$invalidValue);	
		if(!$Validation->isBool($option['google_recaptcha_badge_enable']))
			$Notice->addError(CHBSHelper::getFormName('google_recaptcha_badge_enable',false),$invalidValue);			
		
		/* General / wooCommerce */
		
		if(!$Validation->isBool($option['woocommerce_order_reduce_item']))
			$Notice->addError(CHBSHelper::getFormName('woocommerce_order_reduce_item',false),$invalidValue);
		if(!$Validation->isBool($option['woocommerce_template_plugin_enable']))
			$Notice->addError(CHBSHelper::getFormName('woocommerce_template_plugin_enable',false),$invalidValue);		
	
		/* Email */
		
		if(!$Validation->isBool($option['email_service_reminder_customer_enable']))
			$Notice->addError(CHBSHelper::getFormName('email_service_reminder_customer_enable',false),$invalidValue);	
		if(!$Validation->isNumber($option['email_service_reminder_customer_duration'],1,9999,true))
			$Notice->addError(CHBSHelper::getFormName('email_service_reminder_customer_duration',false),$invalidValue); 
		if(!$Validation->isBool($option['email_service_reminder_driver_enable']))
			$Notice->addError(CHBSHelper::getFormName('email_service_reminder_driver_enable',false),$invalidValue);	
		if(!$Validation->isNumber($option['email_service_reminder_driver_duration'],1,9999,true))
			$Notice->addError(CHBSHelper::getFormName('email_service_reminder_driver_duration',false),$invalidValue); 
		if(!$Validation->isBool($option['email_service_post_arrival_message_customer_enable']))
			$Notice->addError(CHBSHelper::getFormName('email_service_post_arrival_message_customer_enable',false),$invalidValue);	
		if(!$Validation->isNumber($option['email_service_post_arrival_message_customer_duration'],1,999999,true))
			$Notice->addError(CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration',false),$invalidValue); 		
		if(!in_array($option['email_service_post_arrival_message_customer_duration_unit'],array(1,2,3,4)))
			$Notice->addError(CHBSHelper::getFormName('email_service_post_arrival_message_customer_duration_unit',false),$invalidValue); 		
		
		/* Payment */
		
		if((int)$option['booking_status_payment_success']!==-1)
		{
			if(!$BookingStatus->isBookingStatus($option['booking_status_payment_success']))
				$Notice->addError(CHBSHelper::getFormName('booking_status_payment_success',false),$invalidValue);	
		}
		if(!$Validation->isBool($option['booking_status_sum_zero']))
			$Notice->addError(CHBSHelper::getFormName('booking_status_sum_zero',false),$invalidValue);	
		if(!$BookingStatus->isBookingStatusSynchronization($option['booking_status_synchronization']))
			$Notice->addError(CHBSHelper::getFormName('booking_status_synchronization',false),$invalidValue);	
		
		/* Webhooks  */
		
		if(!$Validation->isBool($option['webhook_booking_enable']))
			$Notice->addError(CHBSHelper::getFormName('webhook_booking_enable',false),$invalidValue);	
		if(!$BookingWebHook->isRunEvent($option['webhook_booking_run_event']))
			$Notice->addError(CHBSHelper::getFormName('webhook_booking_run_event',false),$invalidValue);	
		if(!$Validation->isNumber($option['webhook_booking_run_number'],0,999999,true))
			$Notice->addError(CHBSHelper::getFormName('webhook_booking_run_number',false),$invalidValue); 			
		
		/* Currency */
		
		foreach($option['currency_exchange_rate'] as $index=>$value)
		{
			if(!$Currency->isCurrency($index))
			{
				unset($option['currency_exchange_rate'][$index]);
				continue;
			}
			
			if(!CHBSPrice::isPrice($option['currency_exchange_rate'][$index]))
			{
				unset($option['currency_exchange_rate'][$index]);
				continue;				
			}
			
			$option['currency_exchange_rate'][$index]=CHBSPrice::formatToSave($option['currency_exchange_rate'][$index]);
		}
		
		/* Booking acceptance */
		
		$bookingStatusId=$option['booking_driver_acceptance_notificaction_booking_status_id'];
		
		if(!is_array($bookingStatusId)) $bookingStatusId=array();
		
		foreach($bookingStatusId as $index=>$value)
		{
			if(!$BookingStatus->isBookingStatus($value))
				unset($bookingStatusId[$index]);
		}
		
		$option['booking_driver_acceptance_notificaction_booking_status_id']=$bookingStatusId;
		
		
		if(!$Validation->isBool($option['booking_driver_accept_stage_1_enable']))
			$Notice->addError(CHBSHelper::getFormName('booking_driver_accept_stage_1_enable',false),$invalidValue);
		if(!$Validation->isBool($option['booking_driver_accept_stage_2_enable']))
			$Notice->addError(CHBSHelper::getFormName('booking_driver_accept_stage_2_enable',false),$invalidValue);		
		if(!$Validation->isNumber($option['booking_driver_acceptance_confirmation_page'],1,999999,true))
			$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_confirmation_page',false),$invalidValue); 		
		
		$recipient=preg_split('/;/',CHBSHelper::getPostValue('booking_driver_acceptance_email_recipient'));
		foreach($recipient as $index=>$value)
		{
			if(!$Validation->isEmailAddress($value,true))
			{
				$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_email_recipient',false),$invalidValue); 
				break;
			}
		} 
		
		if(!$Validation->isNumber($option['booking_driver_acceptance_stage_1_interval'],1,999999,true))
			$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_stage_1_interval',false),$invalidValue); 			
		if(!$Validation->isNumber($option['booking_driver_acceptance_stage_2_interval'],1,999999,true))
			$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_stage_2_interval',false),$invalidValue); 				
		if((int)$option['booking_driver_acceptance_status_after_accept']!==0)
		{
			if(!$BookingStatus->isBookingStatus($option['booking_driver_acceptance_status_after_accept']))
				$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_status_after_accept',false),$invalidValue);		
		}
		
		if((int)$option['booking_driver_acceptance_status_after_reject']!==0)
		{
			if(!$BookingStatus->isBookingStatus($option['booking_driver_acceptance_status_after_reject']))
				$Notice->addError(CHBSHelper::getFormName('booking_driver_acceptance_status_after_reject',false),$invalidValue);			
		}
		
		/* Booking cancelling */
		
		if(!$Validation->isBool($option['booking_cancel_enable']))
			$Notice->addError(CHBSHelper::getFormName('booking_cancel_enable',false),$invalidValue);
		if(!$Validation->isNumber($option['booking_cancel_confirmation_page'],1,999999,true))
			$Notice->addError(CHBSHelper::getFormName('booking_cancel_confirmation_page',false),$invalidValue); 		
		
		$recipient=preg_split('/;/',CHBSHelper::getPostValue('booking_cancel_email_recipient'));
		foreach($recipient as $index=>$value)
		{
			if(!$Validation->isEmailAddress($value,true))
			{
				$Notice->addError(CHBSHelper::getFormName('booking_cancel_email_recipient',false),$invalidValue); 
				break;
			}
		}
		
		$bookingStatusId=(array)CHBSHelper::getPostValue('booking_cancel_cancellable_booking_status_id');
		foreach($bookingStatusId as $bookingStatusIdValue)
		{
			if(!$BookingStatus->isBookingStatus($bookingStatusIdValue))
				$Notice->addError(CHBSHelper::getFormName('booking_cancel_cancellable_booking_status_id',false),$invalidValue);	
		}
		
		/* Webhook */
		
		if(!$Validation->isBool($option['webhook_after_sent_booking_enable']))
			$Notice->addError(CHBSHelper::getFormName('webhook_after_sent_booking_enable',false),$invalidValue);
		else
		{
			if((int)$option['webhook_after_sent_booking_enable']===1)
			{
				if($Validation->isEmpty($option['webhook_after_sent_booking_url_address']))
					$Notice->addError(CHBSHelper::getFormName('webhook_after_sent_booking_url_address',false),$invalidValue);	   				
			}
		}
		
		if($Notice->isError())
		{
			$response['local']=$Notice->getError();
		}
		else
		{
			$response['global']['error']=0;
			CHBSOption::updateOption($option);
		}

		$response['global']['notice']=$Notice->createHTML(PLUGIN_CHBS_TEMPLATE_PATH.'admin/notice.php');

		echo json_encode($response);
		exit;
	}
	
	/**************************************************************************/
	
	function importDemo()
	{
		if(CHBSHelper::verifyNonce('option_page')===false) return(false);
		
		$Demo=new CHBSDemo();
		$Notice=new CHBSNotice();
		$Validation=new CHBSValidation();
		
		$response=array('global'=>array('error'=>1));
		
		$buffer=$Demo->import();
		
		if($buffer!==false)
		{
			$response['global']['error']=0;
			$subtitle=__('Seems, that demo data has been imported. To make sure if this process has been successfully completed,please check below content of buffer returned by external applications.','chauffeur-booking-system');
		}
		else
		{
			$response['global']['error']=1;
			$subtitle=__('Dummy data cannot be imported.','chauffeur-booking-system');
		}
			
		$response['global']['notice']=$Notice->createHTML(PLUGIN_CHBS_TEMPLATE_PATH.'admin/notice.php',true,$response['global']['error'],$subtitle);
		
		if($Validation->isNotEmpty($buffer))
		{
			$response['global']['notice'].=
			'
				<div class="to-buffer-output">
					'.$buffer.'
				</div>
			';
		}
		
		echo json_encode($response);
		exit;					
	}
	
	/**************************************************************************/
	
	function adminCustomMenuOrder()
	{
		global $submenu;

		$key='edit.php?post_type=chbs_booking';
		
		$menuOrder=array
		(
			array
			(
				'chbs_booking_calendar'
			),
			array
			(
				'edit.php?post_type=chbs_booking'
			),
			array
			(
				'edit.php?post_type=chbs_booking_form'
			),
			array
			(
				'edit.php?post_type=chbs_booking_extra'
			),
			array
			(
				'edit.php?post_type=chbs_route'
			),
			array
			(
				'edit.php?post_type=chbs_vehicle'
			),
			array
			(
				'edit-tags.php?post_type=chbs_booking&taxonomy=chbs_vehicle_c'
			),
			array
			(
				'edit.php?post_type=chbs_vehicle_attr'
			),
			array
			(
				'edit.php?post_type=chbs_vehicle_company'
			),
			array
			(
				'edit.php?post_type=chbs_location'
			),
			array
			(
				'edit.php?post_type=chbs_price_rule'
			),
			array
			(
				'edit.php?post_type=chbs_av_rule'
			),
			array
			(
				'edit.php?post_type=chbs_driver'
			),
			array
			(
				'edit.php?post_type=chbs_coupon'
			),
			array
			(
				'edit.php?post_type=chbs_geofence'
			),
			array
			(
				'edit.php?post_type=chbs_tax_rate'
			),
			array
			(
				'edit.php?post_type=chbs_email_account'
			),
			array
			(
				'edit.php?post_type=chbs_currency'
			)
		);
		
		if(class_exists('CHBSECNExtension'))
		{
			$menuOrder[]=array
			(
				'edit.php?post_type=chbsecn_cn'
			);
		}
		if(class_exists('CHBSEICExtension'))
		{
			$menuOrder[]=array
			(
				'edit.php?post_type=chbseic_icalendar'
			);
		}

		if(array_key_exists($key,$submenu))
		{
			$menu=array();
			
			foreach($menuOrder as $menuOrderValue)
			{
				foreach($submenu[$key] as $submenuValue)
				{
					if($menuOrderValue[0]==$submenuValue[2])
					{
						$menu[]=$submenuValue;
					}
				}
			}

			$menu[]=array(__('Add-ons &rarr;','chauffeur-booking-system'),'manage_options','https://1.envato.market/c/296919/275988/4415?u=https://codecanyon.net/collections/11597341-add-ons-for-chauffeur-taxi-booking-system');
			
			$submenu[$key]=$menu;
		}
	}
	
	/**************************************************************************/
	
	function wpLoaded()
	{
		$Validation=new CHBSValidation();
		
		$runCode=CHBSOption::getOption('run_code');
		$cronEvent=(int)CHBSHelper::getGetValue('cron_event');
				
		if($Validation->isNotEmpty($runCode))
		{
			if((CHBSHelper::getGetValue('run_code')==$runCode))
			{
				switch($cronEvent)
				{
					case 1:

						$BookingServiceNotification=new CHBSBookingServiceNotification(1);
						$BookingServiceNotification->send();
						die();

					break;

					case 2:

						$BookingServiceNotification=new CHBSBookingServiceNotification(2);
						$BookingServiceNotification->send();
						die();

					break;

					case 3:
						
						$BookingServiceNotification=new CHBSBookingServiceNotification(3);
						$BookingServiceNotification->send();
						die();

					break;
				
					case 4:
						
						$BookingDriver=new CHBSBookingDriver();
						$BookingDriver->setBookingDriverAcceptance(1);
						die();
						
					break;
				
					case 5:
				
						$BookingDriver=new CHBSBookingDriver();
						$BookingDriver->setBookingDriverAcceptance(2);
						die();
					
					break;
				}
			}
		}

		if((CHBSHelper::getGetValue('run_code',false)==$runCode))		
		{
			$licenseStatus=CHBSHelper::getGetValue('license_status',false);

			if($licenseStatus>0)
			{
				$License=new CHBSLicense();

				if($licenseStatus<200)
				{
					$License->setAsUnVerified();
				}
				else
				{
					$License->setAsVerified(CHBSHelper::getGetValue('license_id',false),CHBSHelper::getGetValue('license_purchase_code',false),CHBSHelper::getGetValue('license_code',false),CHBSHelper::getGetValue('license_refresh_token',false),CHBSHelper::getGetValue('license_support_datetime',false));
				}
				
				$LogManager=new CHBSLogManager();
				$LogManager->add('license',1,sprintf(__('Response - status %s','chauffeur-booking-system'),$licenseStatus));   
				
				header('Location: '.admin_url('options-general.php?page=chbs#general_license'));
				exit;
			}
		}
	}
	
	/**************************************************************************/
	
	function afterSetupTheme()
	{		
		$VisualComposer=new CHBSVisualComposer();
		$VisualComposer->init();
	}
	
	/**************************************************************************/
	
	function adminNotice()
	{
		$License=new CHBSLicense();
		$Validation=new CHBSValidation();
		
		if($Validation->isEmpty(CHBSOption::getOption('google_map_api_key')))
		{
			echo 
			'
				<div class="notice notice-error is-dismissible">
					<p>
						<b>'.esc_html('Chauffeur Booking System.','chauffeur-booking-system').'</b> '.sprintf(__('Please enter your Google Maps API key in the <a href="%s">Plugin Options</a>.','chauffeur-booking-system'),admin_url('options-general.php?page=chbs',false)).'
					</p>
				</div>
			';
		}
		
		if(!$License->isVerified())
		{
			echo 
			'
				<div class="notice notice-error is-dismissible">
					<p>
						<b>'.esc_html('Chauffeur Booking System.','chauffeur-booking-system').'</b> '.sprintf(__('Your product is not verified. Please enter license details in the <a href="%s">Plugin Options</a>.','chauffeur-booking-system'),admin_url('options-general.php?page=chbs',false)).'
					</p>
				</div>
			';		
		}
		else
		{
			if(!$License->isSupportActive())
			{
				echo 
				'
					<div class="notice notice-error is-dismissible">
						<p>
							<b>'.esc_html('Chauffeur Booking System.','chauffeur-booking-system').'</b> '.sprintf(__('Your support <b>is not active</b>. <a href="%s" target="_blank">Click to renew the support</a> and get access to all features.','chauffeur-booking-system'),self::getProductEnvatoURL()).'
						</p>
					</div>
				';			
			}
		}
	}
	
	/**************************************************************************/
	
	function gutenbergBlockCategory($categories,$post)
	{
		return array_merge
		(
			$categories,
			array
			(
				array
				(
					'slug'=>'chbs-booking-form',
					'title'=>__('Chauffeur Booking System','chauffeur-booking-form'),
				)
			)
		);		
	}
	
	/**************************************************************************/
	
	function removeMultipleGoogleMap()
	{
		global $wp_scripts;
		   
		foreach($wp_scripts->queue as $handle) 
		{
			if($handle=='chbs-google-map') continue;
			
			$src=$wp_scripts->registered[$handle]->src;

			if(preg_match('/maps.google.com\/maps\/api\//',$src))
			{
				wp_dequeue_script($handle);
				wp_deregister_script($handle);	
			}
		}
	}
	
	/**************************************************************************/
	
	static function isAutoRideTheme()
	{
		$Validation=new CHBSValidation();
		
		$theme=wp_get_theme();
		$themeName=strtolower($theme->get('Name'));

		$parentTheme=is_object($theme->parent()) ? $theme->parent() : '';
		$parentThemeName=strtolower($Validation->isEmpty($parentTheme) ? '' : $parentTheme->get('Name'));

		if(($themeName=='autoride') || ($parentThemeName=='autoride')) return(true);	
		
		return(false);
	}
	
	/**************************************************************************/
	
	static function getProductEnvatoURL()
	{
		if(self::isAutoRideTheme())
		{
			return('https://themeforest.net/item/autoride-chauffeur-booking-wordpress-theme/25551220');
		}
		else
		{
			return('https://codecanyon.net/item/chauffeur-booking-system-for-wordpress/21072773');
		}
	}
	
	/**************************************************************************/
	
	static function getProductAffiliateURL()
	{
		return('https://1.envato.market/chauffeur-booking-system-for-wordpress');
	}
	
	/**************************************************************************/
	
	static function getProductId()
	{
		return(self::isAutoRideTheme() ? 25551220 : 21072773); 
	}
	
	/**************************************************************************/
	
	static function getDomain()
	{
		$url=parse_url(site_url());
		return($url['host']);
	}
	
	/**************************************************************************/
	
	static function getServerIPAddress()
	{
		return(filter_input(INPUT_SERVER,'SERVER_ADDR'));
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
