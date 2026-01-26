<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingFormElement
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->fieldType=array
		(
			4=>array(__('Date','chauffeur-booking-system')),
			3=>array(__('File','chauffeur-booking-system')),
			6=>array(__('Location','chauffeur-booking-system')),
			1=>array(__('Text','chauffeur-booking-system')),
			5=>array(__('Time','chauffeur-booking-system')),
			2=>array(__('Select list','chauffeur-booking-system'))
		);		
		
		$this->fieldLayout=array
		(
			1=>array(__('1/1','chauffeur-booking-system'),1,'chbs-form-field-width-100'),
			2=>array(__('1/2+1/2','chauffeur-booking-system'),2,'chbs-form-field-width-50'),
			3=>array(__('1/3+1/3+1/3','chauffeur-booking-system'),3,'chbs-form-field-width-33')
		);		
	}
	
	/**************************************************************************/
	
	function getFieldType()
	{
		return($this->fieldType);
	}
	
	/**************************************************************************/
	
	function isFieldType($fieldType)
	{
		return(array_key_exists($fieldType,$this->getFieldType()) ? true : false);
	}
	
	/**************************************************************************/
	
	function getFieldLayout()
	{
		return($this->fieldLayout);
	}
	
	/**************************************************************************/
	
	function isFieldLayout($fieldLayout)
	{
		return(array_key_exists($fieldLayout,$this->getFieldLayout()) ? true : false);
	}
	
	/**************************************************************************/
	
	function getFieldLayoutData($fieldLayout,$data=1)
	{
		if(!$this->isFieldLayout($fieldLayout)) return($this->fieldLayout[1][$data]);
		return($this->fieldLayout[$fieldLayout][$data]);
	}
	
	/**************************************************************************/
	   
	function save($bookingFormId)
	{
		/***/
		
		$formElementPanel=array();
		$formElementPanelPost=CHBSHelper::getPostValue('form_element_panel');
		
		if(isset($formElementPanelPost['id']))
		{
			$Validation=new CHBSValidation();
			
			foreach($formElementPanelPost['id'] as $index=>$value)
			{
				if($Validation->isEmpty($formElementPanelPost['label'][$index])) continue;
				
				if(!$Validation->isBool($formElementPanelPost['toggle_visibility_enable'][$index]))
					$formElementPanelPost['toggle_visibility_enable'][$index]=0;
				
				if($Validation->isEmpty($value))
					$value=CHBSHelper::createId();
				
				$formElementPanel[]=array('id'=>$value,'label'=>$formElementPanelPost['label'][$index],'toggle_visibility_enable'=>$formElementPanelPost['toggle_visibility_enable'][$index]);
			}
		}
		
		CHBSPostMeta::updatePostMeta($bookingFormId,'form_element_panel',$formElementPanel); 
		
		$meta=CHBSPostMeta::getPostMeta($bookingFormId);
		
		/***/
		
		$formElementField=array();
		$formElementFieldPost=CHBSHelper::getPostValue('form_element_field');	
		
		if(isset($formElementFieldPost['id']))
		{
			$Validation=new CHBSValidation();
			$ServiceType=new CHBSServiceType();
			
			$panelDictionary=$this->getPanel($meta);
			
			foreach($formElementFieldPost['id'] as $index=>$value)
			{
				if(!isset($formElementFieldPost['label'][$index],$formElementFieldPost['field_type'][$index],$formElementFieldPost['mandatory'][$index],$formElementFieldPost['dictionary'][$index],$formElementFieldPost['message_error'][$index],$formElementFieldPost['panel_id'][$index])) continue;
				
				if($Validation->isEmpty($formElementFieldPost['label'][$index])) continue;
				
				if(!$this->isFieldType($formElementFieldPost['field_type'][$index])) continue;
			
				if(!$Validation->isBool((int)$formElementFieldPost['mandatory'][$index])) continue;
				else
				{
					if((int)$formElementFieldPost['mandatory'][$index]===1)
					{	
						if($Validation->isEmpty($formElementFieldPost['message_error'][$index])) continue;
					}
				}
				
				if($formElementFieldPost['field_type'][$index]===2)
				{
					if($Validation->isEmpty($formElementFieldPost['dictionary'][$index])) continue;
				}
				
				if(!$this->isFieldLayout($formElementFieldPost['field_layout'][$index])) continue;
				
				if(!$this->isPanel($formElementFieldPost['panel_id'][$index],$panelDictionary)) continue;

				/***/
				
				$serviceTypeIdEnable=preg_split('/\./',$formElementFieldPost['service_type_id_enable_hidden'][$index]);
				if(is_array($serviceTypeIdEnable))
				{
					foreach($serviceTypeIdEnable as $index2=>$value2)
					{
						if(!$ServiceType->isServiceType($value2))
							unset($serviceTypeIdEnable[$index2]);
					}
				}
				
				if(!is_array($serviceTypeIdEnable)) $serviceTypeIdEnable=array();
				
				/***/
				
				$geofencePickup=preg_split('/\./',$formElementFieldPost['geofence_pickup_hidden'][$index]);
				if(is_array($geofencePickup))
				{
					if(in_array(-1,$geofencePickup)) $geofencePickup=array(-1);
				}
				
				if(!is_array($geofencePickup)) $geofencePickup=array(-1);
				
				/***/
				
				$geofenceDropoff=preg_split('/\./',$formElementFieldPost['geofence_dropoff_hidden'][$index]);
				if(is_array($geofenceDropoff))
				{
					if(in_array(-1,$geofenceDropoff)) $geofenceDropoff=array(-1);
				}
				
				if(!is_array($geofenceDropoff)) $geofenceDropoff=array(-1);
				
				/***/
				
				if($Validation->isEmpty($value))
					$value=CHBSHelper::createId();
				
				$formElementField[]=array('id'=>$value,'label'=>$formElementFieldPost['label'][$index],'field_type'=>$formElementFieldPost['field_type'][$index],'mandatory'=>$formElementFieldPost['mandatory'][$index],'dictionary'=>$formElementFieldPost['dictionary'][$index],'field_layout'=>$formElementFieldPost['field_layout'][$index],'message_error'=>$formElementFieldPost['message_error'][$index],'panel_id'=>$formElementFieldPost['panel_id'][$index],'service_type_id_enable'=>$serviceTypeIdEnable,'geofence_pickup'=>$geofencePickup,'geofence_dropoff'=>$geofenceDropoff);
			}
		}  
		
		CHBSPostMeta::updatePostMeta($bookingFormId,'form_element_field',$formElementField); 
		
		/***/
		
		$formElementAgreement=array();
		$formElementAgreementPost=CHBSHelper::getPostValue('form_element_agreement');		
		
		if(isset($formElementAgreementPost['id']))
		{
			$Validation=new CHBSValidation();
			
			foreach($formElementAgreementPost['id'] as $index=>$value)
			{
				if(!isset($formElementAgreementPost['text'][$index])) continue;
				if($Validation->isEmpty($formElementAgreementPost['text'][$index])) continue;
				if(!$Validation->isBool($formElementAgreementPost['mandatory'][$index])) continue;
				
				/***/
				
				$serviceTypeIdEnable=preg_split('/\./',$formElementAgreementPost['service_type_id_enable_hidden'][$index]);
				if(is_array($serviceTypeIdEnable))
				{
					foreach($serviceTypeIdEnable as $index2=>$value2)
					{
						if(!$ServiceType->isServiceType($value2))
							unset($serviceTypeIdEnable[$index2]);
					}
				}
				
				if(!is_array($serviceTypeIdEnable)) $serviceTypeIdEnable=array();
				
				/***/
				
				if($Validation->isEmpty($value))
					$value=CHBSHelper::createId();
				
				$formElementAgreement[]=array('id'=>$value,'text'=>$formElementAgreementPost['text'][$index],'mandatory'=>$formElementAgreementPost['mandatory'][$index],'service_type_id_enable'=>$serviceTypeIdEnable);
			}
		}	
		
		CHBSPostMeta::updatePostMeta($bookingFormId,'form_element_agreement',$formElementAgreement);		
	}
	
	/**************************************************************************/
	
	function getPanel($meta)
	{
		$panel=array
		(
			array
			(
				'id'=>1,
				'label'=>__('[Contact details]','chauffeur-booking-system')
			),
			array
			(
				'id'=>2,
				'label'=>__('[Billing address]','chauffeur-booking-system')				
			)
		);
			 
		if(isset($meta['form_element_panel']))
		{
			foreach($meta['form_element_panel'] as $value)
				$panel[]=$value;
		}
		
		return($panel);
	}

	/**************************************************************************/
	
	function isPanel($panelId,$panelDictionary)
	{
		foreach($panelDictionary as $value)
		{
			if($value['id']==$panelId) return(true);
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	function getFieldValueByLabel($label,$meta)
	{
		if(is_array($meta))
		{
			foreach($meta['form_element_field'] as $value)
			{
				if($value['label']==$label) return($value['value']);
			}
		}
		
		return(null);
	}
		
	/**************************************************************************/
	
	function createField($panelId,$serviceTypeId,$bookingForm)
	{
		$html=array(null,null);
		
		$Validation=new CHBSValidation();
		$GeofenceChecker=new CHBSGeofenceChecker();
		
		$data=CHBSHelper::getPostOption();
		
		if(!array_key_exists('form_element_field',$bookingForm['meta'])) return(null);
	
		$i=0;
		
		/***/
		
		$panelData=array();
		foreach($bookingForm['meta']['form_element_panel'] as $value)
		{
			if($value['id']==$panelId) $panelData=$value;
		}
		
		/***/
		
		foreach($bookingForm['meta']['form_element_field'] as $value)
		{	
			if($value['panel_id']==$panelId)
			{
				if(array_key_exists('service_type_id_enable',$value))
				{
					if(is_array($value['service_type_id_enable']))
					{
						if(!in_array($serviceTypeId,$value['service_type_id_enable']))
							continue;
					}
				}
				
				$pickupLocation=$data['pickup_location_coordinate_service_type_'.$data['service_type_id']];
				if($GeofenceChecker->locationInGeofence($value['geofence_pickup'],$bookingForm['dictionary']['geofence'],$pickupLocation)===false) continue;
				
				$dropoffLocation=$data['dropoff_location_coordinate_service_type_'.$data['service_type_id']];
				if($GeofenceChecker->locationInGeofence($value['geofence_dropoff'],$bookingForm['dictionary']['geofence'],$dropoffLocation)===false) continue;
				
				$i++;
				
				$columnClass=$this->getFieldLayoutData($value['field_layout'],2);
				$columnNumber=(int)$this->getFieldLayoutData($value['field_layout'],1);
				
				if($i===1)
				{
					$html[1].='<div'.CHBSHelper::createCSSClassAttribute(array('chbs-clear-fix')).'>';
				}
				
				$name='form_element_field_'.$value['id'];
			 
				$html[1].=
				'
						<div class="chbs-form-field '.$columnClass.'">
							<label>'.esc_html($value['label']).((int)$value['mandatory']===1 ? ' *' : '').'</label>
				';
				
				if(($bookingForm['booking_edit']->isBookingEdit()) && (!array_key_exists($name,$data)))
				{
					$valueToSet=$bookingForm['booking_edit']->getBookingFormElementValue($value['id']);
				}
				else $valueToSet=CHBSHelper::getPostValue($name);
				
				if((int)$value['field_type']===1)
				{
					$html[1].=
					'
						<input type="text" name="'.CHBSHelper::getFormName($name,false).'"  value="'.esc_attr($valueToSet).'"/>	
					';					
				}
				elseif((int)$value['field_type']===2)
				{
					$fieldHtml=null;
					$fieldValue=preg_split('/;/',$value['dictionary']);

					foreach($fieldValue as $fieldValueValue)
					{
						if($Validation->isNotEmpty($fieldValueValue))
							$fieldHtml.='<option value="'.esc_attr($fieldValueValue).'"'.CHBSHelper::selectedIf($fieldValueValue,$valueToSet,false).'>'.esc_html($fieldValueValue).'</option>';
					}

					$html[1].=
					'
						<select name="'.CHBSHelper::getFormName($name,false).'">
							'.$fieldHtml.'
						</select>
					';	
				}
				elseif((int)$value['field_type']===3)
				{
					$classButton=array(array('chbs-file-upload','chbs-button','chbs-button-style-3'),array('chbs-file-remove'));
			
					$fileName=null;
			
					if($Validation->isEmpty(CHBSHelper::getPostValue($name.'_name')))
						array_push($classButton[1],'chbs-hidden');
					else 
					{
						$fileName=CHBSHelper::getPostValue($name.'_name');
						array_push($classButton[0],'chbs-hidden');
					}

					$html[1].=
					'
						<div></div>
						<div'.CHBSHelper::createCSSClassAttribute($classButton[0]).'>
							<span>'.esc_html__('Upload a file','chauffeur-booking-system').'</span>
							<input type="file" name="'.CHBSHelper::getFormName($name,false).'"></input>
						</div>
						<div'.CHBSHelper::createCSSClassAttribute($classButton[1]).'>
							<span>'.esc_html__('Uploaded file:','chauffeur-booking-system').'<span>'.esc_html($fileName).'</span></span>
							<span'.CHBSHelper::createCSSClassAttribute(array('chbs-button','chbs-button-style-3')).'>'.esc_html__('Remove file','chauffeur-booking-system').'</span>
						</div>
						<input type="hidden" name="'.CHBSHelper::getFormName($name,false).'_type" value="'.esc_attr(CHBSHelper::getPostValue($name.'_type')).'"/>
						<input type="hidden" name="'.CHBSHelper::getFormName($name,false).'_name" value="'.esc_attr(CHBSHelper::getPostValue($name.'_name')).'"/>
						<input type="hidden" name="'.CHBSHelper::getFormName($name,false).'_tmp_name" value="'.esc_attr(CHBSHelper::getPostValue($name.'_tmp_name')).'"/>
					';		
				}
				elseif((int)$value['field_type']===4)
				{
					$html[1].=
					'
						<input type="text" class="chbs-datepicker chbs-datepicker-form-element" name="'.CHBSHelper::getFormName($name,false).'"  value="'.esc_attr($valueToSet).'"/>	
					';						
				}
				elseif((int)$value['field_type']===5)
				{
					$html[1].=
					'
						<input type="text" class="chbs-timepicker chbs-timepicker-form-element" name="'.CHBSHelper::getFormName($name,false).'"  value="'.esc_attr($valueToSet).'"/>	
					';						
				}
				elseif((int)$value['field_type']===6)
				{
					$html[1].=
					'
						<input type="text" class="chbs-autocomplete-form-element" name="'.CHBSHelper::getFormName($name,false).'"  value="'.esc_attr($valueToSet).'"/>
						<input type="hidden" name="'.CHBSHelper::getFormName($name.'_data',false).'" value=""/>	
					';								
				}
				
				$html[1].=
				'							
					</div>
				';	
				
				if($i===$columnNumber)
				{
					$html[1].='</div>';
					$i=0;
				}
			}
		}
		
		if(array_key_exists('form_element_panel',$bookingForm['meta']))
		{
			if(!in_array($panelId,array(1,2)))
			{
				foreach($bookingForm['meta']['form_element_panel'] as $value)
				{
					if($value['id']==$panelId)
					{
						$checkboxHtml=null;
						
						if((int)$value['toggle_visibility_enable']===1)
						{
							$class=array('chbs-form-checkbox');

							if((int)$data['panel_visibility_'.$panelId]===1)
							{
								array_push($class,'chbs-state-selected');
							}

							$checkboxHtml=
							'
								<span'.CHBSHelper::createCSSClassAttribute($class).'>
									<span class="chbs-meta-icon-tick"></span>
								</span>
								<input type="hidden" name="'.CHBSHelper::getFormName('panel_visibility_'.$panelId,false).'" value="'.esc_attr($data['panel_visibility_'.$panelId]).'"/> 	
							';
						}
						
						$html[0].=
						'
							<div class="chbs-clear-fix">
								<label class="chbs-form-label-group">'.$checkboxHtml.esc_html($value['label']).'</label> 
							</div>
						';
					}
				}
			}
		}
		
		$class=array('chbs-panel');
		if((int)$panelData['toggle_visibility_enable']===1)
		{
			if((int)$data['panel_visibility_'.$panelId]!==1)
				$class[]='chbs-hidden';
		}
		
		return($html[0].'<div'.CHBSHelper::createCSSClassAttribute($class).'>'.$html[1].'</div>');
	}
	
	/**************************************************************************/
	
	function createAgreement($meta,$serviceTypeId)
	{
		$html=null;
		$Validation=new CHBSValidation();
		
		if(!array_key_exists('form_element_agreement',$meta)) return($html);
		
		foreach($meta['form_element_agreement'] as $value)
		{
			if(array_key_exists('service_type_id_enable',$value))
			{
				if(is_array($value['service_type_id_enable']))
				{
					if(!in_array($serviceTypeId,$value['service_type_id_enable']))
						continue;
				}
			}
			
			$html.=
			'
				<div class="chbs-clear-fix">
					<span class="chbs-form-checkbox">
						<span class="chbs-meta-icon-tick"></span>
					</span>
					<input type="hidden" name="'.CHBSHelper::getFormName('form_element_agreement_'.$value['id'],false).'" value="0"/> 
					<div>'.$value['text'].'</div>
				</div>	  
			';
		}
		
		if($Validation->isNotEmpty($html))
		{
			$html=
			'
				<h4 class="chbs-agreement-header">'.esc_html__('Agreements','chauffeur-booking-system').'</h4>
				<div class="chbs-agreement">
					'.$html.'
				</div>
			';
		}
		
		return($html);
	}
	
	/**************************************************************************/
	
	function validateField($meta,$data)
	{
		$error=array();
		
		$Geofence=new CHBSGeofence();
		$Validation=new CHBSValidation();
		$GeofenceChecker=new CHBSGeofenceChecker();
		
		if(!array_key_exists('form_element_field',$meta)) return($error);
		
		/***/
		
		$panelData=array();
		foreach($meta['form_element_panel'] as $value)
			$panelData[$value['id']]=$value;

		/***/
		
		$geofenceDictionary=$Geofence->getDictionary();
		
		/***/
		
		foreach($meta['form_element_field'] as $value)
		{
			$name='form_element_field_'.$value['id'];
			
			$name1=$name2=$name;
			
			if((int)$value['field_type']===3) $name2=$name1.='_tmp_name';
			
			if(array_key_exists('service_type_id_enable',$value))
			{
				if(is_array($value['service_type_id_enable']))
				{
					if(!in_array($data['service_type_id'],$value['service_type_id_enable']))
						continue;
				}
			}	
			
			$check=true;
			if($value['panel_id']!=1)
			{
				if((int)$panelData[$value['panel_id']]['toggle_visibility_enable']===1)
				{
					if((int)$data['panel_visibility_'.$value['panel_id']]!==1)
						$check=false;
				}
			}
			
			$pickupLocation=$data['pickup_location_coordinate_service_type_'.$data['service_type_id']];
			if($GeofenceChecker->locationInGeofence($value['geofence_pickup'],$geofenceDictionary,$pickupLocation)===false) continue;
				
			$dropoffLocation=$data['dropoff_location_coordinate_service_type_'.$data['service_type_id']];
			if($GeofenceChecker->locationInGeofence($value['geofence_dropoff'],$geofenceDictionary,$dropoffLocation)===false) continue;
			
			if((int)$value['mandatory']===1)
			{
				if($value['panel_id']==2)
				{
					if((int)$data['client_billing_detail_enable']===1)
					{
						if($Validation->isEmpty($data[$name1]))
							$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>$value['message_error']);							
					}
				}
				else
				{
					if($check)
					{
						switch($value['field_type'])
						{
							case 4:

								if(!$Validation->isDate($data[$name1]))
									$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>$value['message_error']);	

							break;

							case 5:

								if(!$Validation->isTime($data[$name1]))
									$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>$value['message_error']);	

							break;

							default:

								if($Validation->isEmpty($data[$name1]))
									$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>$value['message_error']);								
						}
					}
				}
			}
			else
			{
				if($check)
				{
					switch($value['field_type'])
					{
						case 4:

							if(!$Validation->isDate($data[$name1],true))
								$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>__('Date is not valid.','chauffeur-booking-system'));	

						break;

						case 5:

							if(!$Validation->isTime($data[$name1],true))
								$error[]=array('name'=>CHBSHelper::getFormName($name2,false),'message_error'=>__('Time is not valid.','chauffeur-booking-system'));	

						break;
					}
				}				
			}
		}
		
		return($error);
	}
	
	/**************************************************************************/
	
	function validateAgreement($meta,$data)
	{
		if(!array_key_exists('form_element_agreement',$meta)) return(false);
		
		foreach($meta['form_element_agreement'] as $value)
		{
			if(array_key_exists('service_type_id_enable',$value))
			{
				if(is_array($value['service_type_id_enable']))
				{
					if(!in_array($data['service_type_id'],$value['service_type_id_enable']))
						continue;
				}
			}
			
			$name='form_element_agreement_'.$value['id'];  
			if((!array_key_exists('mandatory',$value)) || ((int)$value['mandatory']===1))
			{
				if((int)$data[$name]!==1) return(true);
			}
		}
		
		return(false);
	}
	
	/**************************************************************************/
	
	function sendBookingField($bookingId,$meta,$data)
	{
		if(!array_key_exists('form_element_field',$meta)) return;
		
		foreach($meta['form_element_field'] as $index=>$value)
		{
			if(array_key_exists('service_type_id_enable',$value))
			{
				if(is_array($value['service_type_id_enable']))
				{
					if(!in_array($data['service_type_id'],$value['service_type_id_enable']))
					{
						unset($meta['form_element_field'][$index]);
						continue;
					}
				}
			}	
			
			$name='form_element_field_'.$value['id']; 
			$meta['form_element_field'][$index]['value']=$data[$name];
			
			if((int)$value['field_type']===6)
			{
				$name2='form_element_field_'.$value['id'].'_data'; 
				$meta['form_element_field'][$index]['data']=$data[$name2];				
			}
			
			if(array_key_exists($name.'_tmp_name',$data))
			{
				$file1=CHBSFile::getUploadPath().'/'.$data[$name.'_tmp_name'];
				$file2=CHBSFile::getUploadPath().'/'.$data[$name.'_name'];
			
				if(rename($file1,$file2))
				{
					$upload=wp_upload_bits($data[$name.'_name'],null,file_get_contents($file2));

					if($upload['error']===false)
					{
						$attachment=array
						(
							'post_title'=>$data[$name.'_name'],
							'post_mime_type'=>$data[$name.'_type'],
							'post_content'=>'',
							'post_status'=>'inherit'
						);

						$attachmentId=wp_insert_attachment($attachment,$upload['file'],$bookingId);

						if($attachmentId>0)
						{
							$attachmentData=wp_generate_attachment_metadata($attachmentId,$upload['file']);
							wp_update_attachment_metadata($attachmentId,$attachmentData);
							
							$meta['form_element_field'][$index]['attachment_id']=$attachmentId;
						}
					}
				}
				
				@unlink($file1);
				@unlink($file2);
			}
		}
		
		CHBSPostMeta::updatePostMeta($bookingId,'form_element_panel',$meta['form_element_panel']);
		CHBSPostMeta::updatePostMeta($bookingId,'form_element_field',$meta['form_element_field']);
	}
	
	/**************************************************************************/
	
	function sendBookingAgreement($bookingId,$meta,$data)
	{
		if(!array_key_exists('form_element_agreement',$meta)) return;
		
		foreach($meta['form_element_agreement'] as $index=>$value)
		{
			if(array_key_exists('service_type_id_enable',$value))
			{
				if(is_array($value['service_type_id_enable']))
				{
					if(!in_array($data['service_type_id'],$value['service_type_id_enable']))
					{
						unset($meta['form_element_agreement'][$index]);
						continue;
					}
				}
			}			
			
			$name='form_element_agreement_'.$value['id']; 
			$meta['form_element_agreement'][$index]['value']=$data[$name];
			$meta['form_element_agreement'][$index]['text']=$value['text'];
		}
		
		CHBSPostMeta::updatePostMeta($bookingId,'form_element_agreement',$meta['form_element_agreement']);
	}
	
	/**************************************************************************/
	
	function displayField($panelId,$meta,$type=1,$argument=array(),$newLineChar='<br/>',$emptyAllow=true)
	{
		$html=null;
		
		$GoogleMap=new CHBSGoogleMap();
		$Validation=new CHBSValidation();
		
		if(!array_key_exists('form_element_field',$meta)) return($html);
		
		foreach($meta['form_element_field'] as $value)
		{
			if($Validation->isEmpty($value['value']))
			{
				if(!$emptyAllow) continue;
			}
						
			if($value['panel_id']==$panelId)
			{
				$fieldValue=esc_html($value['value']);
				$fieldLabel=esc_html($value['label']);
				
				if((int)$value['field_type']===3)
				{
					if(array_key_exists('attachment_id',$value))
					{
						if((int)$value['attachment_id']>0)
						{
							if(!is_null($file=get_post($value['attachment_id'])))
							{
								if($type===1)
									$fieldValue='<a href="'.get_edit_post_link($value['attachment_id']).'" target="_blank">'.esc_html($file->post_title).'</a>';
								else $fieldValue=esc_html($file->post_title);
							}
							else continue;
						}
						else continue;
					}
				}
				
				if($type==1)
				{
					if((int)$value['field_type']===6)
					{
						if($Validation->isNotEmpty($value['data']))
						{	
							$fieldData=json_decode($value['data']);
							$fieldValue.='&nbsp;<a href="'.$GoogleMap->getRouteURLAddress(array(array('lat'=>$fieldData->{'lat'},'lng'=>$fieldData->{'lng'}))).'" target="_blank">'.esc_html__('Location on the Google Maps.','chauffeur-booking-system').'</a>';
						}
					}
					
					$html.=
					'
						<div>
							<span class="to-legend-field">'.$fieldLabel.'</span>
							<div class="to-field-disabled">'.$fieldValue.'</div>								
						</div>	
					';
				}
				elseif($type==2)
				{
					$html.=
					'
						<tr>
							<td '.$argument['style']['cell'][1].'>'.$fieldLabel.'</td>
							<td '.$argument['style']['cell'][2].'>'.$fieldValue.'</td>
						</tr>
					';		
				}
				elseif($type==3)
				{
					$html.=
					'
						<div>
							<div>'.$fieldLabel.'</div>
							<div>'.$fieldValue.'</div>								
						</div>	
					';
				}
				elseif($type==4)
				{
					$html.='<b>'.$fieldLabel.':</b> '.$fieldValue.$newLineChar;
				}
			}
		}
		
		return($html);
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/