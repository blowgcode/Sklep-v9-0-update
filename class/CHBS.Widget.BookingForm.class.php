<?php

/******************************************************************************/
/******************************************************************************/

class CHBSWidgetBookingForm extends CHBSWidget 
{
	/**************************************************************************/
	
	function __construct() 
	{
		$this->style=array
		(
			1=>array(__('Style 1','chauffeur-booking-form')),
			2=>array(__('Style 2','chauffeur-booking-form')),
			3=>array(__('Style 3','chauffeur-booking-form')),
 			4=>array(__('Style 4','chauffeur-booking-form'))
		);
		
		parent::__construct('chbs_widget_booking_form',__('Chauffeur Booking Form','chauffeur-booking-system'),array('description'=>__('Displays booking form.','chauffeur-booking-system')),array());
	}
	
	/**************************************************************************/
	
	function widget($argument,$instance) 
	{
		$argument['_data']['file']='widget_booking_form.php';
		parent::widget($argument,$instance);
	}
	
	/**************************************************************************/
	
	function update($new_instance,$old_instance)
	{
		$instance=array();
		$instance['css_class']=$new_instance['css_class'];
		$instance['widget_style']=(int)$new_instance['widget_style'];
        $instance['widget_second_step']=$new_instance['widget_second_step'];
		$instance['booking_form_id']=(int)$new_instance['booking_form_id'];
		$instance['booking_form_url']=$new_instance['booking_form_url'];
		$instance['booking_form_new_window']=(int)$new_instance['booking_form_new_window'];
		$instance['booking_form_currency']=$new_instance['booking_form_currency'];
		return($instance);
	}
	
	/**************************************************************************/
	
	function form($instance) 
	{	
		$instance['_data']['file']='widget_booking_form.php';
		$instance['_data']['field']=array('css_class','widget_style','widget_second_step','booking_form_id','booking_form_url','booking_form_new_window','booking_form_currency');
		parent::form($instance);
	}
	
	/**************************************************************************/
	
	function register($class=null)
	{
		parent::register(is_null($class) ? get_class() : $class);
	}
	
	/**************************************************************************/
	
	function getStyle()
	{
		return($this->style);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/