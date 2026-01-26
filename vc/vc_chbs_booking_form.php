<?php

/******************************************************************************/
/******************************************************************************/

$Currency=new CHBSCurrency();
$BookingForm=new CHBSBookingForm();
$VisualComposer=new CHBSVisualComposer();

$currency=array_merge(array(''=>array('name'=>__('[None]','chauffeur-booking-system'))),$Currency->getCurrency());

vc_map
( 
	array
	(
		'base'=>CHBSBookingForm::getShortcodeName(),
		'name'=>__('Chauffeur Booking Form','chauffeur-booking-system'),
		'description'=>__('Displays booking from.','chauffeur-booking-system'), 
		'category'=>__('Content','templatica'),  
		'params'=>array
		(   
			array
			(
				'type'=>'dropdown',
				'param_name'=>'booking_form_id',
				'heading'=>__('Booking form','chauffeur-booking-system'),
				'description'=>__('Select booking form which has to be displayed.','chauffeur-booking-system'),
				'value'=>$VisualComposer->createParamDictionary($BookingForm->getDictionary()),
				'admin_label'=>true
			),
			array
			(
				'type'=>'dropdown',
				'param_name'=>'currency',
				'heading'=>__('Currency','chauffeur-booking-system'),
				'description'=>__('Select currency of booking form.','chauffeur-booking-system'),
				'value'=>$VisualComposer->createParamDictionary($currency),
				'admin_label'=>true
			)  
		)
	)
);  