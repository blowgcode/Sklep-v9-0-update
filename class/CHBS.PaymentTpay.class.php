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
	private $paymentMethodMap=array();
	
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
	
	private function isDebugEnabled($meta=array())
	{
		if(isset($meta['payment_tpay_debug_enable']) && (int)$meta['payment_tpay_debug_enable']===1)
			return(true);
		
		return(defined('WP_DEBUG') && WP_DEBUG);
	}

	/**************************************************************************/
	
	private function logHttpError($message,$status,$body,$requestId,$meta=array())
	{
		$LogManager=new CHBSLogManager();
		$payload=array(
			'status'=>$status,
			'request_id'=>$requestId,
			'body'=>$body
		);
		$LogManager->add('tpay',1,$message.' '.wp_json_encode($payload));
		if($this->isDebugEnabled($meta))
			$this->logDebug($message,$payload);
	}
	
	/**************************************************************************/
	
	private function logApiErrorResponse($message,$status,$body,$requestId,$errors=array(),$meta=array())
	{
		$payload=array(
			'status'=>$status,
			'request_id'=>$requestId,
			'body'=>$body
		);
		
		if(!empty($errors))
			$payload['errors']=$errors;
		
		$LogManager=new CHBSLogManager();
		$LogManager->add('tpay',1,$message.' '.wp_json_encode($payload));
		
		if($this->isDebugEnabled($meta))
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
	
	private function getEnvironmentLabel($meta)
	{
		return(((int)$meta['payment_tpay_sandbox_mode_enable']===1) ? 'sandbox' : 'prod');
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
			$this->logHttpError('Tpay OAuth request failed','wp_error',$response->get_error_message(),'',$meta);
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
			$this->logHttpError('Tpay OAuth invalid response',$status,$body,$requestId,$meta);
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
		$this->paymentMethodMap=array();
		
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
			$this->logHttpError('Tpay channels request failed','wp_error',$response->get_error_message(),'',$meta);
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
			$this->logHttpError('Tpay channels invalid response',$status,$body,$requestId,$meta);
			$this->lastChannelError=array(
				'status'=>$status,
				'request_id'=>$requestId
			);
			return(array());
		}
		
		if(isset($data['result']) && $data['result']==='failed')
		{
			$this->logHttpError('Tpay channels returned failed result',$status,$body,$requestId,$meta);
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
			
			$channelId=isset($channel['id']) ? $channel['id'] : '';
			$groupId=isset($channel['groupId']) ? $channel['groupId'] : '';
			$id=$groupId!=='' ? $groupId : $channelId;
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
			
			if($groupId!=='' && $channelId!=='')
			{
				$this->paymentMethodMap['group_to_channel'][(string)$groupId]=(string)$channelId;
				$this->paymentMethodMap['channel_to_group'][(string)$channelId]=(string)$groupId;
			}
		}
		
		$this->logDebug('Fetched Tpay channels',array('count'=>count($channels),'request_id'=>$requestId));
		
		return($channels);
	}

	/**************************************************************************/
	
	private function createTransaction($meta,$fields,&$errorData=array())
	{
		$errorData=array();
		
		$token=$this->getAccessToken($meta);
		if($token===null)
		{
			$errorData=array(
				'message'=>'auth_failed',
				'status'=>'auth_failed',
				'request_id'=>''
			);
			return(null);
		}
		
		$baseUrl=$this->getApiBaseUrl($meta);
		$url=rtrim($baseUrl,'/').'/transactions';
		
		$response=wp_remote_post($url,array(
			'timeout'=>15,
			'headers'=>array(
				'Content-Type'=>'application/json',
				'Authorization'=>'Bearer '.$token
			),
			'body'=>wp_json_encode($fields)
		));
		
		if(is_wp_error($response))
		{
			$errorData=array(
				'message'=>$response->get_error_message(),
				'status'=>'wp_error',
				'request_id'=>''
			);
			$this->logHttpError('Tpay create transaction failed','wp_error',$response->get_error_message(),'',$meta);
			return(null);
		}
		
		$status=wp_remote_retrieve_response_code($response);
		$body=wp_remote_retrieve_body($response);
		$requestId=wp_remote_retrieve_header($response,'request-id');
		if(empty($requestId))
			$requestId=wp_remote_retrieve_header($response,'x-request-id');
		
		$data=json_decode($body,true);
		$errors=is_array($data) && isset($data['errors']) ? $data['errors'] : array();
		
		if($status<200 || $status>=300 || !is_array($data))
		{
			$errorData=array(
				'message'=>'invalid_response',
				'status'=>$status,
				'request_id'=>$requestId,
				'errors'=>$errors
			);
			$this->logApiErrorResponse('Tpay create transaction invalid response',$status,$body,$requestId,$errors,$meta);
			return(null);
		}
		
		if(isset($data['result']) && $data['result']==='failed')
		{
			$errorData=array(
				'message'=>'failed_result',
				'status'=>$status,
				'request_id'=>$requestId,
				'errors'=>$errors
			);
			$this->logApiErrorResponse('Tpay create transaction failed result',$status,$body,$requestId,$errors,$meta);
			return(null);
		}
		
		return($data);
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
	
	private function getSelectedGroupIdRaw()
	{
		$groupId=CHBSHelper::getPostValue('payment_tpay_group_id');
		if(empty($groupId))
			$groupId=CHBSHelper::getPostValue('payment_tpay_group_id',false);
		if(empty($groupId) && isset($_POST['groupId']))
			$groupId=$_POST['groupId'];
		
		return($groupId);
	}

	/**************************************************************************/
	
	private function getZipCodeFromCoordinate($coordinateValue)
	{
		if(is_array($coordinateValue) || is_object($coordinateValue))
		{
			$data=json_decode(wp_json_encode($coordinateValue),true);
		}
		else
		{
			$data=json_decode((string)$coordinateValue,true);
		}
		
		if(!is_array($data))
			return('');
		
		if(isset($data['zip_code']) && is_string($data['zip_code']))
			return(trim($data['zip_code']));
		
		if(isset($data['postcode']) && is_string($data['postcode']))
			return(trim($data['postcode']));
		
		return('');
	}

	/**************************************************************************/
	
	private function hasErrorCode($errors,$needle)
	{
		if(!is_array($errors))
			return(false);
		
		foreach($errors as $error)
		{
			if(!is_array($error))
				continue;
			
			foreach(array('code','errorCode','error_code','message') as $key)
			{
				if(isset($error[$key]) && is_string($error[$key]) && stripos($error[$key],$needle)!==false)
					return(true);
			}
		}
		
		return(false);
	}

	/**************************************************************************/
	
	private function isNotificationRequest()
	{
		if(isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])!=='POST')
			return(false);
		
		$hasAction=(array_key_exists('action',$_REQUEST) && $_REQUEST['action']==='payment_tpay');
		$hasSignature=isset($_SERVER['HTTP_X_JWS_SIGNATURE']) && $_SERVER['HTTP_X_JWS_SIGNATURE']!=='';
		
		return($hasAction || $hasSignature);
	}

	/**************************************************************************/

	private function respondAndExit($success)
	{
		while(ob_get_level())
		{
			ob_end_clean();
		}
		
		if(!headers_sent())
		{
			http_response_code(200);
			header('Content-Type: text/plain; charset=UTF-8');
		}
		
		echo ($success ? 'TRUE' : 'FALSE');
		exit;
	}

	/**************************************************************************/

	private function getRequestContentType()
	{
		$contentType=isset($_SERVER['CONTENT_TYPE']) ? strtolower((string)$_SERVER['CONTENT_TYPE']) : '';
		if($contentType!=='' && strpos($contentType,';')!==false)
			$contentType=trim(strstr($contentType,';',true));
		
		return($contentType);
	}

	/**************************************************************************/

	private function parseNotificationPayload($rawBody,$contentType)
	{
		if($contentType==='application/json')
		{
			$decoded=json_decode($rawBody,true);
			return(is_array($decoded) ? $decoded : null);
		}
		
		$parsed=array();
		parse_str($rawBody,$parsed);
		return($parsed);
	}

	/**************************************************************************/

	private function getNotificationDataSource($payloadData)
	{
		if(is_array($payloadData) && isset($payloadData['data']) && is_array($payloadData['data']))
			return($payloadData['data']);
		
		return(is_array($payloadData) ? $payloadData : array());
	}

	/**************************************************************************/

	private function base64UrlDecode($data)
	{
		$remainder=strlen($data) % 4;
		if($remainder)
		{
			$data.=str_repeat('=',4 - $remainder);
		}
		
		return(base64_decode(strtr($data,'-_','+/')));
	}

	/**************************************************************************/

	private function base64UrlEncode($data)
	{
		return(rtrim(strtr(base64_encode($data),'+/','-_'),'='));
	}

	/**************************************************************************/

	private function verifyJwsSignatureManually($rawBody,$productionMode,&$errorMessage)
	{
		$jws=isset($_SERVER['HTTP_X_JWS_SIGNATURE']) ? (string)$_SERVER['HTTP_X_JWS_SIGNATURE'] : '';
		if($jws==='')
		{
			$errorMessage='Missing X-JWS-Signature header.';
			return(false);
		}
		
		$jwsParts=explode('.',$jws);
		if(count($jwsParts)!==3)
		{
			$errorMessage='Invalid JWS format.';
			return(false);
		}
		
		list($encodedHeader,$encodedPayload,$encodedSignature)=$jwsParts;
		$headerJson=$this->base64UrlDecode($encodedHeader);
		$headerData=json_decode($headerJson,true);
		$x5u=(is_array($headerData) && isset($headerData['x5u'])) ? (string)$headerData['x5u'] : '';
		
		$prefix=$productionMode ? 'https://secure.tpay.com' : 'https://secure.sandbox.tpay.com';
		if($x5u!=='' && strpos($x5u,$prefix)!==0)
		{
			$errorMessage='Wrong x5u url.';
			return(false);
		}
		
		$certUrl=$x5u!=='' ? $x5u : $prefix.'/x509/notifications-jws.pem';
		$rootCaUrl=$prefix.'/x509/tpay-jws-root.pem';
		$certResponse=wp_remote_get($certUrl);
		if(is_wp_error($certResponse))
		{
			$errorMessage='Unable to download signing certificate.';
			return(false);
		}
		$certPem=wp_remote_retrieve_body($certResponse);
		if($certPem==='')
		{
			$errorMessage='Empty signing certificate.';
			return(false);
		}
		
		$rootResponse=wp_remote_get($rootCaUrl);
		if(is_wp_error($rootResponse))
		{
			$errorMessage='Unable to download root certificate.';
			return(false);
		}
		$rootPem=wp_remote_retrieve_body($rootResponse);
		if($rootPem==='')
		{
			$errorMessage='Empty root certificate.';
			return(false);
		}
		
		$cert=openssl_x509_read($certPem);
		if($cert===false)
		{
			$errorMessage='Invalid signing certificate.';
			return(false);
		}
		
		$certValid=false;
		if(function_exists('openssl_x509_verify'))
		{
			$certValid=openssl_x509_verify($certPem,$rootPem)===1;
		}
		else
		{
			$rootTemp=wp_tempnam('tpay-root-ca');
			if($rootTemp)
				file_put_contents($rootTemp,$rootPem);
			
			if($rootTemp)
			{
				$certValid=openssl_x509_checkpurpose($cert,-1,array($rootTemp));
				$certValid=($certValid===true || $certValid===1);
			}
			
			if($rootTemp && file_exists($rootTemp))
				unlink($rootTemp);
		}
		
		if(!$certValid)
		{
			$errorMessage='Signing certificate is not trusted.';
			return(false);
		}
		
		$publicKey=openssl_pkey_get_public($cert);
		if($publicKey===false)
		{
			$errorMessage='Unable to read public key.';
			return(false);
		}
		
		$payloadEncoded=$this->base64UrlEncode($rawBody);
		$signingInput=$encodedHeader.'.'.$payloadEncoded;
		$signature=$this->base64UrlDecode($encodedSignature);
		$verified=openssl_verify($signingInput,$signature,$publicKey,OPENSSL_ALGO_SHA256);
		
		if(is_resource($publicKey))
			openssl_free_key($publicKey);
		
		if($verified!==1)
		{
			$errorMessage='Invalid JWS signature.';
			return(false);
		}
		
		return(true);
	}

	/**************************************************************************/

	private function verifyMd5sum($data,$secret,&$errorMessage)
	{
		if(!is_array($data) || !isset($data['md5sum']) || $data['md5sum']==='')
			return(true);
		
		$requiredKeys=array('id','tr_id','tr_amount','tr_crc');
		foreach($requiredKeys as $key)
		{
			if(!isset($data[$key]))
			{
				$errorMessage='Missing md5sum input field: '.$key;
				return(false);
			}
		}
		
		$expected=md5((string)$data['id'].(string)$data['tr_id'].(string)$data['tr_amount'].(string)$data['tr_crc'].(string)$secret);
		if(!hash_equals($expected,(string)$data['md5sum']))
		{
			$errorMessage=sprintf(
				'MD5 checksum mismatch. Expected prefix: %s, received prefix: %s, id=%s, tr_id=%s, tr_amount=%s, tr_crc=%s',
				substr($expected,0,8),
				substr((string)$data['md5sum'],0,8),
				(string)$data['id'],
				(string)$data['tr_id'],
				(string)$data['tr_amount'],
				(string)$data['tr_crc']
			);
			return(false);
		}
		
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
		if(is_object($response))
		{
			$response=json_decode(wp_json_encode($response),true);
		}
		
		$keys=array('transactionPaymentUrl','transaction_payment_url','paymentUrl','payment_url','redirectUrl','redirect_url','url');
		
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
			
			$groupId=isset($group['groupId']) ? $group['groupId'] : '';
			$id=$groupId!=='' ? $groupId : (isset($group['id']) ? $group['id'] : '');
			
			$groups[]=array
			(
				'id'=>$id,
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
		
		$rawGroupId=$this->getSelectedGroupIdRaw();
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
		
		if($this->isDebugEnabled($bookingForm['meta']))
		{
			$this->logDebug('Selected Tpay groupId',array(
				'raw_value'=>$rawGroupId,
				'parsed_value'=>$groupId,
				'type'=>gettype($rawGroupId)
			));
		}
		
		$availableGroups=$this->getBankGroups($bookingForm['meta']);
		$availableIds=array();
		$preview=array();
		
		foreach($availableGroups as $index=>$group)
		{
			if(!is_array($group) || !isset($group['id']))
				continue;
			
			$availableIds[]=(int)$group['id'];
			if($index<5)
			{
				$preview[]=array(
					'id'=>$group['id'],
					'name'=>isset($group['name']) ? $group['name'] : ''
				);
			}
		}
		
		if($this->isDebugEnabled($bookingForm['meta']))
		{
			$this->logDebug('Available Tpay methods preview',array(
				'count'=>count($availableIds),
				'preview'=>$preview
			));
		}
		
		if($this->isDebugEnabled($bookingForm['meta']))
		{
			$this->logDebug('ENV',array('tpay_env'=>$this->getEnvironmentLabel($bookingForm['meta'])));
		}
		
		if(!count($availableIds))
		{
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Wybrana metoda płatności jest nieprawidłowa – odśwież stronę i wybierz ponownie.','chauffeur-booking-system');
			return($response);
		}
		
		if(!in_array($groupId,$availableIds,true))
		{
			$groupIdKey=(string)$groupId;
			if(isset($this->paymentMethodMap['channel_to_group'][$groupIdKey]))
			{
				$mappedGroupId=(int)$this->paymentMethodMap['channel_to_group'][$groupIdKey];
				if(in_array($mappedGroupId,$availableIds,true))
				{
					$groupId=$mappedGroupId;
					if($this->isDebugEnabled($bookingForm['meta']))
						$this->logDebug('Mapped channelId to groupId',array('channel_id'=>$groupIdKey,'group_id'=>$mappedGroupId));
				}
			}
		}
		
		if(!in_array($groupId,$availableIds,true))
		{
			$this->logDebug('Invalid Tpay groupId selected',array('group_id'=>$groupId,'available_ids'=>$availableIds));
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Wybrana metoda płatności jest nieprawidłowa – odśwież stronę i wybierz ponownie.','chauffeur-booking-system');
			return($response);
		}
		
		$api=$this->getApiClient($bookingForm['meta']);
		if(is_null($api))
			return(false);
		
		$bookingMeta=isset($booking['meta']) && is_array($booking['meta']) ? $booking['meta'] : array();
		
		$payerEmail=isset($bookingMeta['client_contact_detail_email_address']) ? trim((string)$bookingMeta['client_contact_detail_email_address']) : '';
		$payerFirstName=isset($bookingMeta['client_contact_detail_first_name']) ? trim((string)$bookingMeta['client_contact_detail_first_name']) : '';
		$payerLastName=isset($bookingMeta['client_contact_detail_last_name']) ? trim((string)$bookingMeta['client_contact_detail_last_name']) : '';
		$payerName=trim(sprintf('%s %s',$payerFirstName,$payerLastName));
		
		if($payerEmail==='')
		{
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Aby opłacić przez Tpay wymagany jest adres e-mail.','chauffeur-booking-system');
			$response['error']['local'][]=array(
				'field'=>CHBSHelper::getFormName('client_contact_detail_email_address',false),
				'message'=>__('Enter valid e-mail address','chauffeur-booking-system')
			);
			return($response);
		}
		
		if($payerName==='' || mb_strlen($payerName)<3)
		{
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Aby opłacić przez Tpay wymagane jest imię i nazwisko.','chauffeur-booking-system');
			$response['error']['local'][]=array(
				'field'=>CHBSHelper::getFormName('client_contact_detail_first_name',false),
				'message'=>__('Enter your first name','chauffeur-booking-system')
			);
			$response['error']['local'][]=array(
				'field'=>CHBSHelper::getFormName('client_contact_detail_last_name',false),
				'message'=>__('Enter your last name','chauffeur-booking-system')
			);
			return($response);
		}
		
		$successUrl=$bookingForm['meta']['payment_tpay_success_url_address'];
		$cancelUrl=$bookingForm['meta']['payment_tpay_cancel_url_address'];
		
		if($Validation->isEmpty($successUrl))
			$successUrl=get_the_permalink($postId);
		if($Validation->isEmpty($cancelUrl))
			$cancelUrl=get_the_permalink($postId);
		
		$notificationUrl=add_query_arg('action','payment_tpay',home_url('/'));
		
		$GeoLocation=new CHBSGeoLocation();
		
		$postalCode=isset($bookingMeta['client_billing_detail_postal_code']) ? trim((string)$bookingMeta['client_billing_detail_postal_code']) : '';
		$billingCountry=isset($bookingMeta['client_billing_detail_country_code']) ? trim((string)$bookingMeta['client_billing_detail_country_code']) : '';
		$payerIp=$GeoLocation->getIPAddress();
		
		$payer=array(
			'email'=>$payerEmail,
			'name'=>$payerName
		);
		
		if($Validation->isNotEmpty($bookingMeta['client_contact_detail_phone_number'] ?? ''))
			$payer['phone']=$bookingMeta['client_contact_detail_phone_number'];
		
		if($Validation->isNotEmpty($bookingMeta['client_billing_detail_street_name'] ?? ''))
			$payer['address']=$bookingMeta['client_billing_detail_street_name'];
		
		if($Validation->isNotEmpty($bookingMeta['client_billing_detail_city'] ?? ''))
			$payer['city']=$bookingMeta['client_billing_detail_city'];
		
		if($billingCountry!=='')
			$payer['country']=$billingCountry;
		
		if(is_string($postalCode) && mb_strlen(trim($postalCode))>=3)
		{
			$payer['code']=trim($postalCode);
		}
		else
		{
			foreach(array(1,2,3) as $serviceTypeId)
			{
				$pickupKey='pickup_location_coordinate_service_type_'.$serviceTypeId;
				$dropoffKey='dropoff_location_coordinate_service_type_'.$serviceTypeId;
				
				if(isset($bookingMeta[$pickupKey]))
				{
					$zipCandidate=$this->getZipCodeFromCoordinate($bookingMeta[$pickupKey]);
					if($zipCandidate!=='' && mb_strlen($zipCandidate)>=3)
					{
						$payer['code']=$zipCandidate;
						break;
					}
				}
				
				if(isset($bookingMeta[$dropoffKey]))
				{
					$zipCandidate=$this->getZipCodeFromCoordinate($bookingMeta[$dropoffKey]);
					if($zipCandidate!=='' && mb_strlen($zipCandidate)>=3)
					{
						$payer['code']=$zipCandidate;
						break;
					}
				}
			}
		}
		
		if($payerIp!=='')
			$payer['ip']=$payerIp;
		
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
			'payer'=>$payer,
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
		
		if($this->isDebugEnabled($bookingForm['meta']))
		{
			$this->logDebug('Tpay transaction payload pay',array(
				'pay'=>$fields['pay']
			));
		}
		
		$errorData=array();
		$responseData=$this->createTransaction($bookingForm['meta'],$fields,$errorData);
		
		if($responseData===null)
		{
			if($this->hasErrorCode(isset($errorData['errors']) ? $errorData['errors'] : array(),'bank_group_does_not_exist'))
			{
				$channelId='';
				if(isset($this->paymentMethodMap['group_to_channel'][(string)$groupId]))
					$channelId=$this->paymentMethodMap['group_to_channel'][(string)$groupId];
				elseif(is_numeric($rawGroupId) && (int)$rawGroupId>0)
					$channelId=(int)$rawGroupId;
				
				if($channelId!=='')
				{
					$fallbackFields=$fields;
					$fallbackFields['pay']=array('channelId'=>(int)$channelId);
					
					if($this->isDebugEnabled($bookingForm['meta']))
					{
						$this->logDebug('Retrying Tpay transaction with channelId fallback',array(
							'group_id'=>$groupId,
							'channel_id'=>$channelId
						));
					}
					
					$fallbackError=array();
					$fallbackResponse=$this->createTransaction($bookingForm['meta'],$fallbackFields,$fallbackError);
					
					if($fallbackResponse!==null)
					{
						$responseData=$fallbackResponse;
						$errorData=array();
					}
					else
					{
						$errorData=$fallbackError;
					}
				}
			}
		}
		
		$transactionResult=isset($responseData['result']) ? (string)$responseData['result'] : '';
		$transactionStatus=isset($responseData['status']) ? (string)$responseData['status'] : '';
		$transactionId=isset($responseData['transactionId']) ? (string)$responseData['transactionId'] : (isset($responseData['transaction_id']) ? (string)$responseData['transaction_id'] : '');
		$transactionTitle=isset($responseData['title']) ? (string)$responseData['title'] : '';
		$requestId=isset($responseData['requestId']) ? (string)$responseData['requestId'] : (isset($responseData['request_id']) ? (string)$responseData['request_id'] : '');
		$transactionPaymentUrl=isset($responseData['transactionPaymentUrl']) ? (string)$responseData['transactionPaymentUrl'] : '';
		
		if($this->isDebugEnabled($bookingForm['meta']))
		{
			$this->logDebug('Tpay transaction response',array(
				'result'=>$transactionResult,
				'status'=>$transactionStatus,
				'transaction_id'=>$transactionId,
				'title'=>$transactionTitle,
				'request_id'=>$requestId,
				'transactionPaymentUrl'=>$transactionPaymentUrl
			));
		}
		
		if($responseData===null)
		{
			$httpStatus=isset($errorData['status']) ? $errorData['status'] : 'unknown';
			$requestId=isset($errorData['request_id']) ? $errorData['request_id'] : '';
			$message=sprintf(__('Tpay: nie udało się utworzyć transakcji (HTTP %s, requestId=%s). Sprawdź dane płatnika.','chauffeur-booking-system'),$httpStatus,$requestId);
			
			if(isset($errorData['errors']) && is_array($errorData['errors']) && count($errorData['errors']))
			{
				$firstError=$errorData['errors'][0];
				$errorMessage=isset($firstError['message']) ? $firstError['message'] : '';
				$fieldName=isset($firstError['fieldName']) ? $firstError['fieldName'] : '';
				
				if($errorMessage!=='' || $fieldName!=='')
				{
					if($fieldName==='payer.code')
						$errorMessage=__('Brak kodu pocztowego lub jest za krótki.','chauffeur-booking-system');
					
					$message=sprintf(__('Tpay odrzucił dane płatnika: %s (fieldName=%s).','chauffeur-booking-system'),$errorMessage,$fieldName);
				}
			}
			
			if(current_user_can('manage_options') && isset($errorData['errors']) && is_array($errorData['errors']) && count($errorData['errors']))
			{
				$message.=' '.sprintf(__('Debug: %s','chauffeur-booking-system'),wp_json_encode($errorData['errors']));
			}
			
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=$message;
			return($response);
		}
		
		if($transactionResult!=='success')
		{
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Tpay: nie udało się utworzyć transakcji. Spróbuj ponownie.','chauffeur-booking-system');
			return($response);
		}
		
		$redirectUrl=$this->getPaymentRedirectUrl($responseData);
		
		if($Validation->isEmpty($redirectUrl))
		{
			$this->logDebug('Missing Tpay redirect URL in response',array(
				'response_keys'=>is_array($responseData) ? array_keys($responseData) : array()
			));
			$response['payment_process']=1;
			$response['error']['global'][0]['message']=__('Tpay: nie udało się odczytać adresu płatności. Spróbuj ponownie.','chauffeur-booking-system');
			return($response);
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
			'response'=>$responseData,
			'data'=>array(
				'tr_id'=>$this->getPaymentTransactionId($responseData),
				'tr_status'=>'PENDING',
				'tr_date'=>current_time('mysql')
			)
		);
		
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_data',$meta['payment_tpay_data']);
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_group_id',$groupId);
		CHBSPostMeta::updatePostMeta($booking['post']->ID,'payment_tpay_transaction_id',$this->getPaymentTransactionId($responseData));
		
		$response['payment_process']=1;
		$response['payment_tpay_redirect_url']=esc_url_raw($redirectUrl);
		$response['payment_tpay_redirect_duration']=(int)$bookingForm['meta']['payment_tpay_redirect_duration'];
		$response['callback_object']='CHBSPaymentTpayFrontend';
		$response['callback_function']='redirect';
		
		return($response);
	}
	
	/**************************************************************************/
	
	public function receivePayment()
	{
		if(!$this->isNotificationRequest()) return;

		$LogManager=new CHBSLogManager();
		$LogManager->add('tpay',2,__('[1] Receiving a payment.','chauffeur-booking-system'));
		@ini_set('display_errors','0');
		@ini_set('html_errors','0');
		error_reporting(0);
		
		$rawBody=file_get_contents('php://input');
		$contentType=$this->getRequestContentType();
		$payloadData=$this->parseNotificationPayload($rawBody,$contentType);
		$payloadSource=$this->getNotificationDataSource($payloadData);

		$libraryAvailable=$this->isLibraryAvailable();
		if(!$libraryAvailable)
		{
			$this->logLibraryMissing();
			$LogManager->add('tpay',2,__('[TPAY] Library not available - fallback to manual verification.','chauffeur-booking-system'));
		}
		
		$bookingId=0;
		
		if(is_array($payloadSource) && isset($payloadSource['tr_crc']))
			$bookingId=(int)$payloadSource['tr_crc'];
		
		if($bookingId<=0)
		{
			$LogManager->add('tpay',2,__('[2] Booking reference not found in notification.','chauffeur-booking-system'));
			$this->respondAndExit(true);
		}
		
		$Booking=new CHBSBooking();
		$BookingForm=new CHBSBookingForm();
		$BookingStatus=new CHBSBookingStatus();
		
		$booking=$Booking->getBooking($bookingId);
		
		if(!is_array($booking) || !count($booking))
		{
			$LogManager->add('tpay',2,sprintf(__('[3] Booking %s is not found.','chauffeur-booking-system'),$bookingId));
			$this->respondAndExit(true);
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
			$this->respondAndExit(false);
		}
		
		$jwsVerified=false;
		$data=array();
		$productionMode=((int)(isset($bookingFormMeta['payment_tpay_sandbox_mode_enable']) ? $bookingFormMeta['payment_tpay_sandbox_mode_enable'] : 0)===1) ? false : true;
		
		if(!isset($_SERVER['HTTP_X_JWS_SIGNATURE']) || $_SERVER['HTTP_X_JWS_SIGNATURE']==='')
		{
			$LogManager->add('tpay',2,__('[TPAY] Missing JWS signature header.','chauffeur-booking-system'));
			$this->respondAndExit(false);
		}
		
		if($libraryAvailable)
		{
			try
			{
				$requestParser=new class($rawBody,$contentType,$payloadData) extends \Tpay\OpenApi\Utilities\RequestParser
				{
					private $rawBody;
					private $contentType;
					private $payloadData;
					
					public function __construct($rawBody,$contentType,$payloadData)
					{
						$this->rawBody=$rawBody;
						$this->contentType=$contentType;
						$this->payloadData=$payloadData;
					}
					
					public function getContentType()
					{
						return $this->contentType;
					}
					
					public function getParsedContent()
					{
						return is_array($this->payloadData) ? $this->payloadData : array();
					}
					
					public function getPayload()
					{
						return $this->rawBody;
					}
				};
				
				$cache=new \Tpay\OpenApi\Utilities\Cache(null,new CHBSTpayCache());
				$certificateProvider=new \Tpay\OpenApi\Utilities\CacheCertificateProvider($cache);
				$notificationHandler=new \Tpay\OpenApi\Webhook\JWSVerifiedPaymentNotification($certificateProvider,$notificationSecret,$productionMode,$requestParser);
				$notification=$notificationHandler->getNotification();
				$jwsVerified=true;
				
				if($notification instanceof \Tpay\OpenApi\Model\Objects\NotificationBody\BasicPayment)
					$data=$notification->getNotificationAssociative();
				else $data=(array)$notification;
			}
			catch(Throwable $ex)
			{
				$LogManager->add('tpay',2,$ex->__toString());
			}
		}
		
		if(!$jwsVerified)
		{
			$errorMessage='';
			if(!$this->verifyJwsSignatureManually($rawBody,$productionMode,$errorMessage))
			{
				$LogManager->add('tpay',2,sprintf(__('[TPAY] JWS verification failed: %s','chauffeur-booking-system'),$errorMessage));
				$this->respondAndExit(false);
			}
			
			$LogManager->add('tpay',2,__('[TPAY] JWS verified using manual verification.','chauffeur-booking-system'));
			$data=$payloadSource;
		}
		
		if(!is_array($data) || !count($data))
		{
			$LogManager->add('tpay',2,__('[TPAY] Empty notification payload after verification.','chauffeur-booking-system'));
			$this->respondAndExit(false);
		}
		
		$md5Error='';
		if(!$this->verifyMd5sum($data,$notificationSecret,$md5Error))
		{
			$LogManager->add('tpay',2,sprintf(__('[TPAY] %s','chauffeur-booking-system'),$md5Error));
			$this->respondAndExit(false);
		}
		
		$meta=CHBSPostMeta::getPostMeta($bookingId);
		
		if(!array_key_exists('payment_tpay_data',$meta))
			$meta['payment_tpay_data']=array();
		
		$meta['payment_tpay_data'][]=array
		(
			'type'=>'notification',
			'data'=>$data
		);

		if(count($meta['payment_tpay_data'])>200)
			$meta['payment_tpay_data']=array_slice($meta['payment_tpay_data'],-200);

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
			$this->respondAndExit(true);
		}
		
		if($notificationKey!=='')
		{
			$meta['payment_tpay_notification_ids'][]=$notificationKey;
			if(count($meta['payment_tpay_notification_ids'])>200)
				$meta['payment_tpay_notification_ids']=array_slice($meta['payment_tpay_notification_ids'],-200);
			CHBSPostMeta::updatePostMeta($bookingId,'payment_tpay_notification_ids',$meta['payment_tpay_notification_ids']);
		}
		
		$LogManager->add('tpay',2,sprintf(__('[5] Notification received. tr_id=%s tr_crc=%s tr_status=%s jws_verified=%s','chauffeur-booking-system'),$trId,$trCrc,$trStatus,$jwsVerified ? 'true' : 'false'));
		
		$statusValue=isset($data['tr_status']) ? strtoupper(trim((string)$data['tr_status'])) : '';
		
		if($statusValue==='PAID')
		{
			$LogManager->add('tpay',2,__('[6] Received PAID status (stage 1).','chauffeur-booking-system'));
			$this->respondAndExit(true);
		}
		
		if($statusValue==='CHARGEBACK')
		{
			$LogManager->add('tpay',2,__('[6] Received CHARGEBACK status.','chauffeur-booking-system'));

			$chargebackStatus=(int)apply_filters('chbs_tpay_chargeback_booking_status',0,$bookingId);
			if($chargebackStatus>0 && $BookingStatus->isBookingStatus($chargebackStatus))
			{
				$currentStatus=isset($booking['meta']['booking_status_id']) ? (int)$booking['meta']['booking_status_id'] : 0;
				if($currentStatus!==$chargebackStatus)
				{
					CHBSPostMeta::updatePostMeta($bookingId,'booking_status_id',$chargebackStatus);
					$LogManager->add('tpay',2,sprintf(__('[6] Booking status changed to %d due to CHARGEBACK.','chauffeur-booking-system'),$chargebackStatus));
				}
			}

			$this->respondAndExit(true);
		}
		
		if(in_array($statusValue,array('TRUE','CORRECT','SUCCESS'),true))
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
			
			$this->respondAndExit(true);
		}
		
		$LogManager->add('tpay',2,__('[6] Notification received with unsupported status.','chauffeur-booking-system'));
		$this->respondAndExit(true);
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
				<table class="to-table to-table-fixed-layout">
					<thead>
						<tr>
							<th style="width:15%">
								<div>
									'.esc_html__('Transaction ID','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Transaction ID.','chauffeur-booking-system').'</span>
								</div>
							</th>
							<th style="width:15%">
								<div>
									'.esc_html__('Type','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Type.','chauffeur-booking-system').'</span>
								</div>
							</th>
							<th style="width:15%">
								<div>
									'.esc_html__('Date','chauffeur-booking-system').'
									<span class="to-legend">'.esc_html__('Date.','chauffeur-booking-system').'</span>
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
			$payload=array();
			if(isset($entry['data']) && is_array($entry['data']))
				$payload=$entry['data'];
			elseif(isset($entry['response']) && is_array($entry['response']))
				$payload=$entry['response'];

			$transactionId='-';
			$dateValue='-';
			$statusLabel=$type;
			$statusValue=isset($payload['tr_status']) ? strtoupper(trim((string)$payload['tr_status'])) : '';
			if($statusValue!=='')
				$statusLabel=$statusValue;

			if(in_array($statusLabel,array('TRUE','SUCCESS','CORRECT'),true))
				$statusLabel=__('Paid','chauffeur-booking-system');
			else if($statusLabel==='PAID')
				$statusLabel=__('Paid (stage 1)','chauffeur-booking-system');
			else if($statusLabel==='PENDING')
				$statusLabel=__('Unpaid','chauffeur-booking-system');
			else if($statusLabel==='CHARGEBACK')
				$statusLabel=__('Chargeback','chauffeur-booking-system');

			$transactionIdKeys=array('tr_id','transactionId','transaction_id','id');
			foreach($transactionIdKeys as $key)
			{
				if(isset($payload[$key]) && $payload[$key]!=='')
				{
					$transactionId=$payload[$key];
					break;
				}
			}

			$dateKeys=array('tr_date','created','created_at','createdAt','date');
			foreach($dateKeys as $key)
			{
				if(isset($payload[$key]) && $payload[$key]!=='')
				{
					$dateValue=$payload[$key];
					break;
				}
			}
			
			$html.=
			'
				<tr>
					<td><div>'.esc_html($transactionId).'</div></td>
					<td><div>'.esc_html($statusLabel).'</div></td>
					<td><div>'.esc_html($dateValue).'</div></td>
					<td>
						<div class="to-toggle-details">
							<a href="#">'.esc_html__('Toggle details','chauffeur-booking-system').'</a>
							<div class="to-hidden">
								<pre>
									'.esc_html(var_export($entry,true)).'
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
