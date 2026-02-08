<?php

/******************************************************************************/
/******************************************************************************/

class CHBSPrice
{
	/**************************************************************************/
	
	static function format($value,$currencyIndex,$useSymbol=true,$useCode=true)
	{
		$Currency=new CHBSCurrency();
		
		$currency=$Currency->getCurrency($currencyIndex);

		$value=number_format(floatval($value),$currency['decimal_digit_number'],$currency['decimal_separator'],$currency['thousand_separator']);

		if($useSymbol)
		{
			switch($currency['symbol_position'])
			{
				case 1:
					$value=$currency['symbol'].$value;
				break;
				case 2:
					$value=$currency['symbol'].' '.$value;
				break;
				case 3:
					$value=$value.$currency['symbol'];
				break;
				case 4:
					$value=$value.' '.$currency['symbol'];
				break;
			}
		}
		elseif($useCode) $value.=$currencyIndex;
		
		return($value);
	}
	
	/**************************************************************************/
	
	static function numberFormat($value,$currencyId=null)
	{
		if(is_null($currencyId))
			$currencyId=CHBSGlobalData::getGlobalData('currency_id');
		
		if(!is_null($currencyId))
		{
			$Currency=new CHBSCurrency();
			$currency=$Currency->getCurrency($currencyId);
			
			$value=preg_replace('/,/','.',$value);
			
			return(number_format(floatval($value),$currency['decimal_digit_number'],'.',''));
		}
		
		return($value);
	}
	
	/**************************************************************************/
	
	static function numberFormatDictionary(&$dictionary)
	{
		$PriceRule=new CHBSPriceRule();
		
		$priceUseType=$PriceRule->getPriceUseType();
		
		foreach($priceUseType as $priceUseTypeIndex=>$priceUseTypeValue)
		{
			foreach($dictionary as $dictionaryIndex=>$dictionaryValue)
			{
				$key='price_'.$priceUseTypeIndex.'_value';
				
				if(array_key_exists($key,$dictionaryValue['meta']))
					$dictionary[$dictionaryIndex]['meta'][$key]=self::numberFormat($dictionaryValue['meta'][$key]);
			}
		}
	}
	
	/**************************************************************************/
	
	static function formatToSave($value,$empty=false)
	{
		$Validation=new CHBSValidation();
		
		if(($Validation->isEmpty($value)) && ($empty)) return('');
		
		$value=preg_replace('/,/','.',$value);
		return($value);
	}
	
	/**************************************************************************/
	
	static function isPrice($value,$empty=false)
	{
		$Validation=new CHBSValidation();
		return($Validation->isPrice($value,$empty));
	}
	
	/**************************************************************************/
	
	static function calculateGross($value,$taxRateId=0,$taxValue=0)
	{
		if($taxRateId!=0)
		{
			$TaxRate=new CHBSTaxRate();
			$dictionary=$TaxRate->getDictionary();
			$taxValue=$dictionary[$taxRateId]['meta']['tax_rate_value'];
		}
		
		$value*=(1+($taxValue/100));
		
		return($value);
	}
	
	/**************************************************************************/
	
	static function getDefaultPrice()
	{
		return(number_format(0.00,2,'.',''));
	}

	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/