<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPaymentTpay
{
	/**************************************************************************/
	
	const PAYMENT_ID=6;
	
	/**************************************************************************/
	
	private static $autoloadInitialized=false;
	private static $hooksInitialized=false;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->registerAutoloader();
		
		if(!self::$hooksInitialized)
		{
			self::$hooksInitialized=true;
			
			add_filter('chbs_payment_prepare_request_data',array($this,'preparePayment'),10,6);
			add_filter(PLUGIN_CHBS_CONTEXT.'_booking_payment_transaction',array($this,'renderTransactionDetails'),10,2);
		}
	}
	
	/**************************************************************************/
	
	private function registerAutoloader()
	{
		if(self::$autoloadInitialized) return;
		
		self::$autoloadInitialized=true;
		
		spl_autoload_register(function($class)
		{
			$prefix='Tpay\\OpenApi\\';
			$baseDir=PLUGIN_CHBS_LIBRARY_PATH.'tpay-openapi-php-master/';
			
			$len=strlen($prefix);
			if(strncmp($prefix,$class,$len)!==0)
				return;
			
			$relativeClass=substr($class,$len);
			$file=$baseDir.str_replace('\\','/',$relativeClass).'.php';
			
			if(file_exists($file))
				require_once($file);
		});
	}
	
	/**************************************************************************/
	
	private function getApiClient($meta)
	{
		$clientId=trim($meta['payment_tpay_client_id']);
		$clientSecret=trim($meta['payment_tpay_client_secret']);
		
		if($clientId==='' || $clientSecret==='')
			return(null);
		
		$cache=new \Tpay\OpenApi\Utilities\Cache(null,new CHBSTpayCache());
		$productionMode=((int)$meta['payment_tpay_sandbox_mode_enable']===1) ? false : true;
		
		return(new \Tpay\OpenApi\Api\TpayApi($cache,$clientId,$clientSecret,$productionMode));
	}
	
	/**************************************************************************/
	
	private function getPaymentRedirectUrl($response)
	{
		$keys=array('redirectUrl','redirect_url','paymentUrl','payment_url','transactionPaymentUrl','transaction_payment_url','url');
		
		foreach($keys as $key)
		{
			if(isset($response[$key]) && is_string($response[$key]) && $response[$key]!=='')
				return($response[$key]);
		}
		
		return(null);
	}
	
	/**************************************************************************/
	
	private function getPaymentTransactionId($response)
	{
		$keys=array('transactionId','transaction_id','id');
		
		foreach($keys as $key)
		{
			if(isset($response[$key]) && is_string($response[$key]) && $response[$key]!=='')
				return($response[$key]);
		}
		
		return(null);
	}
	
	/**************************************************************************/
	
	private function getBankGroups($meta)
	{
		$cacheKey='bank_groups_'.md5($meta['payment_tpay_client_id'].'|'.(int)$meta['payment_tpay_sandbox_mode_enable']);
		
		$cache=new CHBSTpayCache();
		$cached=$cache->get($cacheKey);
		
		if(is_array($cached))
			return($cached);
		
		$api=$this->getApiClient($meta);
		if(is_null($api))
			return(array());
		
		try
		{
			$response=$api->transactions()->getBankGroups(true);
		}
		catch(Exception $ex)
		{
			return(array());
		}
		
		$groups=array();
		$rawGroups=array();
		
		if(isset($response['data']) && is_array($response['data']))
			$rawGroups=$response['data'];
		elseif(isset($response['groups']) && is_array($response['groups']))
			$rawGroups=$response['groups'];
		elseif(isset($response['items']) && is_array($response['items']))
			$rawGroups=$response['items'];
		elseif(is_array($response))
			$rawGroups=$response;
		
		foreach($rawGroups as $group)
		{
			if(!is_array($group))
				continue;
			
			$groups[]=array
			(
				'id'=>isset($group['id']) ? $group['id'] : (isset($group['groupId']) ? $group['groupId'] : ''),
				'name'=>isset($group['name']) ? $group['name'] : '',
				'img'=>isset($group['img']) ? $group['img'] : (isset($group['image']) ? $group['image'] : '')
			);
		}
		
		if(count($groups))
			$cache->set($cacheKey,$groups,3600);
		
		return($groups);
	}
	
	/**************************************************************************/
	
	public function getBankSelectionForm($bookingForm)
	{
		$groups=$this->getBankGroups($bookingForm['meta']);
		
		if(!count($groups))
		{
			return('<div class="chbs-notice">'.esc_html__('Unable to load Tpay payment methods. Check the Tpay credentials.','chauffeur-booking-system').'</div>');
		}
		
		$language=strtolower(substr(get_locale(),0,2))==='pl' ? 'pl' : 'en';
		
		\Tpay\OpenApi\Utilities\Util::$libraryPath=PLUGIN_CHBS_URL.'library/tpay-openapi-php-master/';
		\Tpay\OpenApi\Utilities\Util::$customTemplateDirectory=PLUGIN_CHBS_PATH.'template/tpay/';
		
		$paymentForms=new \Tpay\OpenApi\Forms\PaymentForms($language);
		
		return($paymentForms->getBankSelectionForm($groups,false,false,'',true));
	}
	
	/**************************************************************************/
	
	public function preparePayment($r,$response,$bookingForm,$booking,$bookingBilling,$postId)
	{
		if((int)$response['payment_id']!==self::PAYMENT_ID)
			return($r);
		
		$Validation=new CHBSValidation();
		
		$groupId=(int)CHBSHelper::getPostValue('payment_tpay_group_id');
		
		if($groupId<=0)
		{
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Select a Tpay payment method.','chauffeur-booking-system');
			return($response);
		}
		
		$api=$this->getApiClient($bookingForm['meta']);
		if(is_null($api))
			return(false);
		
		$successUrl=$bookingForm['meta']['payment_tpay_success_url_address'];
		$cancelUrl=$bookingForm['meta']['payment_tpay_cancel_url_address'];
		
		if($Validation->isEmpty($successUrl))
			$successUrl=get_the_permalink($postId);
		if($Validation->isEmpty($cancelUrl))
			$cancelUrl=get_the_permalink($postId);
		
		$notificationUrl=add_query_arg('action','payment_tpay',home_url('/'));
		
		$payerName=trim(sprintf('%s %s',$booking['meta']['client_contact_detail_first_name'],$booking['meta']['client_contact_detail_last_name']));
		$GeoLocation=new CHBSGeoLocation();
		
		$fields=array
		(
			'amount'=>CHBSPrice::numberFormat($bookingBilling['summary']['pay']),
			'description'=>$booking['post']->post_title,
			'hiddenDescription'=>(string)$booking['post']->ID,
			'lang'=>strtolower(substr(get_locale(),0,2)),
			'pay'=>array
			(
				'groupId'=>$groupId
			),
			'payer'=>array
			(
				'email'=>$booking['meta']['client_contact_detail_email_address'],
				'name'=>$payerName,
				'phone'=>$booking['meta']['client_contact_detail_phone_number'],
				'address'=>$booking['meta']['client_billing_detail_street_name'],
				'code'=>$booking['meta']['client_billing_detail_postal_code'],
				'city'=>$booking['meta']['client_billing_detail_city'],
				'country'=>$booking['meta']['client_billing_detail_country_code'],
				'ip'=>$GeoLocation->getIPAddress(),
				'userAgent'=>isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
			),
			'callbacks'=>array
			(
				'payerUrls'=>array
				(
					'success'=>$successUrl,
					'error'=>$cancelUrl
				),
				'notification'=>array
				(
					'url'=>$notificationUrl
				)
			)
		);
		
		try
		{
			$responseData=$api->transactions()->createTransactionWithInstantRedirection($fields);
		}
		catch(Exception $ex)
		{
			$LogManager=new CHBSLogManager();
			$LogManager->add('tpay',1,$ex->__toString());
			return(false);
		}
		
		$redirectUrl=$this->getPaymentRedirectUrl($responseData);
		
		if($Validation->isEmpty($redirectUrl))
			return(false);
		
		$meta=CHBSPostMeta::getPostMeta($booking['post']);
		
		if(!array_key_exists('payment_tpay_data',$meta))
			$meta['payment_tpay_data']=array();
		
		$meta['payment_tpay_data'][]=array
		(
			'type'=>'transaction_create',
			'group_id'=>$groupId,
			'response'=>$responseData
		);
		
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_data',$meta['payment_tpay_data']);
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_group_id',$groupId);
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_transaction_id',$this->getPaymentTransactionId($responseData));
		
		$response['payment_process']=1;
		$response['payment_tpay_redirect_url']=esc_url($redirectUrl);
		$response['payment_tpay_redirect_duration']=(int)$bookingForm['meta']['payment_tpay_redirect_duration'];
		
		return($response);
	}
	
	/**************************************************************************/
	
	public function receivePayment()
	{
		if(!array_key_exists('action',$_REQUEST)) return;
		if($_REQUEST['action']!=='payment_tpay') return;
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('tpay',2,__('[1] Receiving a payment.','chauffeur-booking-system'));
		
		$rawPayload=file_get_contents('php://input');
		$payloadData=json_decode($rawPayload,true);
		
		$bookingId=0;
		
		if(is_array($payloadData) && isset($payloadData['data']['tr_crc']))
			$bookingId=(int)$payloadData['data']['tr_crc'];
		elseif(isset($_POST['tr_crc']))
			$bookingId=(int)$_POST['tr_crc'];
		
		if($bookingId<=0)
		{
			$LogManager->add('tpay',2,__('[2] Booking reference not found in notification.','chauffeur-booking-system'));
			http_response_code(200);
			echo 'OK';
			exit;
		}
		
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		$BookingStatus=new CHBSBookingStatus();
		
		$booking=$Booking->getBooking($bookingId);
		
		if(!is_array($booking) || !count($booking))
		{
			$LogManager->add('tpay',2,sprintf(__('[3] Booking %s is not found.','chauffeur-booking-system'),$bookingId));
			http_response_code(200);
			echo 'OK';
			exit;
		}
		
		$bookingForm=$BookingForm->getDictionary(array('booking_form_id'=>$booking['meta']['booking_form_id']));
		$bookingFormMeta=array();
		
		if(is_array($bookingForm) && array_key_exists($booking['meta']['booking_form_id'],$bookingForm))
			$bookingFormMeta=$bookingForm[$booking['meta']['booking_form_id']]['meta'];
		
		$notificationSecret=isset($bookingFormMeta['payment_tpay_notification_secret']) ? $bookingFormMeta['payment_tpay_notification_secret'] : '';
		if($notificationSecret==='')
			$notificationSecret=isset($bookingFormMeta['payment_tpay_client_secret']) ? $bookingFormMeta['payment_tpay_client_secret'] : '';
		
		if($notificationSecret==='')
		{
			$LogManager->add('tpay',2,__('[4] Missing Tpay notification secret.','chauffeur-booking-system'));
			http_response_code(400);
			exit;
		}
		
		try
		{
			$cache=new \Tpay\OpenApi\Utilities\Cache(null,new CHBSTpayCache());
			$certificateProvider=new \Tpay\OpenApi\Utilities\CacheCertificateProvider($cache);
			$productionMode=((int)(isset($bookingFormMeta['payment_tpay_sandbox_mode_enable']) ? $bookingFormMeta['payment_tpay_sandbox_mode_enable'] : 0)===1) ? false : true;
			
			$notificationHandler=new \Tpay\OpenApi\Webhook\JWSVerifiedPaymentNotification($certificateProvider,$notificationSecret,$productionMode);
			$notification=$notificationHandler->getNotification();
			
			if($notification instanceof \Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment)
				$data=$notification->getNotificationAssociative();
			else $data=(array)$notification;
		}
		catch(Exception $ex)
		{
			$LogManager->add('tpay',2,$ex->__toString());
			http_response_code(400);
			exit;
		}
		
		$meta=CHBSPostMeta::getPostMeta($bookingId);
		
		if(!array_key_exists('payment_tpay_data',$meta))
			$meta['payment_tpay_data']=array();
		
		$meta['payment_tpay_data'][]=array
		(
			'type'=>'notification',
			'data'=>$data
		);
		
		CHBSPostMeta::updatePostMeta($bookingId,'payment_tpay_data',$meta['payment_tpay_data']);
		
		$statusValue=isset($data['tr_status']) ? strtolower((string)$data['tr_status']) : '';
		
		if(in_array($statusValue,array('true','paid','correct','success'),true))
		{
			if(CHBSOption::getOption('booking_status_payment_success')!=-1)
			{
				if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_status_payment_success')))
				{
					$LogManager->add('tpay',2,__('[4] Updating booking status.','chauffeur-booking-system'));
					CHBSBookingHelper::paymentSuccess($bookingId);
				}
				else
				{
					$LogManager->add('tpay',2,__('[5] Cannot find a valid booking status.','chauffeur-booking-system'));
				}
			}
		}
		
		http_response_code(200);
		echo 'OK';
		exit;
	}
	
	/**************************************************************************/
	
	public function renderTransactionDetails($html,$data)
	{
		if(!isset($data['meta']['payment_tpay_data']))
			return($html);
		
		if(!is_array($data['meta']['payment_tpay_data']) || !count($data['meta']['payment_tpay_data']))
			return($html);
		
		$html=
		'
			<div>	
				<table class="to-table">
					<thead>
						<tr>
							<th style="width:15%">
								<div>
									'.esc_html__('Type','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Type.','chauffeur-booking-system').'</span>
								</div>
							</th>
							<th style="width:15%">
								<div>
									'.esc_html__('Group','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Selected payment group.','chauffeur-booking-system').'</span>
								</div>
							</th>
							<th style="width:15%">
								<div>
									'.esc_html__('Status','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Status.','chauffeur-booking-system').'</span>
								</div>
							</th>
							<th style="width:55%">
								<div>
									'.esc_html__('Details','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Details.','chauffeur-booking-system').'</span>
								</div>
							</th>
						</tr>
					</thead>
					<tbody>
		';
		
		foreach($data['meta']['payment_tpay_data'] as $entry)
		{
			$type=isset($entry['type']) ? $entry['type'] : '-';
			$groupId=isset($entry['group_id']) ? $entry['group_id'] : (isset($entry['data']['tr_channel']) ? $entry['data']['tr_channel'] : '-');
			$status=isset($entry['data']['tr_status']) ? $entry['data']['tr_status'] : '-';
			
			$html.=
			'
				<tr>
					<td><div>'.esc_html($type).'</div></td>
					<td><div>'.esc_html($groupId).'</div></td>
					<td><div>'.esc_html($status).'</div></td>
					<td>
						<div class="to-toggle-details">
							<a href="#">'.esc_html__('Toggle details','chauffeur-booking-system').'</a>
							<div class="to-hidden">
								<pre>
									'.var_export($entry,true).'
								</pre>
							</div>
						</div>
					</td>
				</tr>
			';
		}
		
		$html.=
		'
					</tbody>
				</table>
			</div>
		';
		
		return($html);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
