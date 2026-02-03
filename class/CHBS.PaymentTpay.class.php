<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPaymentTpay
{
	/**************************************************************************/
	
	const PAYMENT_ID=6;
	const ACCESS_TOKEN_TRANSIENT_PREFIX='chbs_tpay_access_token_';
	
	/**************************************************************************/
	
	private static $autoloadInitialized=false;
	private static $hooksInitialized=false;
	private static $libraryAvailable=null;
	private $lastChannelError=array();
	
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
		if(self::$autoloadInitialized) return(self::$libraryAvailable === true);
		
		self::$autoloadInitialized=true;
		self::$libraryAvailable=false;

		$libraryRoot=PLUGIN_CHBS_LIBRARY_PATH.'tpay-openapi-php-master/';
		$vendorAutoloadCandidates=array(
			$libraryRoot.'vendor/autoload.php',
			PLUGIN_CHBS_PATH.'vendor/autoload.php'
		);
		if(defined('WP_CONTENT_DIR'))
		{
			$vendorAutoloadCandidates[]=rtrim(WP_CONTENT_DIR,'/').'/vendor/autoload.php';
		}
		if(defined('ABSPATH'))
		{
			$vendorAutoloadCandidates[]=rtrim(ABSPATH,'/').'/vendor/autoload.php';
		}
		$vendorAutoloadCandidates=array_unique(apply_filters('chbs_tpay_autoload_paths',$vendorAutoloadCandidates));
		$srcRoot=$libraryRoot.'src/';
		
		foreach($vendorAutoloadCandidates as $vendorAutoload)
		{
			if($vendorAutoload && file_exists($vendorAutoload))
			{
				require_once($vendorAutoload);
				break;
			}
		}
		
		if(!class_exists('Tpay\\OpenApi\\Api\\TpayApi'))
		{
			if(!is_dir($srcRoot))
			{
				return(false);
			}
			
			spl_autoload_register(function($class) use ($srcRoot)
			{
				$prefixes=array(
					'Tpay\\OpenApi\\'=>$srcRoot,
					'Tpay\\Example\\'=>PLUGIN_CHBS_LIBRARY_PATH.'tpay-openapi-php-master/examples/'
				);
				
				foreach($prefixes as $prefix=>$baseDir)
				{
					$len=strlen($prefix);
					if(strncmp($prefix,$class,$len)!==0)
						continue;
					
					$relativeClass=substr($class,$len);
					$file=$baseDir.str_replace('\\','/',$relativeClass).'.php';
					
					if(file_exists($file))
					{
						require_once($file);
					}
					return;
				}
			});
		}

		if(class_exists('Tpay\\OpenApi\\Api\\TpayApi'))
		{
			self::$libraryAvailable=true;
		}
		
		return(self::$libraryAvailable);
	}

	/**************************************************************************/
	
	private function isLibraryAvailable()
	{
		if(self::$libraryAvailable!==null)
			return(self::$libraryAvailable);
		
		return($this->registerAutoloader());
	}
	
	/**************************************************************************/
	
	private function logLibraryMissing()
	{
		$LogManager=new CHBSLogManager();
		$LogManager->add('tpay',1,__('Unable to load Tpay OpenAPI library. Payment temporarily unavailable.','chauffeur-booking-system'));
		
		return(false);
	}

	/**************************************************************************/
	
	private function logDebug($message,$context=array())
	{
		if(defined('WP_DEBUG') && WP_DEBUG)
		{
			if(!empty($context))
				$message.=' '.wp_json_encode($context);
			
			error_log('[CHBS Tpay] '.$message);
		}
	}

	/**************************************************************************/
	
	private function logHttpError($message,$status,$body,$requestId)
	{
		$LogManager=new CHBSLogManager();
		$payload=array(
			'status'=>$status,
			'request_id'=>$requestId,
			'body'=>$body
		);
		$LogManager->add('tpay',1,$message.' '.wp_json_encode($payload));
		$this->logDebug($message,$payload);
	}
	
	/**************************************************************************/
	
	private function getApiBaseUrl($meta)
	{
		$sandboxMode=((int)$meta['payment_tpay_sandbox_mode_enable']===1);
		
		if(class_exists('Tpay\\OpenApi\\Api\\ApiAction'))
		{
			return($sandboxMode ? \Tpay\OpenApi\Api\ApiAction::TPAY_API_URL_SANDBOX : \Tpay\OpenApi\Api\ApiAction::TPAY_API_URL_PRODUCTION);
		}
		
		return($sandboxMode ? 'https://openapi.tpay.com' : 'https://api.tpay.com');
	}
	
	/**************************************************************************/
	
	private function getAccessToken($meta)
	{
		$clientId=trim($meta['payment_tpay_client_id']);
		$clientSecret=trim($meta['payment_tpay_client_secret']);
		
		if($clientId==='' || $clientSecret==='')
			return(null);
		
		$cacheKey=self::ACCESS_TOKEN_TRANSIENT_PREFIX.md5($clientId.'|'.(int)$meta['payment_tpay_sandbox_mode_enable']);
		$cached=get_transient($cacheKey);
		
		if(is_array($cached) && !empty($cached['access_token']))
			return($cached['access_token']);
		
		$baseUrl=$this->getApiBaseUrl($meta);
		$url=rtrim($baseUrl,'/').'/oauth/auth';
		
		$response=wp_remote_post($url,array(
			'timeout'=>15,
			'headers'=>array(
				'Content-Type'=>'application/x-www-form-urlencoded'
			),
			'body'=>array(
				'client_id'=>$clientId,
				'client_secret'=>$clientSecret
			)
		));
		
		if(is_wp_error($response))
		{
			$this->logHttpError('Tpay OAuth request failed','wp_error',$response->get_error_message(),'');
			return(null);
		}
		
		$status=wp_remote_retrieve_response_code($response);
		$body=wp_remote_retrieve_body($response);
		$requestId=wp_remote_retrieve_header($response,'request-id');
		if(empty($requestId))
			$requestId=wp_remote_retrieve_header($response,'x-request-id');
		
		$data=json_decode($body,true);
		$accessToken=is_array($data) && isset($data['access_token']) ? (string)$data['access_token'] : '';
		$expiresIn=is_array($data) && isset($data['expires_in']) ? (int)$data['expires_in'] : 0;
		
		if($accessToken==='')
		{
			$this->logHttpError('Tpay OAuth invalid response',$status,$body,$requestId);
			return(null);
		}
		
		$ttl=$expiresIn>0 ? max(60,$expiresIn-100) : 7100;
		if($ttl>7100) $ttl=7100;
		
		set_transient($cacheKey,array('access_token'=>$accessToken),$ttl);
		
		$this->logDebug('Tpay OAuth token cached',array('expires_in'=>$ttl,'request_id'=>$requestId));
		
		return($accessToken);
	}
	
	/**************************************************************************/
	
	private function getPaymentChannels($meta)
	{
		$this->lastChannelError=array();
		
		$token=$this->getAccessToken($meta);
		if($token===null)
		{
			$this->lastChannelError=array(
				'status'=>'auth_failed',
				'request_id'=>''
			);
			return(array());
		}
		
		$baseUrl=$this->getApiBaseUrl($meta);
		$url=rtrim($baseUrl,'/').'/transactions/channels';
		
		$response=wp_remote_get($url,array(
			'timeout'=>15,
			'headers'=>array(
				'Authorization'=>'Bearer '.$token
			)
		));
		
		if(is_wp_error($response))
		{
			$this->logHttpError('Tpay channels request failed','wp_error',$response->get_error_message(),'');
			$this->lastChannelError=array(
				'status'=>'wp_error',
				'request_id'=>''
			);
			return(array());
		}
		
		$status=wp_remote_retrieve_response_code($response);
		$body=wp_remote_retrieve_body($response);
		$requestId=wp_remote_retrieve_header($response,'request-id');
		if(empty($requestId))
			$requestId=wp_remote_retrieve_header($response,'x-request-id');
		
		$data=json_decode($body,true);
		
		if($status<200 || $status>=300 || !is_array($data))
		{
			$this->logHttpError('Tpay channels invalid response',$status,$body,$requestId);
			$this->lastChannelError=array(
				'status'=>$status,
				'request_id'=>$requestId
			);
			return(array());
		}
		
		if(isset($data['result']) && $data['result']==='failed')
		{
			$this->logHttpError('Tpay channels returned failed result',$status,$body,$requestId);
			$this->lastChannelError=array(
				'status'=>$status,
				'request_id'=>$requestId
			);
			return(array());
		}
		
		$channels=array();
		$rawChannels=array();
		
		if(isset($data['channels']) && is_array($data['channels']))
			$rawChannels=$data['channels'];
		elseif(isset($data['data']) && is_array($data['data']))
			$rawChannels=$data['data'];
		
		foreach($rawChannels as $channel)
		{
			if(!is_array($channel))
				continue;
			
			if(isset($channel['available']) && (int)$channel['available']===0)
				continue;
			
			$id=isset($channel['id']) ? $channel['id'] : (isset($channel['groupId']) ? $channel['groupId'] : '');
			$name=isset($channel['name']) ? $channel['name'] : '';
			$image='';
			
			if(isset($channel['image']) && is_array($channel['image']) && isset($channel['image']['url']))
				$image=$channel['image']['url'];
			elseif(isset($channel['img']))
				$image=$channel['img'];
			
			$channels[]=array(
				'id'=>$id,
				'name'=>$name,
				'img'=>$image
			);
		}
		
		$this->logDebug('Fetched Tpay channels',array('count'=>count($channels),'request_id'=>$requestId));
		
		return($channels);
	}
	
	/**************************************************************************/
	
	private function getSelectedGroupId()
	{
		$groupId=CHBSHelper::getPostValue('payment_tpay_group_id');
		if(empty($groupId))
			$groupId=CHBSHelper::getPostValue('payment_tpay_group_id',false);
		if(empty($groupId) && isset($_POST['groupId']))
			$groupId=$_POST['groupId'];
		
		$groupId=(int)trim((string)$groupId);
		
		return($groupId);
	}

	/**************************************************************************/
	
	private function isNotificationRequest()
	{
		if(array_key_exists('action',$_REQUEST) && $_REQUEST['action']==='payment_tpay')
			return(true);
		
		if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])!=='POST')
			return(false);
		
		if(!isset($_SERVER['HTTP_X_JWS_SIGNATURE']))
			return(false);
		
		return(true);
	}
	
	/**************************************************************************/
	
	private function getApiClient($meta)
	{
		if(!$this->isLibraryAvailable())
		{
			$this->logLibraryMissing();
			return(null);
		}
		
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
	
	private function getBankGroupsFromApi($meta)
	{
		$clientId=isset($meta['payment_tpay_client_id']) ? (string)$meta['payment_tpay_client_id'] : '';
		$this->logDebug('Fetching Tpay bank groups',array(
			'client_id_prefix'=>substr($clientId,0,4),
			'sandbox_mode'=>(int)$meta['payment_tpay_sandbox_mode_enable']
		));
		
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
			$this->logDebug('Failed to fetch Tpay bank groups',array('error'=>$ex->getMessage()));
			return(array());
		}
		
		if(isset($response['error']))
		{
			$this->logDebug('Tpay bank groups error response',array('error'=>$response['error']));
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
		{
			$cache->set($cacheKey,$groups,3600);
		}
		
		$this->logDebug('Fetched Tpay bank groups',array('count'=>count($groups)));
		
		return($groups);
	}

	/**************************************************************************/
	
	private function getBankGroups($meta)
	{
		$channels=$this->getPaymentChannels($meta);
		
		if(count($channels))
			return($channels);
		
		$this->logDebug('Falling back to Tpay bank groups');
		
		return($this->getBankGroupsFromApi($meta));
	}
	
	/**************************************************************************/
	
	public function getBankSelectionForm($bookingForm)
	{
		if(!$this->isLibraryAvailable())
		{
			$this->logLibraryMissing();
			return('<div class="chbs-notice">'.esc_html__('Płatność Tpay tymczasowo niedostępna.','chauffeur-booking-system').'</div>');
		}
		
		$groups=$this->getBankGroups($bookingForm['meta']);
		
		if(!count($groups))
		{
			$this->logDebug('No Tpay bank groups available for rendering');
			$debugInfo='';
			if(current_user_can('manage_options') && !empty($this->lastChannelError))
			{
				$debugInfo=' <small>'.esc_html(sprintf('Debug: HTTP %s / requestId %s',$this->lastChannelError['status'],$this->lastChannelError['request_id'])).'</small>';
			}
			return('<div class="chbs-notice">'.esc_html__('Unable to load Tpay payment methods. Check the Tpay credentials.','chauffeur-booking-system').$debugInfo.'</div>');
		}
		
		$language=strtolower(substr(get_locale(),0,2))==='pl' ? 'pl' : 'en';
		
		\Tpay\OpenApi\Utilities\Util::$libraryPath=PLUGIN_CHBS_URL.'library/tpay-openapi-php-master/src/';
		\Tpay\OpenApi\Utilities\Util::$customTemplateDirectory=PLUGIN_CHBS_PATH.'template/tpay/';
		
		$paymentForms=new \Tpay\OpenApi\Forms\PaymentForms($language);
		$formHtml=$paymentForms->getBankSelectionForm($groups,false,false,'',true);
		
		$this->logDebug('Rendered Tpay bank selection form',array('length'=>strlen($formHtml)));
		
		return($formHtml);
	}
	
	/**************************************************************************/
	
	public function preparePayment($r,$response,$bookingForm,$booking,$bookingBilling,$postId)
	{
		if((int)$response['payment_id']!==self::PAYMENT_ID)
			return($r);
		
		$Validation=new CHBSValidation();
		
		$groupId=$this->getSelectedGroupId();
		
		if($groupId<=0)
		{
			$this->logDebug('Missing selected Tpay groupId',array(
				'post_keys'=>array_keys($_POST)
			));
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Select a Tpay payment method.','chauffeur-booking-system');
			return($response);
		}
		
		$this->logDebug('Selected Tpay groupId',array('group_id'=>$groupId));
		
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
		
		$postalCode=isset($booking['meta']['client_billing_detail_postal_code']) ? $booking['meta']['client_billing_detail_postal_code'] : '';
		
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
				'code'=>$postalCode,
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
			$this->logDebug('Failed to create Tpay transaction',array('error'=>$ex->getMessage()));
			return(false);
		}
		
		$redirectUrl=$this->getPaymentRedirectUrl($responseData);
		
		if($Validation->isEmpty($redirectUrl))
		{
			$this->logDebug('Missing Tpay redirect URL in response',array(
				'response_keys'=>is_array($responseData) ? array_keys($responseData) : array()
			));
			return(false);
		}
		
		$this->logDebug('Created Tpay transaction',array(
			'transaction_id'=>$this->getPaymentTransactionId($responseData),
			'redirect_url'=>$redirectUrl
		));
		
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
		if(!$this->isNotificationRequest()) return;

		if(!$this->isLibraryAvailable())
		{
			$this->logLibraryMissing();
			http_response_code(503);
			echo 'FALSE';
			exit;
		}
		
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
			echo 'TRUE';
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
			echo 'TRUE';
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
		
		$jwsVerified=false;
		
		try
		{
			$cache=new \Tpay\OpenApi\Utilities\Cache(null,new CHBSTpayCache());
			$certificateProvider=new \Tpay\OpenApi\Utilities\CacheCertificateProvider($cache);
			$productionMode=((int)(isset($bookingFormMeta['payment_tpay_sandbox_mode_enable']) ? $bookingFormMeta['payment_tpay_sandbox_mode_enable'] : 0)===1) ? false : true;
			
			$notificationHandler=new \Tpay\OpenApi\Webhook\JWSVerifiedPaymentNotification($certificateProvider,$notificationSecret,$productionMode);
			$notification=$notificationHandler->getNotification();
			$jwsVerified=true;
			
			if($notification instanceof \Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment)
				$data=$notification->getNotificationAssociative();
			else $data=(array)$notification;
		}
		catch(Exception $ex)
		{
			$LogManager->add('tpay',2,$ex->__toString());
			http_response_code(400);
			echo 'FALSE';
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
		
		$trId=isset($data['tr_id']) ? (string)$data['tr_id'] : '';
		$trCrc=isset($data['tr_crc']) ? (string)$data['tr_crc'] : '';
		$trStatus=isset($data['tr_status']) ? (string)$data['tr_status'] : '';
		$notificationKey=trim($trId.'|'.$trCrc.'|'.$trStatus,'|');
		
		if(!array_key_exists('payment_tpay_notification_ids',$meta))
			$meta['payment_tpay_notification_ids']=array();
		
		if($notificationKey!=='' && in_array($notificationKey,$meta['payment_tpay_notification_ids'],true))
		{
			$LogManager->add('tpay',2,sprintf(__('[5] Duplicate Tpay notification ignored. tr_id=%s tr_crc=%s tr_status=%s jws_verified=%s','chauffeur-booking-system'),$trId,$trCrc,$trStatus,$jwsVerified ? 'true' : 'false'));
			http_response_code(200);
			echo 'TRUE';
			exit;
		}
		
		if($notificationKey!=='')
		{
			$meta['payment_tpay_notification_ids'][]=$notificationKey;
			CHBSPostMeta::updatePostMeta($bookingId,'payment_tpay_notification_ids',$meta['payment_tpay_notification_ids']);
		}
		
		$LogManager->add('tpay',2,sprintf(__('[5] Notification received. tr_id=%s tr_crc=%s tr_status=%s jws_verified=%s','chauffeur-booking-system'),$trId,$trCrc,$trStatus,$jwsVerified ? 'true' : 'false'));
		
		$statusValue=isset($data['tr_status']) ? strtolower((string)$data['tr_status']) : '';
		
		if(in_array($statusValue,array('true','paid','correct','success'),true))
		{
			if(CHBSOption::getOption('booking_status_payment_success')!=-1)
			{
				if($BookingStatus->isBookingStatus(CHBSOption::getOption('booking_status_payment_success')))
				{
					$successStatus=(int)CHBSOption::getOption('booking_status_payment_success');
					$currentStatus=isset($booking['meta']['booking_status_id']) ? (int)$booking['meta']['booking_status_id'] : 0;
					
					if($currentStatus===$successStatus)
					{
						$LogManager->add('tpay',2,__('[6] Booking already marked as paid.','chauffeur-booking-system'));
					}
					else
					{
						$LogManager->add('tpay',2,__('[6] Scheduling booking status update.','chauffeur-booking-system'));
						if(!wp_next_scheduled('chbs_tpay_payment_success',array($bookingId)))
						{
							wp_schedule_single_event(time(),'chbs_tpay_payment_success',array($bookingId));
						}
						if(apply_filters('chbs_tpay_process_payment_synchronously',false,$bookingId))
						{
							CHBSBookingHelper::paymentSuccess($bookingId);
						}
					}
				}
				else
				{
					$LogManager->add('tpay',2,__('[7] Cannot find a valid booking status.','chauffeur-booking-system'));
				}
			}
		}
		
		http_response_code(200);
		echo 'TRUE';
		exit;
	}

	/**************************************************************************/
	
	public function processPaymentSuccess($bookingId)
	{
		CHBSBookingHelper::paymentSuccess((int)$bookingId);
	}

	/**************************************************************************/
	
	public function maybeRunDiagnostics()
	{
		if(!is_user_logged_in() || !current_user_can('manage_options'))
			return;
		
		if(!isset($_GET['chbs_tpay_test']))
			return;
		
		$bookingFormId=isset($_GET['booking_form_id']) ? (int)$_GET['booking_form_id'] : 0;
		
		if($bookingFormId<=0)
		{
			wp_send_json_error(array('message'=>'Missing booking_form_id.'));
		}
		
		$BookingForm=new CHBSBookingForm();
		$formDictionary=$BookingForm->getDictionary(array('booking_form_id'=>$bookingFormId));
		
		if(!is_array($formDictionary) || !array_key_exists($bookingFormId,$formDictionary))
		{
			wp_send_json_error(array('message'=>'Booking form not found.'));
		}
		
		$meta=$formDictionary[$bookingFormId]['meta'];
		$api=$this->getApiClient($meta);
		
		if(is_null($api))
		{
			wp_send_json_error(array('message'=>'Unable to initialize Tpay API client.'));
		}
		
		$results=array(
			'bank_groups'=>null,
			'transaction'=>null
		);
		
		try
		{
			$results['bank_groups']=$api->transactions()->getBankGroups(true);
		}
		catch(Exception $ex)
		{
			$results['bank_groups_error']=$ex->getMessage();
		}
		
		try
		{
			$groups=array();
			if(isset($results['bank_groups']['data']) && is_array($results['bank_groups']['data']))
				$groups=$results['bank_groups']['data'];
			
			$groupId=isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
			if($groupId<=0 && count($groups))
				$groupId=(int)$groups[0]['id'];
			
			$notificationUrl=add_query_arg('action','payment_tpay',home_url('/'));
			
			$transactionData=array
			(
				'amount'=>'1.00',
				'description'=>'CHBS Tpay test transaction',
				'hiddenDescription'=>'diagnostic',
				'lang'=>strtolower(substr(get_locale(),0,2)),
				'pay'=>array(
					'groupId'=>$groupId
				),
				'payer'=>array(
					'email'=>get_bloginfo('admin_email'),
					'name'=>'CHBS Test',
					'phone'=>'',
					'ip'=>isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
					'userAgent'=>isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
				),
				'callbacks'=>array
				(
					'payerUrls'=>array
					(
						'success'=>home_url('/'),
						'error'=>home_url('/')
					),
					'notification'=>array
					(
						'url'=>$notificationUrl
					)
				)
			);
			
			$results['transaction']=$api->transactions()->createTransactionWithInstantRedirection($transactionData);
		}
		catch(Exception $ex)
		{
			$results['transaction_error']=$ex->getMessage();
		}
		
		wp_send_json_success($results);
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
