<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingExtra
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->mandatoryType=array
		(
			0=>array(__('Disable','chauffeur-booking-system')),
			2=>array(__('Disable (and equal to passenger quantity)','chauffeur-booking-system')),
			1=>array(__('Enable (and customer can enter own quantity)','chauffeur-booking-system'))
		);
	}

	/**************************************************************************/
	
	function getMandatoryTypeName($mandatoryType)
	{
		return($this->mandatoryType[$mandatoryType][0]);
	}
	
	/**************************************************************************/
	
	function isMandatoryTypeName($mandatoryType)
	{
		return(array_key_exists($mandatoryType,$this->mandatoryType) ? true : false);
	}	
	
	/**************************************************************************/
	
	function getMandatoryType()
	{
		return($this->mandatoryType);
	}	
	
	/**************************************************************************/

	public function init()
	{
		$this->registerCPT();
	}

	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_booking_extra');
	}

	/**************************************************************************/

	public static function getCPTCategoryName()
	{
		return(self::getCPTName().'_c');
	}

	/**************************************************************************/

	private function registerCPT()
	{
		register_post_type
		(
			self::getCPTName(),
			array
			(
				'labels'=>array
				(
					'name'=>__('Booking Extras','chauffeur-booking-system'),
					'singular_name'=>__('Booking Extra','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Booking Add-on','chauffeur-booking-system'),
					'edit_item'=>__('Edit Booking Extra','chauffeur-booking-system'),
					'new_item'=>__('New Booking Extra','chauffeur-booking-system'),
					'all_items'=>__('Booking Extras','chauffeur-booking-system'),
					'view_item'=>__('View Booking Extra','chauffeur-booking-system'),
					'search_items'=>__('Search Booking Extras','chauffeur-booking-system'),
					'not_found'=>__('No Booking Extras Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Booking Extras Found in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Booking Extras','chauffeur-booking-system')
				),	
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>array('title','page-attributes','thumbnail')  
			)
		);

		register_taxonomy
		(
			self::getCPTCategoryName(),
			self::getCPTName(),
			array
			(
				'label' =>	__('Booking Extra Categories','chauffeur-booking-system'),
				'hierarchical'  =>  false
			)
		);

		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_booking_extra',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));
	}

	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_extra',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_booking_extra');
		
		$data['dictionary']['vehicle']=$Vehicle->getDictionary();
		$data['dictionary']['tax_rate']=$TaxRate->getDictionary();
		$data['dictionary']['service_type']=$ServiceType->getServiceType();
		$data['dictionary']['transfer_type']=$TransferType->getTransferType();
		$data['dictionary']['mandatory_type']=$this->getMandatoryType();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_booking_extra.php');
		echo $Template->output();			
	}
	
	 /**************************************************************************/
	
	function adminCreateMetaBoxClass($class) 
	{
		array_push($class,'to-postbox-1');
		return($class);
	}

	/**************************************************************************/
	
	function setPostMetaDefault(&$meta)
	{
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();		
		
		CHBSHelper::setDefault($meta,'description','');
		
		CHBSHelper::setDefault($meta,'vehicle_id',array(-1));
		
		CHBSHelper::setDefault($meta,'quantity_enable','1');
		CHBSHelper::setDefault($meta,'quantity_max','1');
		CHBSHelper::setDefault($meta,'mandatory','0');
		
		CHBSHelper::setDefault($meta,'price',CHBSPrice::getDefaultPrice());
	   
		CHBSHelper::setDefault($meta,'read_more_link_url_address','');
		
		CHBSHelper::setDefault($meta,'service_type_id_enable',array_keys($ServiceType->getServiceType()));
		CHBSHelper::setDefault($meta,'transfer_type_id_enable',array_keys($TransferType->getTransferType()));
		
		$TaxRate=new CHBSTaxRate();
		CHBSHelper::setDefault($meta,'tax_rate_id',$TaxRate->getDefaultTaxPostId());
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_booking_extra_noncename','savePost')===false) return(false);
		
		$meta=array();

		$TaxRate=new CHBSTaxRate();
		$Vehicle=new CHBSVehicle();
		$Validation=new CHBSValidation();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();   
		
		$this->setPostMetaDefault($meta);
	  
		/***/
		
		$meta['vehicle_id']=(array)CHBSHelper::getPostValue('vehicle_id');		
		if(in_array(-1,$meta['vehicle_id']))
		{
			$meta['vehicle_id']=array(-1);
		}
		else
		{
			$vehicle=$Vehicle->getDictionary();
			foreach($meta['vehicle_id'] as $index=>$value)
			{
				if(!isset($vehicle[$value]))
					unset($meta['vehicle_id'][$index]);				
			}
		}
		
		if(!count($meta['vehicle_id']))
			$meta['vehicle_id']=array(-1);
		
		/***/
		
		$meta['description']=CHBSHelper::getPostValue('description');
		
		$meta['quantity_enable']=CHBSHelper::getPostValue('quantity_enable');
		if(!in_array($meta['quantity_enable'],array(0,1,2)))
			$meta['quantity_enable']=1;
		
		if($meta['quantity_enable']!=1) $meta['quantity_max']=1;
		else
		{
			$meta['quantity_max']=CHBSHelper::getPostValue('quantity_max');
			if(!$Validation->isNumber($meta['quantity_max'],1,9999))
				$meta['quantity_max']=1;
		}
		
		$meta['mandatory']=CHBSHelper::getPostValue('mandatory');
		if(!$Validation->isBool($meta['mandatory']))
			$meta['mandatory']=0;		
		
		$meta['price']=CHBSHelper::getPostValue('price');
		if(!CHBSPrice::isPrice($meta['price'],false))
		   $meta['price']=CHBSPrice::getDefaultPrice();  
		
		$meta['price']=CHBSPrice::formatToSave($meta['price'],false);
		
		$meta['tax_rate_id']=CHBSHelper::getPostValue('tax_rate_id');
		if(!$TaxRate->isTaxRate($meta['tax_rate_id']))
			$meta['tax_rate_id']=0;
		
		$meta['read_more_link_url_address']=CHBSHelper::getPostValue('read_more_link_url_address');
		
		/***/
		
		$meta['service_type_id_enable']=(array)CHBSHelper::getPostValue('service_type_id_enable');
		foreach($meta['service_type_id_enable'] as $index=>$value)
		{
			if(!$ServiceType->isServiceType($value))
				unset($meta['service_type_id_enable'][$index]);
		}
		
		$meta['transfer_type_id_enable']=(array)CHBSHelper::getPostValue('transfer_type_id_enable');
		foreach($meta['transfer_type_id_enable'] as $index=>$value)
		{
			if(!$TransferType->isTransferType($value))
				unset($meta['transfer_type_id_enable'][$index]);
		}
		
		/***/
		
		foreach($meta as $index=>$value)
			CHBSPostMeta::updatePostMeta($postId,$index,$value);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array(),$sortingType=3)
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'booking_extra_id'=>0,
			'category_id'=>array()
		);
		
		$attribute=shortcode_atts($default,$attr);
		
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1
		);
		
		if(in_array($sortingType,array(3,4)))
		{
			$argument['orderby']='menu_order';
		}
		if(in_array($sortingType,array(5,6)))
		{
			$argument['orderby']='title';
		}
		
		if(in_array($sortingType,array(3,5)))
		{
			$argument['order']='asc';
		}
		if(in_array($sortingType,array(4,6)))
		{
			$argument['order']='desc';
		}	
		
		if($attribute['booking_extra_id'])
			$argument['p']=$attribute['booking_extra_id'];

		if(!is_array($attribute['category_id']))
			$attribute['category_id']=array($attribute['category_id']);

		if(array_sum($attribute['category_id']))
		{
			$argument['tax_query']=array
			(
				array
				(
					'taxonomy'=>self::getCPTCategoryName(),
					'field'=>'term_id',
					'terms'=>$attribute['category_id'],
					'operator'=>'IN'
				)
			);
		}

		$query=new WP_Query($argument);
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			$dictionary[$post->ID]['post']=$post;
			$dictionary[$post->ID]['meta']=CHBSPostMeta::getPostMeta($post);
			$dictionary[$post->ID]['category'][0]=array();
					   
			$category=get_the_terms($post->ID,self::getCPTCategoryName());
			if(is_array($category))
			{
				foreach($category as $value)
					$dictionary[$post->ID]['category'][0][$value->{'term_id'}]=(array)$value;
			}
		}
		
		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function getCategory()
	{
		$category=array();
		
		$result=get_terms(self::getCPTCategoryName());
		if(is_wp_error($result)) return($category);
		
		foreach($result as $value)
			$category[$value->{'term_id'}]=(array)$value;
		
		return($category);
	}
	
	/**************************************************************************/
	
	function calculatePrice($bookingExtra,$taxRate)
	{
		$Currency=new CHBSCurrency();
		
		/***/
		
		$taxRateValue=0;
		$taxRateId=$bookingExtra['meta']['tax_rate_id'];
		
		if(isset($taxRate[$taxRateId]))
			$taxRateValue=$taxRate[$taxRateId]['meta']['tax_rate_value'];
		
		/***/
		
		$currency=$Currency->getCurrency(CHBSCurrency::getFormCurrency());
		
		/***/
		
		if(!array_key_exists('quantity',$bookingExtra))
			$bookingExtra['quantity']=1;
		
		/***/
		
		$bookingExtra['meta']['price']=CHBSPrice::numberFormat($bookingExtra['meta']['price']);
		
		$priceNetValue=CHBSPrice::numberFormat($bookingExtra['meta']['price']*CHBSCurrency::getExchangeRate());		   
		$priceGrossValue=CHBSPrice::calculateGross($priceNetValue,$taxRateId);
		
		$priceNetFormat=CHBSPrice::format($priceNetValue,CHBSCurrency::getFormCurrency());
		
		$sumNetValue=$priceNetValue*$bookingExtra['quantity'];
		$sumGrossValue=$priceGrossValue*$bookingExtra['quantity'];

		$priceGrossFormat=CHBSPrice::format($priceGrossValue,CHBSCurrency::getFormCurrency());
		$sumGrossFormat=CHBSPrice::format($sumGrossValue,CHBSCurrency::getFormCurrency());

		$sumNetFormat=CHBSPrice::format($sumNetValue,CHBSCurrency::getFormCurrency());

		$priceNetValue=CHBSPrice::numberFormat($priceNetValue);
		$priceGrossValue=CHBSPrice::numberFormat($priceGrossValue);	   
		
		$priceNetValue=CHBSPrice::numberFormat($priceNetValue);
		$priceGrossValue=CHBSPrice::numberFormat($priceGrossValue);	   
		
		/***/
		
		$data=array
		(
			'price'=>array
			(
				'net'=>array
				(
					'value'=>$priceNetValue,
					'format'=>$priceNetFormat,
				),
				'gross'=>array
				(
					'value'=>$priceGrossValue,
					'format'=>$priceGrossFormat
				)
			),
			'sum'=>array
			(
				'net'=>array
				(
					'value'=>$sumNetValue,
					'format'=>$sumNetFormat
				),
				'gross'=>array
				(
					'value'=>$sumGrossValue,
					'format'=>$sumGrossFormat
				)
			),
			'currency'=>$currency
		);
		
		return($data);
	}
	
	/**************************************************************************/
	
	function validate($data,$bookingForm,$taxRateDictionary)
	{
		$bookingExtra=array();
		$bookingExtraId=preg_split('/,/',$data['booking_extra_id']);
		
		foreach($bookingExtraId as $value)
		{
			if(array_key_exists($value,$bookingForm['dictionary']['booking_extra']))
			{
				$quantity=(int)$data['booking_extra_'.$value.'_quantity'];
				
				if($bookingForm['dictionary']['booking_extra'][$value]['meta']['quantity_enable']==1) 
				{
					if(!(($quantity>=1) && ($quantity<=$bookingForm['dictionary']['booking_extra'][$value]['meta']['quantity_max']))) 
						$quantity=1;
				}
				else $quantity=1;
				
				if((int)$bookingForm['dictionary']['booking_extra'][$value]['meta']['quantity_enable']===2)
				{
					if(CHBSBookingHelper::isPassengerEnable($bookingForm['meta'],$data['service_type_id']))
					{
						$quantity=CHBSBookingHelper::getPassenegerSum($bookingForm['meta'],$data);
					}
				}
				
				$taxValue=0;
				if(isset($taxRateDictionary[$bookingForm['dictionary']['booking_extra'][$value]['meta']['tax_rate_id']]))
					$taxValue=$taxRateDictionary[$bookingForm['dictionary']['booking_extra'][$value]['meta']['tax_rate_id']]['meta']['tax_rate_value'];
				
				/***/
				
				$price=$bookingForm['dictionary']['booking_extra'][$value]['meta']['price'];
				
				if(CHBSCurrency::getBaseCurrency()!=CHBSCurrency::getFormCurrency())
				{
					$rate=0;
					$dictionary=CHBSOption::getOption('currency_exchange_rate');

					if(array_key_exists(CHBSCurrency::getFormCurrency(),$dictionary))
						$rate=$dictionary[CHBSCurrency::getFormCurrency()];

					$price*=$rate;
				}
				
				/***/
				
				array_push($bookingExtra,array
				(
					'id'=>$value,
					'name'=>$bookingForm['dictionary']['booking_extra'][$value]['post']->post_title,
					'price'=>$price,
					'quantity'=>$quantity,
					'tax_rate_value'=>$taxValue,
					'note'=>$data['booking_extra_'.$value.'_note']
				));
			}
		}
		
		return($bookingExtra);
	}
	
	/**************************************************************************/
	
	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>__('Title','chauffeur-booking-system'),
			'price'=>__('Price','chauffeur-booking-system'),
			'quantity'=>__('Quantity','chauffeur-booking-system'),
			'service_type'=>__('Service types','chauffeur-booking-system'),
			'transfer_type'=>__('Transfer types','chauffeur-booking-system'),
			'vehicle'=>__('Vehicles','chauffeur-booking-system')
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$Vehicle=new CHBSVehicle();
		$Validation=new CHBSValidation();
		$ServiceType=new CHBSServiceType();
		$TransferType=new CHBSTransferType();
		
		$vehicleDictionary=$Vehicle->getDictionary();
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		switch($column) 
		{
			case 'price':
				
				echo CHBSPrice::format($meta['price'],CHBSOption::getOption('currency'));
				
			break;
		
			case 'quantity':
				
				echo 
				'
					<table class="to-table-post-list">
						<tr>
							<td>'.esc_html__('Enabled','chauffeur-booking-system').'</td>
							<td>'.$this->getMandatoryTypeName($meta['quantity_enable']).'</td>
						</tr>	
						<tr>
							<td>'.esc_html__('Maximum','chauffeur-booking-system').'</td>
							<td>'.$meta['quantity_max'].'</td>
						</tr>	
						<tr>
							<td>'.esc_html__('Mandatory','chauffeur-booking-system').'</td>
							<td>'.((int)$meta['mandatory']===1 ? esc_html__('Yes','chauffeur-booking-system') : esc_html__('No','chauffeur-booking-system')).'</td>
						</tr>
					</table>
				';
				
			break;
		
			case 'service_type':
				
				$html=null;
				
				if(is_array($meta['service_type_id_enable']))
				{
					foreach($meta['service_type_id_enable'] as $value)
					{
						if($Validation->isNotEmpty($html)) $html.=', ';
						$html.=$ServiceType->getServiceTypeName($value);
					}
				}
				
				if($Validation->isEmpty($html)) $html='-';
					
				echo $html;
				
			break;	
		
			case 'transfer_type':
				
				$html=null;
				
				if(is_array($meta['transfer_type_id_enable']))
				{
					foreach($meta['transfer_type_id_enable'] as $value)
					{
						if($Validation->isNotEmpty($html)) $html.=', ';
						$html.=$TransferType->getTransferTypeName($value);
					}
				}
				
				if($Validation->isEmpty($html)) $html='-';
					
				echo $html;
				
			break;		
		
			case 'vehicle':
				
				$html=null;
				
				$vehicle=$meta['vehicle_id'];
				
				if(in_array(-1,$vehicle)) $html=esc_html__('All vehicles','chauffeur-booking-system');
				else
				{
					foreach($vehicle as $vehicleId)
					{
						if($Validation->isNotEmpty($html)) $html.=', ';
						$html.='<a href="'.get_edit_post_link($vehicleId).'" target="_blank">'.esc_html($vehicleDictionary[$vehicleId]['post']->post_title).'</a>';
					}
				}
	
				echo $html;
				
			break;	
		}
	}
	
	/**************************************************************************/
	
	function manageEditSortableColumns($column)
	{
		return($column);	   
	}
		
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/