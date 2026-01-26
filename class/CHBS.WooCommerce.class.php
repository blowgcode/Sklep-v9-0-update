<?php

/******************************************************************************/
/******************************************************************************/

class CHBSWooCommerce
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function isEnable($meta)
	{
		return((class_exists('WooCommerce')) && ($meta['woocommerce_enable']) && (!$meta['price_hide']));
	}
	
	/**************************************************************************/
	
	function isPayment($paymentId,$dictionary=null)
	{
		if(is_null($dictionary)) $dictionary=$this->getPaymentDictionary();

		foreach($dictionary as $value)
		{
			if($value->{'id'}==$paymentId) return(true);
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	function getPaymentDictionary()
	{
		$dictionary=WC()->payment_gateways->payment_gateways();

		foreach($dictionary as $index=>$value)
		{
			if(!isset($value->enabled)) unset($dictionary[$index]);
			if($value->enabled!='yes') unset($dictionary[$index]);
		}
		
		return($dictionary);
	}
	
	/**************************************************************************/
	
	function getPaymentName($paymentId,$dictionary=null)
	{
		if(is_null($dictionary)) $dictionary=$this->getPaymentDictionary();
		
		foreach($dictionary as $value)
		{
			if($value->{'id'}==$paymentId) return($value->{'title'});
		}		
		
		return(null);
	}
	
	/**************************************************************************/
	
	function sendBooking($bookingId,$bookingForm,$data)
	{
		global $wpdb;
		
		$User=new CHBSUser();
		$Booking=new CHBSBooking();
		
		if(($booking=$Booking->getBooking($bookingId))===false) return(false);		
		
		$userId=0;
		
		if(!$User->isSignIn())
		{
			if(((int)$data['client_sign_up_enable']===1) || ((int)$bookingForm['meta']['woocommerce_account_enable_type']===2))
				$userId=$User->createUser($data['client_contact_detail_email_address'],$data['client_sign_up_login'],$data['client_sign_up_password']);
		}
		else
		{
			$userData=$User->getCurrentUserData();
			$userId=$userData->data->ID;
		}
		
		CHBSPostMeta::updatePostMeta($bookingId,'user_id',$userId);
		
		$address=array
		(
			'first_name'=>$booking['meta']['client_contact_detail_first_name'],
			'last_name'=>$booking['meta']['client_contact_detail_last_name'],
			'company'=>$booking['meta']['client_billing_detail_company_name'],
			'address_1'=>$booking['meta']['client_billing_detail_street_name'],
			'address_2'=>$booking['meta']['client_billing_detail_street_number'],
			'city'=>$booking['meta']['client_billing_detail_city'],
			'postcode'=>$booking['meta']['client_billing_detail_postal_code'],
			'country'=>$booking['meta']['client_billing_detail_country_code'],
			'state'=>$booking['meta']['client_billing_detail_state'],
			'phone'=>$booking['meta']['client_contact_detail_phone_number'],
			'email'=>$booking['meta']['client_contact_detail_email_address']			
		);
		
		$argument=array();
		
		$argument['customer_id']=$userId;
		
		if((int)$booking['meta']['business_user_paid']===1) $argument['status']='completed';
	
		$order=wc_create_order($argument);
		$order->set_address($address,'billing');
		$order->set_address($address,'shipping');
		$order->set_payment_method($booking['meta']['payment_id']);
		
		switch((int)$booking['meta']['booking_status_id'])
		{			
			case 3:
				
				$order->update_status('cancelled');
				
			break;
		
			default:
				
				$order->update_status('pending');
		}		

		$order->set_currency($booking['meta']['currency_id']);
		
		update_post_meta($order->get_id(),PLUGIN_CHBS_CONTEXT.'_booking_id',$bookingId);
		
		CHBSPostMeta::updatePostMeta($bookingId,'woocommerce_booking_id',$order->get_id());
		
		if($userId>0) 
		{
			$userData=array
			(
				'ID'=>$userId,
				'first_name'=>$booking['meta']['client_contact_detail_first_name'],
				'last_name'=>$booking['meta']['client_contact_detail_last_name']
			);
			
			wp_update_user($userData); 
			
			update_post_meta($order->get_id(),'_customer_user',$userId);
		
			foreach($address as $index=>$value) 
			{
				update_user_meta($userId,'billing_'.$index,$value);
				update_user_meta($userId,'shipping_'.$index,$value);
			}		  
		}
			
		/***/

		$billing=$Booking->createBilling($bookingId);
		
		if((int)CHBSOption::getOption('woocommerce_order_reduce_item')===1)
			$billing['detail']=$billing['reduced'];
		
		if(isset($billing['detail']))
		{
			$productId=array();
			
			/***/
			
			foreach($billing['detail'] as $detail)
			{
				if($detail['visible']!=1) continue;
				
				$product=$this->prepareProduct
				(
					array
					(
						'post'=>array
						(
							'post_title'=>$detail['name']
						),
						'meta'=>array
						(
							'chbs_price_gross'=>$detail['value_gross'],
							'chbs_tax_value'=>$detail['tax_value'],
							'_price'=>$detail['value_net']
						)
					)
				);
				
				$productId[]=$this->createProduct($product);
				$order->add_product(wc_get_product(end($productId)));
			}
			
			$order->calculate_totals();
				
			/***/
		
			$query=$wpdb->prepare('delete from '.$wpdb->prefix.'woocommerce_order_items where order_id=%d and order_item_type=%s',$order->get_id(),'tax');
			$wpdb->query($query);
			
			/***/
			
			$taxRateId=1;
			$orderItem=$order->get_items();
				
			/***/
			
			$i=0;
			$taxArray=array();
			foreach($orderItem as $item)
			{
				$priceNet=get_post_meta($item->get_product_id(),'_price',true);
				$priceGross=get_post_meta($item->get_product_id(),'chbs_price_gross',true);
				$taxValue=number_format(get_post_meta($item->get_product_id(),'chbs_tax_value',true),2,'.','');
				$taxAmount=round($priceGross-$priceNet,2);
				$taxLabel=sprintf(__('Tax %.2f','chauffeur-booking-system'),$taxValue).'%';
				
				if(!isset($taxArray[$taxValue]))
				{
					$query=$wpdb->prepare('insert into '.$wpdb->prefix.'woocommerce_order_items(order_item_name,order_item_type,order_id) VALUES (%s,%s,%d)',array('TAX-'.$taxValue,'tax',$order->get_id()));
					$wpdb->query($query);

					$taxItemId=$wpdb->insert_id;

					$taxArray[$taxValue]=array
					(
						'taxItemId'=>$taxItemId,
						'rate_id'=>$taxRateId,
						'label'=>$taxLabel,
						'compound'=>'',
						'tax_amount'=>$taxAmount,
						'shipping_tax_amount'=>0,
					);
					
					wc_add_order_item_meta($taxArray[$taxValue]['taxItemId'],'rate_id',$taxArray[$taxValue]['rate_id']);
					wc_add_order_item_meta($taxArray[$taxValue]['taxItemId'],'label',$taxArray[$taxValue]['label']);
					wc_add_order_item_meta($taxArray[$taxValue]['taxItemId'],'compound',$taxArray[$taxValue]['compound']);
					wc_add_order_item_meta($taxArray[$taxValue]['taxItemId'],'tax_amount',$taxArray[$taxValue]['tax_amount']);
					wc_add_order_item_meta($taxArray[$taxValue]['taxItemId'],'shipping_tax_amount',$taxArray[$taxValue]['shipping_tax_amount']);
				}
				else
				{
					$taxArray[$taxValue]['tax_amount']+=$taxAmount;
					wc_update_order_item_meta($taxArray[$taxValue]['taxItemId'],'tax_amount',$taxArray[$taxValue]['tax_amount']);		
				}
				
				$taxData=array
				(
					'total'=>array
					(
						$taxArray[$taxValue]['rate_id']=>(string)$taxAmount,
					),
					'subtotal'=>array
					(
						$taxArray[$taxValue]['rate_id']=>(string)$taxAmount,
					)
				);
								
				wc_update_order_item_meta($item->get_id(),'_line_subtotal_tax',$taxAmount);
				wc_update_order_item_meta($item->get_id(),'_line_tax',$taxAmount);
				wc_update_order_item_meta($item->get_id(),'_line_tax_data',$taxData);
						
				$taxRateId++;
			}
					
			update_post_meta($order->get_id(),'_order_tax',$billing['summary']['value_gross']-$billing['summary']['value_net']);
			update_post_meta($order->get_id(),'_order_total',$billing['summary']['value_gross']);
					
			foreach($productId as $value) wp_delete_post($value);
			
			$order->set_total($billing['summary']['value_gross']);
			$order->save();
			
			return($order->get_checkout_payment_url());
		}
		
		return(null);
	}
	
	/**************************************************************************/
	
	function prepareProduct($data)
	{
 		$argument=array
		(
			'post'=>array
			(
				'post_title'=>'',
				'post_content'=>'',
				'post_status'=>'publish',
				'post_type'=>'product',
			),
			'meta'=>array
			(
				'chbs_price_gross'=>0,
				'chbs_tax_value'=>0,
				'_visibility'=>'visible',
				'_stock_status'=>'instock',
				'_downloadable'=>'no',
				'_virtual'=>'yes',
				'_regular_price'=>0,
				'_sale_price'=>0,
				'_purchase_note'=>'',
				'_featured'=>'no',
				'_weight'=>'',
				'_length'=>'',
				'_width'=>'',
				'_height'=>'',
				'_sku'=>'',
				'_product_attributes'=>array(),
				'_sale_price_dates_from'=>'',
				'_sale_price_dates_to'=>'',
				'_price'=>0,
				'_sold_individually'=>'',
				'_manage_stock'=>'no',
				'_backorders'=>'no',
				'_stock'=>'',
				'total_sales'=>'0',
			),
		);
		
		if(isset($data['post']))
		{
			foreach($data['post'] as $index=>$value)
				$argument['post'][$index]=$value;
		}
		
		if(isset($data['meta']))
		{
			foreach($data['meta'] as $index=>$value)
				$argument['meta'][$index]=$value;
		}		
		
		return($argument);	   
	}
	
	/**************************************************************************/
	
	function createProduct($data)
	{
		$productId=wp_insert_post($data['post']);
		wp_set_object_terms($productId,'simple','product_type');
		foreach($data['meta'] as $key=>$value)
			update_post_meta($productId,$key,$value);
		return($productId);
	}
	
	/**************************************************************************/
	
	function locateTemplate($template,$templateName,$templatePath) 
	{
		global $woocommerce;
	   
		$templateTemp=$template;
		if(!$templatePath) $templatePath=$woocommerce->template_url;
 
		$pluginPath=PLUGIN_CHBS_PATH.'woocommerce/';
	 
		$template=locate_template(array($templatePath.$templateName,$templateName));
 
		if((!$template) && (file_exists($pluginPath.$templateName)))
			$template=$pluginPath.$templateName;
 
		if(!$template) $template=$templateTemp;
   
		return ($template);
	}
	
	/**************************************************************************/
	
	function getUserData()
	{
		$userData=array();
		$userCurrent=wp_get_current_user();
		
		$Country=new WC_Countries();
		$Customer=new WC_Customer($userCurrent->ID);
		
		$billingAddress=$Customer->get_billing();
		
		$userData['client_contact_detail_first_name']=$userCurrent->user_firstname;
		$userData['client_contact_detail_last_name']=$userCurrent->user_lastname;
		$userData['client_contact_detail_email_address']=$userCurrent->user_email;
		$userData['client_contact_detail_phone_number']=$billingAddress['phone'];
		
		$userData['client_billing_detail_enable']=1;
		$userData['client_billing_detail_company_name']=$billingAddress['company'];
		$userData['client_billing_detail_tax_number']=null;
		$userData['client_billing_detail_street_name']=$billingAddress['address_1'];
		$userData['client_billing_detail_street_number']=$billingAddress['address_2'];
		$userData['client_billing_detail_city']=$billingAddress['city'];
		$userData['client_billing_detail_state']=null;
		$userData['client_billing_detail_postal_code']=$billingAddress['postcode'];
		$userData['client_billing_detail_country_code']=$billingAddress['country'];
		
		$state=$billingAddress['state'];
		$country=$billingAddress['country'];
		
		$countryState=$Country->get_states();
		
		if((isset($countryState[$country])) && (isset($countryState[$country][$state])))
			$userData['client_billing_detail_state']=$countryState[$country][$state];
		
		return($userData);
	}
	
	/**************************************************************************/
	
	function getPaymentURLAddress($bookingId)
	{
		$order=wc_get_order($bookingId);
		
		if($order!==false)
			return($order->get_checkout_payment_url());
		
		return(null);
	}
	
	/**************************************************************************/
	
	function addAction()
	{
		add_action('woocommerce_order_status_changed',array($this,'changeStatus'));
		add_action('woocommerce_email_customer_details',array($this,'createOrderEmailMessageBody'));
		
		add_action('woocommerce_view_order',array($this,'viewOrder'));
		
		add_action('add_meta_boxes',array($this,'addMetaBox'));
		
		add_action('wpo_wcpdf_order_items_data',array($this,'changeOrderItemsData'),10,4);
		
		add_filter('postbox_classes_product_chbs_meta_box_woocommerce_product',array($this,'adminCreateMetaBoxClass'));
		
		add_action('before_delete_post',array($this,'removeProduct'));
		
		add_action('woocommerce_order_item_meta_end',array($this,'displayOrderItemMeta'),10,4);
		
		add_action('woocommerce_after_cart_item_name',array($this,'displayCartItemMeta'),10,4);
		
		add_filter('current_screen',array($this,'disableBookingEdit'),100,3);
	}
	
	/**************************************************************************/
	
	function changeOrderItemsData($data_list,$this_order)
	{
		if(count($data_list)>=1)
		{
			$meta=CHBSPostMeta::getPostMeta($this_order->get_id());

			if(array_key_exists('booking_id',$meta))
			{
				$Date=new CHBSDate();
				$Booking=new CHBSBooking();
				
				if(($booking=$Booking->getBooking($meta['booking_id']))===false) return($data_list);

				$pickupLocation=null;
				$dropoffLocation=null;
				
				$i=0;
				foreach($booking['meta']['coordinate'] as $index=>$value)
				{
					if($i===0) $pickupLocation=$value['address'];
					if($i===count($booking['meta']['coordinate'])-1) $dropoffLocation=$value['address'];
					
					$i++;
				}
				
				$pickupDateTime=$Date->formatDateToDisplay($booking['meta']['pickup_date']).' '.$Date->formatTimeToDisplay($booking['meta']['pickup_time']);
				
				$key=key($data_list);
				
				$data_list[$key]['name'].='<br/>'.__('Pickup date: ','chauffer-booking-system').$pickupDateTime;
				$data_list[$key]['name'].='<br/>'.__('Pickup location: ','chauffer-booking-system').$pickupLocation;
				$data_list[$key]['name'].='<br/>'.__('Drop-off location: ','chauffer-booking-system').$dropoffLocation;
			}
		}

		return($data_list);
	}
	
	/**************************************************************************/
	
	function addMetaBox()
	{
		global $post;
		
		switch($post->post_type)
		{
			case 'shop_order':

				$meta=CHBSPostMeta::getPostMeta($post);

				if((is_array($meta)) && (array_key_exists('booking_id',$meta)) && ($meta['booking_id']>0))
				{
					add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_woocommerce_order',__('Booking','chauffeur-booking-system'),array($this,'addMetaBoxWooCommerceBooking'),'shop_order','side','low');		
				}
				
			break;
			
			case 'product':
				
				$meta=CHBSPostMeta::getPostMeta($post);
				if((is_array($meta)) && (array_key_exists('booking_id',$meta)) && ($meta['booking_id']>0))
				{
					add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_woocommerce_product',__('Booking information','chauffeur-booking-system'),array($this,'addMetaBoxWooCommerceProduct'),'product','normal','low');		
				}				
				
			break;
		}
	}
	
	/**************************************************************************/
	
	function addMetaBoxWooCommerceBooking()
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		echo 
		'
			<div>
				<div>'.esc_html__('This order has corresponding booking from "Chauffeur Booking System" plugin. Click on button below to see its details in new window.','chauffeur-booking-system').'</div>
				<br/>
				<a class="button button-primary" href="'.esc_url(get_edit_post_link($meta['booking_id'])).'" target="_blank">'.esc_html__('Open booking','chauffeur-booking-system').'</a>
			</div>
		';
	}
	
	/**************************************************************************/
	
	function addMetaBoxWooCommerceProduct()
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		if((is_array($meta)) && (array_key_exists('booking_id',$meta)) && ($meta['booking_id']>0))
		{
			$Booking=new CHBSBooking();
		
			$data=$Booking->getBooking($meta['booking_id']);

			$data['billing']=$Booking->createBilling($meta['booking_id']);

			$data['mode']='product';

			$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_booking.php');
			echo $Template->output();
		}
	}
	
	/**************************************************************************/
	
	function adminCreateMetaBoxClass($class)
	{
		array_push($class,'to-postbox-1');
		return($class);
	}
	
	/**************************************************************************/
	
	function changeStatus($orderId=-1,$bookingId=-1,&$emailSend=false)
	{
		$Booking=new CHBSBooking();
		
		$bookingStatusSynchronizationId=(int)CHBSOption::getOption('booking_status_synchronization');
		
		if($bookingStatusSynchronizationId===1) return(false);
		
		if(!class_exists('WC_Order')) return(false);
			
		/***/
		
		$BookingStatus=new CHBSBookingStatus();
		
		if((int)$orderId!==-1)
		{
			$orderMeta=CHBSPostMeta::getPostMeta($orderId);		
			if((array_key_exists('booking_id',$orderMeta)) && ($orderMeta['booking_id']>0))
				$bookingId=(int)$orderMeta['booking_id'];
		}
		elseif((int)$bookingId!==-1)
		{
			if(($booking=$Booking->getBooking($bookingId))!==false) 		
			{
				if((array_key_exists('woocommerce_booking_id',$booking['meta'])) && ($booking['meta']['woocommerce_booking_id']>0))
					$orderId=$booking['meta']['woocommerce_booking_id'];
			}
		}
		
		/***/
		
		if((int)$bookingStatusSynchronizationId===2)
		{
			if($bookingId!=-1)
			{
				$order=new WC_Order($orderId);
				
				$status=$BookingStatus->mapBookingStatus($order->get_status());
				
				if($status!==false) 
				{	
					$bookingOld=$Booking->getBooking($bookingId);
					
					CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',$status);
					
					$bookingNew=$Booking->getBooking($bookingId);
					
					$Booking->sendEmailBookingChangeStatus($bookingOld,$bookingNew);
					
					$GoogleCalendar=new CHBSGoogleCalendar();
					$GoogleCalendar->sendBooking($bookingId,false,'after_booking_status_change');
					
					$emailSend=true;
				}
			}
		}
		else if((int)$bookingStatusSynchronizationId===3)
		{
			if($orderId!=-1)
			{
				$Booking=new CHBSBooking();
				if(($booking=$Booking->getBooking($bookingId))!==false) 
				{
					$status=$BookingStatus->mapBookingStatus($booking['meta']['booking_status_id']);
					if($status!==false)
					{
						$order=new WC_Order($orderId);
						$order->update_status($status);
					}
				}
			}			
		}
	}
	
	/**************************************************************************/
	
	function createOrderEmailMessageBody($order,$sent_to_admin=false)
	{
		if(!($order instanceof WC_Order)) return(''); 
		
		$Email=new CHBSEmail();
		$Booking=new CHBSBooking();
		
		$meta=CHBSPostMeta::getPostMeta($order->get_id());
		
		$bookingId=(int)$meta['booking_id'];
		
		if($bookingId<=0) return;
		
		if(($booking=$Booking->getBooking($bookingId))===false) return;
		
		$data=array();
		
		$data['style']=$Email->getEmailStyle();
		
		$data['booking']=$booking;
		$data['booking']['billing']=$Booking->createBilling($bookingId);
		$data['booking']['booking_title']=$booking['post']->post_title;
		
		$data['document_header_exclude']=1;
				
		if(!$sent_to_admin)
			unset($data['booking']['booking_form_name']);
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'email_booking.php');
		$body=$Template->output();
		
		echo $body;
	}
	
	/**************************************************************************/
	
	function viewOrder($orderId)
	{	
		$orderMeta=CHBSPostMeta::getPostMeta($orderId);
			
		if((array_key_exists('booking_id',$orderMeta)) && ($orderMeta['booking_id']>0))
		{
			$bookingId=(int)$orderMeta['booking_id'];
			
			$Booking=new CHBSBooking();
			if(($booking=$Booking->getBooking($bookingId))!==false)
			{
				$data=array();
				
				$data['booking']=$booking;
				$data['booking']['billing']=$Booking->createBilling($bookingId);
		
				$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'public/woocommerce_view_order.php');
				echo $Template->output();			
			}
		}
	}
	
	/**************************************************************************/
	
	function isAddToCartEnable($bookingForm)
	{
		$b=array_fill(0,3,false);
		
		$b[0]=class_exists('WooCommerce') ? true : false;
		$b[1]=(int)$bookingForm['meta']['woocommerce_add_to_cart_enable']===1 ? true : false;
		
		if(class_exists('WooCommerce'))
			$b[2]=CHBSGlobalData::getGlobalData('currency_id')===get_woocommerce_currency() ? true : false;
		
		$b[2]=true;
		
		if(!in_array(false,$b,true)) return(true);
		
		return(false);
	}
	
	/**************************************************************************/
	
	function addToCart($booking,$bookingForm)
	{
		if(!$this->isAddToCartEnable($bookingForm)) return(false);
		
		/***/
		
		$productName=$this->getProductAddToCartName($booking);
		
		/***/

		$product=new WC_Product_Simple();

		$product->set_name($productName);

		$product->set_sold_individually(true);
		$product->set_catalog_visibility('hidden');
		$product->set_regular_price($booking['billing']['summary']['value_gross']); 

		$productId=$product->save();
		
		/***/
		
		CHBSPostMeta::updatePostMeta($productId,'booking_id',$booking['post']->ID);
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'woocommerce_product_id',$productId);
		
		$this->setProductCategoryAddToCart($booking,$productId);
		
		/***/
		
		WC()->cart->add_to_cart($productId);
		
		/***/
		
		$response=array();
		
		$response['step']=5;
		$response['redirect_to_woocommerce_cart_enable']=1;
		$response['redirect_to_woocommerce_cart_url_address']=wc_get_cart_url();
		
		CHBSHelper::createJSONResponse($response);
	}
	
	/**************************************************************************/
	
	function setProductCategoryAddToCart($booking,$productId)
	{
		$Vehicle=new CHBSVehicle();
		
		/***/
		
		$vehicleId=$booking['meta']['vehicle_id'];
		
		$vehicleDictionary=$Vehicle->getDictionary();
		
		if(!array_key_exists($vehicleId,$vehicleDictionary)) return(false);
		
		/***/
		
		$vehicleCategory=get_the_terms($vehicleId,CHBSVehicle::getCPTCategoryName());
		
		if(!is_array($vehicleCategory)) return(false);
		
		/***/
		
		$wooCommerceCategory=get_terms(array('taxonomy'=>'product_cat','hide_empty'=>false));
		
		if(!is_array($wooCommerceCategory)) return(false);
		
		/***/
		
		$productCategory=array();
		
		foreach($vehicleCategory as $vehicleCategoryData)
		{
			foreach($wooCommerceCategory as $wooCommerceCategoryData)
			{
				if($vehicleCategoryData->slug==$wooCommerceCategoryData->slug)
				{
					$productCategory[]=$wooCommerceCategoryData->term_id;
				}
			}
		}

		if(count($productCategory))
		{
			array_unique($productCategory);
			wp_set_object_terms($productId,$productCategory,'product_cat');
		}
		
		return(true);
	}
	
	/**************************************************************************/
	
	function getProductAddToCartName($booking)
	{
		$Date=new CHBSDate();
		$Validation=new CHBSValidation();
		$TransferType=new CHBSTransferType();
		
		/***/
		
		$location=CHBSBookingHelper::getBookingLocation($booking);
		
		/***/
		
		$productName=sprintf(__(' A "%s" transfer from "%s"','chauffeur-booking-system'),$TransferType->getTransferTypeName($booking['meta']['transfer_type_id']),$location['pickup']);
		
		if($Validation->isNotEmpty($location['dropoff']))
			$productName.=sprintf(__(' to "%s"','chauffeur-booking-system'),$location['dropoff']);
		
		$productName.=sprintf(__(', on "%s"','chauffeur-booking-system'),$Date->formatDateToDisplay($booking['meta']['pickup_date']).' '.$Date->formatTimeToDisplay($booking['meta']['pickup_time']));
		
		if(in_array($booking['meta']['service_type_id'],array(1,3)))
		{
			if((int)$booking['meta']['transfer_type_id']===3)
			{
				$productName.=sprintf(__(', return on "%s"','chauffeur-booking-system'),$Date->formatDateToDisplay($booking['meta']['return_date']).' '.$Date->formatTimeToDisplay($booking['meta']['return_time']));
			}
		}
		
		/***/

		return($productName);
	}
	
	/**************************************************************************/
	
	function removeProduct($postId)
	{
		$productMeta=CHBSPostMeta::getPostMeta($postId);
			
		if((array_key_exists('booking_id',$productMeta)) && ($productMeta['booking_id']>0))
		{
			wp_delete_post($productMeta['booking_id'],true);
		}
	}
	
	/**************************************************************************/
	
	function displayOrderItemMeta($item_id,$item,$order)
	{
		$this->displayItemMeta($item->get_product_id());
	}
	
	/**************************************************************************/
	
	function displayCartItemMeta($cart_item,$cart_item_key)
	{
		$this->displayItemMeta($cart_item['product_id']);
	}
	
	/**************************************************************************/
	
	function displayItemMeta($productId)
	{
		$html=null;
		
		$productMeta=CHBSPostMeta::getPostMeta($productId);
		
		if((array_key_exists('booking_id',$productMeta)) && ($productMeta['booking_id']>0))
		{
			$Booking=new CHBSBooking();
			if(($booking=$Booking->getBooking($productMeta['booking_id']))!==false)
			{
				$bookingBilling=$Booking->createBilling($productMeta['booking_id']);
				
				/***/
				
				$html=null;
				
				$Date=new CHBSDate();
				$Validation=new CHBSValidation();
				$ServiceType=new CHBSServiceType();
				$TransferType=new CHBSTransferType();
				
				$location=CHBSBookingHelper::getBookingLocation($booking);
				
				/***/
				
				$html.=$this->getItemMetaLine(__('Pickup location: ','chauffeur-booking-system'),$location['pickup']);
				
				if(count($location['waypoint']))
					$html.=$this->getItemMetaLine(__('Waypoints: ','chauffeur-booking-system'),join(' - ',$location['waypoint']));
				
				if($Validation->isNotEmpty($location['dropoff']))
					$html.=$this->getItemMetaLine(__('Drop-off location: ','chauffeur-booking-system'),$location['dropoff']);
					
				$html.=$this->getItemMetaLine(__('Pickup date, time: '),$Date->formatDateToDisplay($booking['meta']['pickup_date']).' '.$Date->formatTimeToDisplay($booking['meta']['pickup_time']));	
				
				if(in_array($booking['meta']['service_type_id'],array(1,3)))
				{
					if((int)$booking['meta']['transfer_type_id']===3)
					{
						$html.=$this->getItemMetaLine(__('Return date, time: ','chauffeur-booking-system'),$Date->formatDateToDisplay($booking['meta']['return_date']).' '.$Date->formatTimeToDisplay($booking['meta']['return_time']));	
					}
				}	
				
				if($booking['meta']['passenger_enable']==1)
				{
					$passengerNumber=CHBSBookingHelper::getPassengerLabel($booking['meta']['passenger_adult_number'],$booking['meta']['passenger_children_number'],1,$booking['meta']['passenger_use_person_label']);
					$html.=$this->getItemMetaLine(__('Passengers: ','chauffeur-booking-system'),$passengerNumber);
				}
				
				$html.=$this->getItemMetaLine(__('Service type: ','chauffeur-booking-system'),$ServiceType->getServiceTypeName($booking['meta']['service_type_id']));
				$html.=$this->getItemMetaLine(__('Transfer type: ','chauffeur-booking-system'),$TransferType->getTransferTypeName($booking['meta']['transfer_type_id']));
				$html.=$this->getItemMetaLine(__('Vehicle name: ','chauffeur-booking-system'),$booking['meta']['vehicle_name']);
				
				$html.=$this->getItemMetaLine(__('Duration: ','chauffeur-booking-system'),$bookingBilling['summary']['duration_s2']);
				$html.=$this->getItemMetaLine(__('Distance: ','chauffeur-booking-system'),$bookingBilling['summary']['distance_s2']);
				
				if((is_array($booking['meta']['booking_extra'])) && (count($booking['meta']['booking_extra'])))
				{
					$htmlTemp=null;
					
					foreach($booking['meta']['booking_extra'] as $index=>$value)
					{
						if($Validation->isNotEmpty($htmlTemp)) $htmlTemp.=', ';
						$htmlTemp.=$value['name'].' x '.$value['quantity'];
					}
					
					$html.=$this->getItemMetaLine(__('Extras: '),$htmlTemp);
				}
				
				/***/
				
				$html='<ul class="chbs-product-meta-list">'.$html.'</ul>';
			}
		}
		
		echo $html;
	}
	
	/**************************************************************************/
	
	function getItemMetaLine($label,$value)
	{
		$html=
		'
			<li>
				<span>'.esc_html($label).'</span>
				<span>'.esc_html($value).'</span>
			</li>
		';
		
		return($html);
	}
					
	/**************************************************************************/
	
	function disableBookingEdit()
	{
		$screen=get_current_screen();
		
		if(($screen->post_type==='chbs_booking'))
		{
			$bookingId=(int)CHBSHelper::getGetValue('post',false);
			if($bookingId>0)
			{
				$Booking=new CHBSBooking();
				if(($booking=$Booking->getBooking($bookingId))!==false)
				{
					if($booking['meta']['woocommerce_product_id']>0)
					{
						wp_redirect(admin_url('edit.php?post_type='.CHBSBooking::getCPTName()));
						exit();						
					}
				}
			}
		}
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/