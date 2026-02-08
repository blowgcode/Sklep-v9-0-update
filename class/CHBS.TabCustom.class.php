<?php

/******************************************************************************/
/******************************************************************************/

class CHBSTabCustom
{
	/**************************************************************************/
	
	function __construct()
	{
		
	}
	
	/**************************************************************************/
	
	function createTabCustom($attr,$content)
	{
        $default=array
        (
            'booking_form_id'=>'0'
        );		
		
		$attribute=shortcode_atts($default,$attr);
		
		$class=array('chbs-main','chbs-booking-form-id-'.$attribute['booking_form_id']);
		
		$html='<div'.CHBSHelper::createCSSClassAttribute($class).'><div class="chbs-tab-custom">'.do_shortcode($content).'</div></div>';
		
		return($html);
	}

	/**************************************************************************/
	
	function createTabCustomItem($attribute,$content)
	{
		$id=CHBSHelper::createId('custom_tab');
		
        $html= 
        '
            <a href="#'.esc_attr($id).'">'.esc_html($attribute['header']).'</a>
            <div id="'.esc_attr($id).'">'.do_shortcode($content).'</div>
        ';	
		
		return($html);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/