<?php

/******************************************************************************/
/******************************************************************************/

class CHBSHelper
{
	/**************************************************************************/
	
	static function setDefault(&$address,$index,$value)
	{	
		if(array_key_exists($index,(array)$address)) return;
		$address[$index]=$value;		
	}
		
	/**************************************************************************/
	
	static function createId($prefix=null)
	{
		return((is_null($prefix) ? null : $prefix.'_').strtoupper(md5(microtime().rand())));
	}
	
	/**************************************************************************/
	
	static function createHash($value)
	{
		return(strtoupper(md5($value)));
	}
	
	/**************************************************************************/
	
	static function createSalt()
	{
		return(uniqid(mt_rand(),true));
	}
	
	/**************************************************************************/
	
	static function getPostOption($prefix=null,$extensionPrefix=PLUGIN_CHBS_PREFIX)
	{
		if(!is_null($prefix)) $prefix='_'.$prefix.'_';
		
		$option=array();
		$result=array();
		
		$address=filter_input_array(INPUT_POST);
		if(!is_array($address)) $address=array();
		
		$constant=CHBSConstant::get('option_prefix',true,$extensionPrefix);
		
		foreach($address as $key=>$value)
		{
			if(preg_match('/^'.$constant.$prefix.'/',$key,$result)) 
			{
				$index=preg_replace('/^'.$constant.'_/','',$key);
				$option[$index]=$value;
			}
		}	
		
		$option=CHBSHelper::stripslashes($option);
		
		return($option);
	}

	/**************************************************************************/
	
	static function stripslashes($value)
	{
		if((function_exists('get_magic_quotes_gpc')) && (@get_magic_quotes_gpc()))
			return(stripslashes_deep($value));
		else return($value);
	}

	/**************************************************************************/
	
	static function getFormName($name,$display=true,$extensionPrefix=PLUGIN_CHBS_PREFIX)
	{
		$constant=CHBSConstant::get('option_prefix',true,$extensionPrefix);
		
		if(!$display) return($constant.'_'.$name);
		echo $constant.'_'.$name;
	}
	
	/**************************************************************************/
	
	static function displayIf($value,$testValue,$text,$display=true)
	{
		if(is_array($value))
		{
			foreach($value as $v)
			{
				if(strcmp($v,$testValue)==0) 
				{
					if($display) echo $text;
					else return($text);
					return;
				}	
			}
		}
		else 
		{
			if(is_null($value)) $value='';
			
			if(strcmp($value,$testValue)==0) 
			{
				if($display) echo $text;
				else return($text);
			}
		}
	}
	
	/**************************************************************************/
	
	static function disabledIf($value,$testValue,$display=true)
	{
		return(self::displayIf($value,$testValue,' disabled ',$display));
	}
	
	/**************************************************************************/

	static function checkedIf($value,$testValue=1,$display=true)
	{
		return(self::displayIf($value,$testValue,' checked ',$display));
	}
	
	/**************************************************************************/
	
	static function selectedIf($value,$testValue=1,$display=true)
	{
		return(self::displayIf($value,$testValue,' selected ',$display));
	}
		
	/**************************************************************************/
	
	static function removeUIndex(&$address)
	{
		$argument=array_slice(func_get_args(),1);
		
		$address=(array)$address;
		
		foreach($argument as $index)
		{
			if(!is_array($index))
			{
				if(!array_key_exists($index,$address))
					$address[$index]='';
			}
			else
			{
				if(!array_key_exists($index[0],$address))
					$address[$index[0]]=$index[1];				
			}
		}
	}
	
	/**************************************************************************/
	
	static function addProtocolName($value,$protocol='http://')
	{
		if(!preg_match('/^'.preg_quote($protocol,'/').'/',$value)) return($protocol.$value);
		return($value);
	}
 
	/**************************************************************************/
	
