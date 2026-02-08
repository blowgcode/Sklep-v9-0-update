<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPaymentStripe
{
	/**************************************************************************/
	
	public $event;
	public $apiVersion;
	public $paymentMethod;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->apiVersion='2020-08-27';
		
		$this->paymentMethod=array
		(
			'alipay'=>array(__('Alipay','chauffeur-booking-system')),
			'card'=>array(__('Cards','chauffeur-booking-system')),			
			'ideal'=>array(__('iDEAL','chauffeur-booking-system')),
			'fpx'=>array(__('FPX','chauffeur-booking-system')),
			'bacs_debit'=>array(__('Bacs Direct Debit','chauffeur-booking-system')),
			'bancontact'=>array(__('Bancontact','chauffeur-booking-system')),
			'giropay'=>array(__('Giropay','chauffeur-booking-system')),
			'p24'=>array(__('Przelewy24','chauffeur-booking-system')),
			'eps'=>array(__('EPS','chauffeur-booking-system')),
			'sofort'=>array(__('Sofort','chauffeur-booking-system')),
			'sepa_debit'=>array(__('SEPA Direct Debit','chauffeur-booking-system'))
		);
		
		$this->event=array
		(
			'payment_intent.canceled',
			'payment_intent.created',
			'payment_intent.payment_failed',
			'payment_intent.processing',
			'payment_intent.requires_action',
			'payment_intent.succeeded',
			'payment_method.attached'
		);
		
		asort($this->paymentMethod);
	}
	
	/**************************************************************************/
	
	function getPaymentMethod()
	{
		return($this->paymentMethod);
	}
	
	/**************************************************************************/
	
	function isPaymentMethod($paymentMethod)
	{
		return(array_key_exists($paymentMethod,$this->paymentMethod) ? true : false);
	}
	
	/**************************************************************************/
	
	function getWebhookEndpointUrlAdress()
	{
		$address=add_query_arg('action','payment_stripe',home_url().'/');
		return($address);
	}
	
	/**************************************************************************/
	
	function createWebhookEndpoint($bookingForm)
	{
		$StripeClient=new \Stripe\StripeClient(['api_key'=>$bookingForm['meta']['payment_stripe_api_key_secret'],'stripe_version'=>$this->apiVersion]);
		
		$webhookEndpoint=$StripeClient->webhookEndpoints->create(['url'=>$this->getWebhookEndpointUrlAdress(),'enabled_events'=>$this->event]);		
		
		CHBSOption::updateOption(array('payment_stripe_webhook_endpoint_id'=>$webhookEndpoint->id));
	}
	
	/**************************************************************************/
	
	function updateWebhookEndpoint($bookingForm,$webhookEndpointId)
	{
		$StripeClient=new \Stripe\StripeClient(['api_key'=>$bookingForm['meta']['payment_stripe_api_key_secret'],'stripe_version'=>$this->apiVersion]);
		
		$StripeClient->webhookEndpoints->update($webhookEndpointId,['url'=>$this->getWebhookEndpointUrlAdress()]);
	}
	
	/**************************************************************************/
	
	function createSession($booking,$bookingBilling,$bookingForm)
	{
		try
		{
			\Stripe\Stripe::setApiVersion($this->apiVersion);
			
			$Validation=new CHBSValidation();

			$currentURLAddress=home_url();

			/***/

			Stripe\Stripe::setApiKey($bookingForm['meta']['payment_stripe_api_key_secret']);

			/***/

			$webhookEndpointId=CHBSOption::getOption('payment_stripe_webhook_endpoint_id');

			if($Validation->isEmpty($webhookEndpointId)) $this->createWebhookEndpoint($bookingForm);
			else
			{
				try
				{
					$this->updateWebhookEndpoint($bookingForm,$webhookEndpointId);
				} 
				catch (Exception $ex) 
				{
					$this->createWebhookEndpoint($bookingForm);
				}
			}

			/***/

			$productId=$bookingForm['meta']['payment_stripe_product_id'];

			if($Validation->isEmpty($productId))
			{
				$product=\Stripe\Product::create(
				[
					'name'=>sprintf(__('Chauffeur services - %s','chauffeur-booking-system'),$booking['post']->post_title)
				]);		

				$productId=$product->id;

				CHBSPostMeta::updatePostMeta($bookingForm['post']->ID,'payment_stripe_product_id',$productId);
			}

			/***/

			$price=\Stripe\Price::create(
			[
				'product'=>$productId,
				'unit_amount'=>$bookingBilling['summary']['pay']*100,
				'currency'=>$booking['meta']['currency_id'],
			]);

			/***/

			if($Validation->isEmpty($bookingForm['meta']['payment_stripe_success_url_address']))
				$bookingForm['meta']['payment_stripe_success_url_address']=$currentURLAddress;
			if($Validation->isEmpty($bookingForm['meta']['payment_stripe_cancel_url_address']))
				$bookingForm['meta']['payment_stripe_cancel_url_address']=$currentURLAddress;

			$session=\Stripe\Checkout\Session::create
			(
				[
					'payment_method_types'=>$bookingForm['meta']['payment_stripe_method'],
					'mode'=>'payment',
					'line_items'=>
					[
						[
							'price'	=>$price->id,
							'quantity'=>1
						]
					],
					'success_url'=>$bookingForm['meta']['payment_stripe_success_url_address'],
					'cancel_url'=>$bookingForm['meta']['payment_stripe_cancel_url_address']
				]		
			);

            CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_stripe_intent_id',$session->payment_intent);

			return($session->id);
		}
  		catch(Exception $ex) 
		{
			$LogManager=new CHBSLogManager();
			$LogManager->add('stripe',1,$ex->__toString());	
			return(false);
		}
	}
	
	/**************************************************************************/
	
	function receivePayment()
	{
		$LogManager=new CHBSLogManager();
		
		if(!array_key_exists('action',$_REQUEST)) return(false);
		
		if($_REQUEST['action']=='payment_stripe')
		{
			$LogManager->add('stripe',2,__('[1] Receiving a payment.','chauffeur-booking-system'));	
			
			global $post;
			
			$event=null;
			$content=@file_get_contents('php://input');
	
			try 
			{
				$event=\Stripe\Event::constructFrom(json_decode($content,true));
			} 
			catch(\UnexpectedValueException $e) 
			{
				$LogManager->add('stripe',2,__('[2] Error during parsing data in JSON format.','chauffeur-booking-system'));	
				http_response_code(400);
				exit();
			}	
			
			if(in_array($event->type,$this->event))
			{
				$LogManager->add('stripe',2,__('[4] Checking a booking.','chauffeur-booking-system'));	
			
				$Booking=new CHBSBooking();
				$BookingForm=new CHBSBookingForm();				
				$BookingStatus=new CHBSBookingStatus();
				
				$argument=array
				(
					'post_type'=>CHBSBooking::getCPTName(),
					'posts_per_page'=>-1,
					'meta_query'=>array
					(
						array
						(
							'key'=>PLUGIN_CHBS_CONTEXT.'_payment_stripe_intent_id',
							'value'=>$event->data->object->id
						)					  
					)
				);
				
				CHBSHelper::preservePost($post,$bPost);
				
				$query=new WP_Query($argument);
				if($query!==false) 
				{
					if($query->found_posts)
					{
						$LogManager->add('stripe',2,sprintf(__('[6] Booking %s is found.','chauffeur-booking-system'),$event->data->object->id));	
						
						while($query->have_posts())
						{
							$query->the_post();

							$meta=CHBSPostMeta::getPostMeta($post);

							if(!array_key_exists('payment_stripe_data',$meta)) $meta['payment_stripe_data']=array();

							$meta['payment_stripe_data'][]=$event;

							CHBSPostMeta::updatePostMeta($post->ID,'payment_stripe_data',$meta['payment_stripe_data']);

							$LogManager->add('stripe',2,__('[7] Updating a booking about transaction details.','chauffeur-booking-system'));
							
							if($event->type=='payment_intent.succeeded')
							{
								if(CHBSOption::getOption('booking_status_payment_success')!=-1)
								{
									if($BookingStatus->isBookingStatus(ChBSOption::getOption('booking_status_payment_success')))
									{
										$LogManager->add('stripe',2,__('[11] Updating booking status.','chauffeur-booking-system'));	
										
										CHBSBookingHelper::paymentSuccess($post->ID);
									}
									else
									{
										$LogManager->add('stripe',2,__('[10] Cannot find a valid booking status.','chauffeur-booking-system'));	
									}
								}
								else
								{
									$LogManager->add('stripe',2,__('[9] Changing status of the booking after successful payment is off.','chauffeur-booking-system'));	
								}
							}
							else
							{
								$LogManager->add('stripe',2,sprintf(__('[8] Event %s is not supported.','chauffeur-booking-system'),$event->type));	
							}

							break;
						}
					}
					else
					{
						$LogManager->add('stripe',2,sprintf(__('[5] Booking %s is not found.','chauffeur-booking-system'),$event->data->object->id));	
					}
				}
			
				CHBSHelper::preservePost($post,$bPost,0);
			}
			else 
			{
				$LogManager->add('stripe',2,sprintf(__('[3] Event %s is not supported.','chauffeur-booking-system'),$event->type));	
			}
		
			http_response_code(200);
			exit();
		}
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/