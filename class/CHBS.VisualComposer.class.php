<?php

/******************************************************************************/
/******************************************************************************/

class CHBSVisualComposer
{
	/**************************************************************************/
	
	function __construct()
	{		
        
	}
    
    /**************************************************************************/
    
    function init()
    {        
        add_action('vc_before_init',array($this,'beforeInitAction'));
    }
    
    /**************************************************************************/
    
    function beforeInitAction()
    {
        require_once(PLUGIN_CHBS_VC_PATH.'vc_'.PLUGIN_CHBS_CONTEXT.'_booking_form.php');
    }
    
    /**************************************************************************/
    
    function createParamDictionary($data)
    {        
        $dictionary=array();
        
        foreach($data as $index=>$value)
        {
            if(is_array($value))
            {
                if(array_key_exists('post',$value))
                    $dictionary[$index]=$value['post']->post_title;
                elseif(isset($value['name']))
                    $dictionary[$index]=$value['name'];
            }
        }
        
        return(array_combine(array_values($dictionary),array_keys($dictionary)));
    }
    
    /**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/