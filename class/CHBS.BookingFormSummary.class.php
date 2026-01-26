<?php

/******************************************************************************/
/******************************************************************************/

class CHBSBookingFormSummary
{
	/**************************************************************************/
	
	function __construct()
	{
		$this->data=array();
	}
	
	/**************************************************************************/
	
	function add($data,$layout=1)
	{
		array_push($this->data,array('data'=>$data,'layout'=>$layout));
	}
	
	/**************************************************************************/
	
function createField($name, $value, $html = false)
{
    // Sprawdzenie, czy pole jest związane z czasem i czy zawiera przedział
    if (strpos(strtolower($name), 'time') !== false && strpos($value, ' - ') !== false) {
        // Zakładamy, że wartość już jest w formacie przedziału czasowego 'HH:MM - HH:MM'
        // Tutaj możesz dodać dodatkową logikę, jeśli format przedziału czasowego wymaga dostosowania
        $timeRange = $value;
    } else {
        // Dla wszystkich innych pól zachowaj oryginalne zachowanie
        $timeRange = $html ? $value : nl2br(esc_html($value));
    }

    // Generowanie HTML dla pola
    $html =
        '
        <div class="chbs-summary-field-name">' . ($html ? $name : nl2br(esc_html($name))) . '</div>
        <div class="chbs-summary-field-value">' . $timeRange . '</div>
    ';

    return $html;
}

	
	/**************************************************************************/
	
	function create($header,$step=-1)
	{
		$html=null;
		$Validation=new CHBSValidation();
		
		foreach($this->data as $data)
		{
			switch($data['layout'])
			{
				case 1:
					
					if($Validation->isEmpty($data['data'][1])) break;
					
					$html.=
					'
						<div class="chbs-summary-field">
							'.$this->createField($data['data'][0],$data['data'][1]).'
						</div>
					';

				break;
			
				case 2:
					
					if(($Validation->isEmpty($data['data'][0][1])) && ($Validation->isEmpty($data['data'][1][1]))) break;
					
					$html.=
					'
						<div class="chbs-summary-field">
							<div class="chbs-layout-50x50 chbs-clear-fix">
								<div class="chbs-layout-column-left">						
									'.$this->createField($data['data'][0][0],$data['data'][0][1]).'
								</div>
								<div class="chbs-layout-column-right">
									'.$this->createField($data['data'][1][0],$data['data'][1][1]).'
								</div>
							</div>
						</div>
					';
					
				break;
			
				case 3:
					
					$add=null;
					foreach($data['data'][1] as $value)
					{
						if($Validation->isNotEmpty($add)) $add.='<br>';
						$add.=$value;
					}
					
					if($Validation->isEmpty($add)) break;
					
					$html.=
					'
						<div class="chbs-summary-field">
							'.$this->createField(esc_html($data['data'][0]),$add,true).'
						</div>
					';   
					
				break;
			}
		}
		
		if($Validation->isEmpty($html)) return;
		
		$html=
		'
			<div class="chbs-summary">
				<div class="chbs-summary-header">
					<h4>'.esc_html($header).'</h4>
					'.($step==-1 ? null : '<a href="#" data-step="'.$step.'">'.esc_html__('Edit','chauffeur-booking-system').'</a>').'
				</div>				 
				'.$html.'
			</div>
		';
		
		$this->data=array();
		
		return($html);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/