	static function createLink($value)
	{
		return(preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>',$value));
	}
 
	/**************************************************************************/
	
	static function createMailToLink($value)
	{
		return(preg_replace('/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/','<a href="mailto:$1$2">$1$2</a>',$value));
	}

	/**************************************************************************/
	
	static function getPostValue($name,$prefix=true,$extensionPrefix=PLUGIN_CHBS_PREFIX)
	{
		if($prefix) $name=CHBSConstant::get('context',true,$extensionPrefix).'_'.$name;
		
		if(!array_key_exists($name,$_POST)) return(null);
		
		return(CHBSHelper::stripslashes($_POST[$name]));
	}
	
	/**************************************************************************/
	
	static function getGetValue($name,$prefix=true,$extensionPrefix=PLUGIN_CHBS_PREFIX)
	{
		if($prefix) $name=CHBSConstant::get('context',true,$extensionPrefix).'_'.$name;
		
		if(!array_key_exists($name,$_GET)) return(null);
		
		return(CHBSHelper::stripslashes($_GET[$name]));
	}
	
	/**************************************************************************/
	
	static function getEmailFromString($value)
	{
		foreach(preg_split('/\s/', $value) as $token)
		{
			$email=filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
			if($email!==false) return($email);
		}
	
		return(null);
	}
	
	/**************************************************************************/
	
	static function sessionStart()
	{
		if(version_compare(PHP_VERSION,'5.4.0','<'))
		{
			if(session_id()=='') session_start();
		}
		else
		{
			if(session_status()==PHP_SESSION_NONE) session_start();
		}
	}
	
	/**************************************************************************/
	
	static function checkSavePost($post_id,$name,$action=null)
	{
		if((defined('DOING_AUTOSAVE')) && (DOING_AUTOSAVE)) return(false);

		if(!array_key_exists($name,$_POST)) return(false);

		if(!wp_verify_nonce($_POST[$name],$action)) return(false);

		if(!current_user_can('edit_post',$post_id)) return(false);
		
		return(true);
	}
	
	/**************************************************************************/
	
	static function isEditMode()
	{
		global $pagenow;
		return(!($pagenow=='post-new.php'));
	}
	
	/**************************************************************************/
	
	static function createCSSClassAttribute($class)
	{
		$Validation=new CHBSValidation();
		
		if(!is_array($class)) $class=func_get_args();
		
		$class=esc_attr(join(' ',$class));
		
		if($Validation->isNotEmpty($class)) return(' class="'.$class.'"');
		
		return(null);
	}
	
	/**************************************************************************/
	
	static function createStyleAttribute($style)
	{
		$ret=null;
		$Validation=new CHBSValidation();
		
		if(!is_array($style)) return($ret);
		
		foreach($style as $index=>$value)
		{
			if($Validation->isEmpty($value)) continue;
			$ret.=$index.':'.$value.';';
		}
		
		return($Validation->isEmpty($ret) ? null : ' style="'.esc_attr($ret).'"');
	}
	
	/**************************************************************************/
	
	static function preservePost(&$post,&$bPost,$action=1)
	{
		if(!is_object($post)) return;
		
		if($action==1) $bPost=$post;
		else 
		{
			if(!is_object($bPost)) return;
			
			$post=$bPost;
			setup_postdata($post); 
		}
	}
	
	/**************************************************************************/
	
	static function valueInRange($value,$start,$stop)
	{
		return(($start<=$value) && ($value<=$stop) ? true : false);
	}
	
	/**************************************************************************/
	
	static function createJSONResponse($response)
	{
		echo json_encode($response);
		exit;			  
	}
	
	/**************************************************************************/
	
	static function getTheExcerpt($postId) 
	{
		global $post;  
		$aPost=$post;
		$post=get_post($postId);
		
		ob_start();
		the_excerpt();
		$output=ob_get_contents();
		ob_end_clean();
		
		$post=$aPost;
		return($output);
	}
	
	/**************************************************************************/
	
	static function splitBy($address,$char=';')
	{
		$Validation=new CHBSValidation();
		
		$address=preg_split('/'.$char.'/',$address);
		
		foreach($address as $index=>$value)
		{
			if($Validation->isEmpty($value))
				unset($address[$index]);
		}
		
		return($address);
	}
	
	/**************************************************************************/
	
	static function createPostIdField($label)
	{
		global $post;
		
		$html=
		'
			<li>
				<h5>'.esc_html($label).'</h5>
				<span class="to-legend">'.esc_html($label).'.</span>
				<div class="to-field-disabled">
					'.esc_html($post->ID).'
					<a href="#" class="to-copy-to-clipboard to-float-right" data-clipboard-text="'.esc_attr($post->ID).'" data-label-on-success="'.esc_attr__('Copied!','chauffeur-booking-system').'">'.esc_html__('Copy','chauffeur-booking-system').'</a>
				</div>
			</li>		
		';
		
		return($html);
	}
	
	/**************************************************************************/
	
	static function displayDatePeriod($start,$stop)
	{
		$Validation=new CHBSValidation();
		
		if($Validation->isNotEmpty($start))
			$start=esc_html__('From: ','chauffeur-booking-system').$start;	
		
		if($Validation->isNotEmpty($stop))
			$stop=esc_html__('To: ','chauffeur-booking-system').$stop;	
			
		if(($Validation->isNotEmpty($start)) && ($Validation->isNotEmpty($stop)))
			$stop=' - '.$stop;
		
		return($start.$stop);
	}
	
	/**************************************************************************/
	
	static function displayAddress($data,$prefix=null,$additionalData=array())
	{
		$Country=new CHBSCountry();
		$Validation=new CHBSValidation();

		/***/
		
		$address=array();
		if(!is_null($prefix))
		{
			foreach($data as $index=>$value)
			{
				if(preg_match('/^'.$prefix.'/',$index))
				{
					$index=preg_replace('/'.$prefix.'/','',$index);
					$address[$index]=$value;
				}
			}
		}
		else $address=$data;
		
		/***/
		
		$html=null;
		if(array_key_exists('name',$address))
			$html=$address['name'];
		elseif(array_key_exists('company_name',$address))
			$html=$address['company_name'];		
		
		/***/
		
		$streetName='';
		if(array_key_exists('street',$address))
			$streetName=$address['street'];
		elseif(array_key_exists('street_name',$address)) 
			$streetName=$address['street_name'];
			
		if($Validation->isNotEmpty($streetName))
		{
			if($Validation->isNotEmpty($html)) $html.='<br>';
			
			if((int)CHBSOption::getOption('address_format_type')===2)
				$html.=trim($address['street_number'].' '.$streetName);
			else $html.=trim($streetName.' '.$address['street_number']);
		}		

		/***/
		
		if(array_key_exists('postal_code',$address))
			$address['postcode']=$address['postal_code'];
		
		if((int)CHBSOption::getOption('address_format_type')===2)
		{
			if(($Validation->isNotEmpty($address['postcode'])) || ($Validation->isNotEmpty($address['city'])) || ($Validation->isNotEmpty($address['state'])))
			{
				if($Validation->isNotEmpty($html)) $html.='<br>';
				
				$city=$address['city'];
				$statePostcode=trim($address['state'].' '.$address['postcode']);
				
				if(($Validation->isNotEmpty($city)) && ($Validation->isNotEmpty($statePostcode)))
					$html.=trim($city.', '.$statePostcode);
				else $html.=trim($city.' '.$statePostcode);
			}
		}
		else
		{
			if($Validation->isNotEmpty($address['postcode']) || $Validation->isNotEmpty($address['city']))
			{
				if($Validation->isNotEmpty($html)) $html.='<br>';
				$html.=trim($address['postcode'].' '.$address['city']);
			}
			
			if($Validation->isNotEmpty($address['state']))
			{
				if($Validation->isNotEmpty($html)) $html.='<br>';
				$html.=$address['state'];
			}
		}
		
		/***/
		
		$country='';
		$countryCode='';
		
		if(array_key_exists('country',$address)) 
			$countryCode=$address['country'];
		elseif(array_key_exists('country_code',$address)) 
			$countryCode=$address['country_code'];
			
		if($Country->isCountry($countryCode))
			$country=$Country->getCountryName($countryCode);
		
		if($Validation->isNotEmpty($country))
		{
			if($Validation->isNotEmpty($html)) $html.='<br>';
			$html.=$country;			
		}
		
		foreach($additionalData as $index=>$value)
		{
			if($Validation->isNotEmpty($value['value']))
			{
				if($Validation->isNotEmpty($html)) $html.='<br>';
				if(array_key_exists('label',$value)) $html.=$value['label'];
				$html.=$value['value'];
			}
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	static function getAddress($bookingMeta)
	{
		$Validation=new CHBSValidation();
				
		if((is_array($bookingMeta)) && (array_key_exists('address',$bookingMeta)) && ($Validation->isNotEmpty($bookingMeta['address']))) return($bookingMeta['address']);
				
		return($bookingMeta['formatted_address']);
	}
	
	/**************************************************************************/
	
	static function addInlineScript($name,$script,$type=1)
	{
		if((int)$type===1)
		{
			wp_add_inline_script($name,$script);
		}
		else
		{
			echo 
			'
				<script>
					'.$script.'
				</script>
			';
		}
	}
	
	/**************************************************************************/
	
	static function addInlineStyle($name,$style,$type=1)
	{
		if((int)$type===1)
		{
			wp_add_inline_style($name,$style);
		}
		else
		{
			echo 
			'
				<style>
					'.$style.'
				</style>
			';
		}
	}
	
	/**************************************************************************/
	
	static function editPostLink($postId,$label=null,$target='_blank')
	{
		$Validation=new CHBSValidation();
		
		if($Validation->isEmpty($label)) $label=__('Edit','chauffeur-booking-system');
		
		$link='<a href="'.esc_url(self::editPostURLAddress($postId)).'" target="'.esc_attr($target).'">'.esc_html($label).'</a>';
		return($link);
	}
	
	/**************************************************************************/
	
	static function editPostURLAddress($postId)
	{
		$url=admin_url('post.php?action=edit&post='.$postId);
		return($url);		
	}
	
	/**************************************************************************/
	
	static function displayDictionary($data,$dictionary,$link=false,$sort=false)
	{
		if(in_array(-1,$data)) return('');
		
		$html=null;
		$dataSort=array();
		
		$Validation=new CHBSValidation();

		foreach($data as $value)
		{
			if(!array_key_exists($value,$dictionary)) continue;

			if(array_key_exists('post',$dictionary[$value]))
				$label=$dictionary[$value]['post']->post_title;
			else if(array_key_exists('name',$dictionary[$value]))
				$label=$dictionary[$value]['name'];	
			else $label=$dictionary[$value][0];			

			$dataSort[$value]=$label;
		}

		if($sort) asort($dataSort);

		$data=$dataSort;
		
		foreach($data as $index=>$value)
		{
			$label=$value;
			
			if($link) $label='<a href="'.esc_url(CHBSHelper::editPostURLAddress($index)).'">'.$value.'</a>';
			if($Validation->isNotEmpty($html)) $html.=', ';
			$html.=$label;
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	static function displayBoolValue($value,$type=1)
	{
		$label=array
		(
			1=>array
			(
				1=>__('Enable','chauffeur-booking-system'),
				0=>__('Disable','chauffeur-booking-system'),
			),
			2=>array
			(
				1=>__('Yes','chauffeur-booking-system'),
				0=>__('No','chauffeur-booking-system'),
			)
		);
		
		if(!in_array($value,array(1,0))) $value=0;
		
		return($label[$type][$value]);
	}
	
	/**************************************************************************/
	
	static function createShortcode($name)
	{
		return('['.$name.']');
	}

	/**************************************************************************/
	
	static function createNonceField($name,$action='savePost')
	{
		return(wp_nonce_field($action,$name.'_noncename',false,false));
	}	
	
	/**************************************************************************/
	
	static function verifyNonce($name,$action=null,$prefix=PLUGIN_CHBS_CONTEXT)
	{
		if(is_null($action)) $action=$name;
		
		if(!preg_match('/\_noncename$/',$name)) $name.='_noncename';
		if(!preg_match('/^'.$prefix.'\_/',$name)) $name=$prefix.'_'.$name;
		
		if(array_key_exists($name,$_POST))
		{
			if(!wp_verify_nonce($_POST[$name],$action)) return(false);
			unset($_POST[$name]);
		}
		else if(array_key_exists($name,$_GET))
		{
			if(!wp_verify_nonce($_GET[$name],$action)) return(false);
			unset($_GET[$name]);
		}		
		
		return(true);
	}
	
	/**************************************************************************/
	
	static function displayNonce($data)
	{
		if(array_key_exists('nonce',$data))
			return($data['nonce']);
		
		return('');
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/