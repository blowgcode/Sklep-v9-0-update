<?php

/******************************************************************************/
/******************************************************************************/

class CHBSCurrency
{
	/**************************************************************************/
	
	private $currency;
	
	/**************************************************************************/
	
	function __construct()
	{
		$this->currency=CHBSGlobalData::getGlobalData('currency');
		
		$this->symbolPosition=array
		(
			1=>array(__('Left','chauffeur-booking-system')),
			2=>array(__('Left with space','chauffeur-booking-system')),
			3=>array(__('Right','chauffeur-booking-system')),
			4=>array(__('Right with space','chauffeur-booking-system'))
		);
	}
	
	/**************************************************************************/
	
	function isSymbolPosition($symbolPosition)
	{
		return(array_key_exists($symbolPosition,$this->symbolPosition) ? true : false);
	}
	
	/**************************************************************************/
	
	function getSymbolPosition()
	{
		return($this->symbolPosition);
	}
	
	/**************************************************************************/
	
	function getSymbolPositionName($symbolPosition)
	{
		return($this->symbolPosition[$symbolPosition][0]);
	}
	
	/**************************************************************************/
	
	function init()
	{
		$this->registerCPT();
		$this->currency=CHBSGlobalData::setGlobalData('currency',array($this,'load'),true);
	}
	
	/**************************************************************************/
	
	function load()
	{
		$currency=array
		(
			'AFN'=>array
			(
				'name'=>__('Afghan afghani','chauffeur-booking-system'),
				'symbol'=>'AFN'
			),
			'ALL'=>array
			(
				'name'=>__('Albanian lek','chauffeur-booking-system'),
				'symbol'=>'ALL'
			),
			'DZD'=>array
			(
				'name'=>__('Algerian dinar','chauffeur-booking-system'),
				'symbol'=>'DZD'
			),
			'AOA'=>array
			(
				'name'=>__('Angolan kwanza','chauffeur-booking-system'),
				'symbol'=>'AOA'
			),
			'ARS'=>array
			(
				'name'=>__('Argentine peso','chauffeur-booking-system'),
				'symbol'=>'ARS'
			),
			'AMD'=>array
			(
				'name'=>__('Armenian dram','chauffeur-booking-system'),
				'symbol'=>'AMD'
			),
			'AWG'=>array
			(
				'name'=>__('Aruban florin','chauffeur-booking-system'),
				'symbol'=>'AWG'
			),
			'AUD'=>array
			(
				'name'=>__('Australian dollar','chauffeur-booking-system'),
				'symbol'=>'&#36;'
			),
			'AZN'=>array
			(
				'name'=>__('Azerbaijani manat','chauffeur-booking-system'),
				'symbol'=>'AZN'
			),
			'BSD'=>array
			(
				'name'=>__('Bahamian dollar','chauffeur-booking-system'),
				'symbol'=>'BSD'
			),
			'BHD'=>array
			(
				'name'=>__('Bahraini dinar','chauffeur-booking-system'),
				'symbol'=>'BHD'
			),
			'BDT'=>array
			(
				'name'=>__('Bangladeshi taka','chauffeur-booking-system'),
				'symbol'=>'BDT'
			),
			'BBD'=>array
			(
				'name'=>__('Barbadian dollar','chauffeur-booking-system'),
				'symbol'=>'BBD'
			),
			'BYR'=>array
			(
				'name'=>__('Belarusian ruble','chauffeur-booking-system'),
				'symbol'=>'BYR'
			),
			'BZD'=>array
			(
				'name'=>__('Belize dollar','chauffeur-booking-system'),
				'symbol'=>'BZD'
			),
			'BTN'=>array
			(
				'name'=>__('Bhutanese ngultrum','chauffeur-booking-system'),
				'symbol'=>'BTN'
			),
			'BOB'=>array
			(
				'name'=>__('Bolivian boliviano','chauffeur-booking-system'),
				'symbol'=>'BOB'
			),
			'BAM'=>array
			(
				'name'=>__('Bosnia and Herzegovina konvertibilna marka','chauffeur-booking-system'),
				'symbol'=>'BAM'
			),
			'BWP'=>array
			(
				'name'=>__('Botswana pula','chauffeur-booking-system'),
				'symbol'=>'BWP'
			),
			'BRL'=>array
			(
				'name'=>__('Brazilian real','chauffeur-booking-system'),
				'symbol'=>'&#82;&#36;'
			),
			'GBP'=>array
			(
				'name'=>__('British pound','chauffeur-booking-system'),
				'symbol'=>'&pound;',
			),
			'BND'=>array
			(
				'name'=>__('Brunei dollar','chauffeur-booking-system'),
				'symbol'=>'BND'
			),
			'BGN'=>array
			(
				'name'=>__('Bulgarian lev','chauffeur-booking-system'),
				'symbol'=>'BGN'
			),
			'BIF'=>array
			(
				'name'=>__('Burundi franc','chauffeur-booking-system'),
				'symbol'=>'BIF'
			),
			'KYD'=>array
			(
				'name'=>__('Cayman Islands dollar','chauffeur-booking-system'),
				'symbol'=>'KYD'
			),
			'KHR'=>array
			(
				'name'=>__('Cambodian riel','chauffeur-booking-system'),
				'symbol'=>'KHR'
			),
			'CAD'=>array
			(
				'name'=>__('Canadian dollar','chauffeur-booking-system'),
				'symbol'=>'CAD'
			),
			'CVE'=>array
			(
				'name'=>__('Cape Verdean escudo','chauffeur-booking-system'),
				'symbol'=>'CVE'
			),
			'XAF'=>array
			(
				'name'=>__('Central African CFA franc','chauffeur-booking-system'),
				'symbol'=>'XAF'
			),
			'GQE'=>array
			(
				'name'=>__('Central African CFA franc','chauffeur-booking-system'),
				'symbol'=>'GQE'
			),
			'XPF'=>array
			(
				'name'=>__('CFP franc','chauffeur-booking-system'),
				'symbol'=>'XPF'
			),
			'CLP'=>array
			(
				'name'=>__('Chilean peso','chauffeur-booking-system'),
				'symbol'=>'CLP'
			),
			'CNY'=>array
			(
				'name'=>__('Chinese renminbi','chauffeur-booking-system'),
				'symbol'=>'&yen;'
			),
			'COP'=>array
			(
				'name'=>__('Colombian peso','chauffeur-booking-system'),
				'symbol'=>'COP'
			),
			'KMF'=>array
			(
				'name'=>__('Comorian franc','chauffeur-booking-system'),
				'symbol'=>'KMF'
			),
			'CDF'=>array
			(
				'name'=>__('Congolese franc','chauffeur-booking-system'),
				'symbol'=>'CDF'
			),
			'CRC'=>array
			(
				'name'=>__('Costa Rican colon','chauffeur-booking-system'),
				'symbol'=>'CRC'
			),
			'HRK'=>array
			(
				'name'=>__('Croatian kuna','chauffeur-booking-system'),
				'symbol'=>'HRK'
			),
			'CUC'=>array
			(
				'name'=>__('Cuban peso','chauffeur-booking-system'),
				'symbol'=>'CUC'
			),
			'CZK'=>array
			(
				'name'=>__('Czech koruna','chauffeur-booking-system'),
				'symbol'=>'&#75;&#269;'
			),
			'DKK'=>array
			(
				'name'=>__('Danish krone','chauffeur-booking-system'),
				'symbol'=>'&#107;&#114;'
			),
			'DJF'=>array
			(
				'name'=>__('Djiboutian franc','chauffeur-booking-system'),
				'symbol'=>'DJF'
			),
			'DOP'=>array
			(
				'name'=>__('Dominican peso','chauffeur-booking-system'),
				'symbol'=>'DOP'
			),
			'XCD'=>array
			(
				'name'=>__('East Caribbean dollar','chauffeur-booking-system'),
				'symbol'=>'XCD'
			),
			'EGP'=>array
			(
				'name'=>__('Egyptian pound','chauffeur-booking-system'),
				'symbol'=>'EGP'
			),
			'ERN'=>array
			(
				'name'=>__('Eritrean nakfa','chauffeur-booking-system'),
				'symbol'=>'ERN'
			),
			'EEK'=>array
			(
				'name'=>__('Estonian kroon','chauffeur-booking-system'),
				'symbol'=>'EEK'
			),
			'ETB'=>array
			(
				'name'=>__('Ethiopian birr','chauffeur-booking-system'),
				'symbol'=>'ETB'
			),
			'EUR'=>array
			(
				'name'=>__('European euro','chauffeur-booking-system'),
				'symbol'=>'&euro;'
			),
			'FKP'=>array
			(
				'name'=>__('Falkland Islands pound','chauffeur-booking-system'),
				'symbol'=>'FKP'
			),
			'FJD'=>array
			(
				'name'=>__('Fijian dollar','chauffeur-booking-system'),
				'symbol'=>'FJD'
			),
			'GMD'=>array
			(
				'name'=>__('Gambian dalasi','chauffeur-booking-system'),
				'symbol'=>'GMD'
			),
			'GEL'=>array
			(
				'name'=>__('Georgian lari','chauffeur-booking-system'),
				'symbol'=>'GEL'
			),
			'GHS'=>array
			(
				'name'=>__('Ghanaian cedi','chauffeur-booking-system'),
				'symbol'=>'GHS'
			),
			'GIP'=>array
			(
				'name'=>__('Gibraltar pound','chauffeur-booking-system'),
				'symbol'=>'GIP'
			),
			'GTQ'=>array
			(
				'name'=>__('Guatemalan quetzal','chauffeur-booking-system'),
				'symbol'=>'GTQ',
				'decimal_separator'=>'.'
			),
			'GNF'=>array
			(
				'name'=>__('Guinean franc','chauffeur-booking-system'),
				'symbol'=>'GNF'
			),
			'GYD'=>array
			(
				'name'=>__('Guyanese dollar','chauffeur-booking-system'),
				'symbol'=>'GYD'
			),
			'HTG'=>array
			(
				'name'=>__('Haitian gourde','chauffeur-booking-system'),
				'symbol'=>'HTG'
			),
			'HNL'=>array
			(
				'name'=>__('Honduran lempira','chauffeur-booking-system'),
				'symbol'=>'HNL'
			),
			'HKD'=>array
			(
				'name'=>__('Hong Kong dollar','chauffeur-booking-system'),
				'symbol'=>'&#36;'
			),
			'HUF'=>array
			(
				'name'=>__('Hungarian forint','chauffeur-booking-system'),
				'symbol'=>'&#70;&#116;'
			),
			'ISK'=>array
			(
				'name'=>__('Icelandic krona','chauffeur-booking-system'),
				'symbol'=>'ISK'
			),
			'INR'=>array
			(
				'name'=>__('Indian rupee','chauffeur-booking-system'),
				'symbol'=>'&#8377;'
			),
			'IDR'=>array
			(
				'name'=>__('Indonesian rupiah','chauffeur-booking-system'),
				'symbol'=>'Rp',
			),
			'IRR'=>array
			(
				'name'=>__('Iranian rial','chauffeur-booking-system'),
				'symbol'=>'IRR',
				'decimal_separator'=>'&#1643;'
			),
			'IQD'=>array
			(
				'name'=>__('Iraqi dinar','chauffeur-booking-system'),
				'symbol'=>'IQD',
				'decimal_separator'=>'&#1643;'
			),
			'ILS'=>array
			(
				'name'=>__('Israeli new sheqel','chauffeur-booking-system'),
				'symbol'=>'&#8362;'
			),
			'YER'=>array
			(
				'name'=>__('Yemeni rial','chauffeur-booking-system'),
				'symbol'=>'YER'
			),
			'JMD'=>array
			(
				'name'=>__('Jamaican dollar','chauffeur-booking-system'),
				'symbol'=>'JMD'
			),
			'JPY'=>array
			(
				'name'=>__('Japanese yen','chauffeur-booking-system'),
				'symbol'=>'&yen;'
			),
			'JOD'=>array
			(
				'name'=>__('Jordanian dinar','chauffeur-booking-system'),
				'symbol'=>'JOD'
			),
			'KZT'=>array
			(
				'name'=>__('Kazakhstani tenge','chauffeur-booking-system'),
				'symbol'=>'KZT'
			),
			'KES'=>array
			(
				'name'=>__('Kenyan shilling','chauffeur-booking-system'),
				'symbol'=>'KES'
			),
			'KGS'=>array
			(
				'name'=>__('Kyrgyzstani som','chauffeur-booking-system'),
				'symbol'=>'KGS'
			),
			'KWD'=>array
			(
				'name'=>__('Kuwaiti dinar','chauffeur-booking-system'),
				'symbol'=>'KWD',
				'decimal_separator'=>'&#1643;'
			),
			'LAK'=>array
			(
				'name'=>__('Lao kip','chauffeur-booking-system'),
				'symbol'=>'LAK'
			),
			'LVL'=>array
			(
				'name'=>__('Latvian lats','chauffeur-booking-system'),
				'symbol'=>'LVL'
			),
			'LBP'=>array
			(
				'name'=>__('Lebanese lira','chauffeur-booking-system'),
				'symbol'=>'LBP'
			),
			'LSL'=>array
			(
				'name'=>__('Lesotho loti','chauffeur-booking-system'),
				'symbol'=>'LSL'
			),
			'LRD'=>array
			(
				'name'=>__('Liberian dollar','chauffeur-booking-system'),
				'symbol'=>'LRD'
			),
			'LYD'=>array
			(
				'name'=>__('Libyan dinar','chauffeur-booking-system'),
				'symbol'=>'LYD'
			),
			'LTL'=>array
			(
				'name'=>__('Lithuanian litas','chauffeur-booking-system'),
				'symbol'=>'LTL'
			),
			'MOP'=>array
			(
				'name'=>__('Macanese pataca','chauffeur-booking-system'),
				'symbol'=>'MOP'
			),
			'MKD'=>array
			(
				'name'=>__('Macedonian denar','chauffeur-booking-system'),
				'symbol'=>'MKD'
			),
			'MGA'=>array
			(
				'name'=>__('Malagasy ariary','chauffeur-booking-system'),
				'symbol'=>'MGA'
			),
			'MYR'=>array
			(
				'name'=>__('Malaysian ringgit','chauffeur-booking-system'),
				'symbol'=>'&#82;&#77;'
			),
			'MWK'=>array
			(
				'name'=>__('Malawian kwacha','chauffeur-booking-system'),
				'symbol'=>'MWK'
			),
			'MVR'=>array
			(
				'name'=>__('Maldivian rufiyaa','chauffeur-booking-system'),
				'symbol'=>'MVR'
			),
			'MRO'=>array
			(
				'name'=>__('Mauritanian ouguiya','chauffeur-booking-system'),
				'symbol'=>'MRO'
			),
			'MUR'=>array
			(
				'name'=>__('Mauritian rupee','chauffeur-booking-system'),
				'symbol'=>'MUR'
			),
			'MXN'=>array
			(
				'name'=>__('Mexican peso','chauffeur-booking-system'),
				'symbol'=>'&#36;'
			),
			'MMK'=>array
			(
				'name'=>__('Myanma kyat','chauffeur-booking-system'),
				'symbol'=>'MMK'
			),
			'MDL'=>array
			(
				'name'=>__('Moldovan leu','chauffeur-booking-system'),
				'symbol'=>'MDL'
			),
			'MNT'=>array
			(
				'name'=>__('Mongolian tugrik','chauffeur-booking-system'),
				'symbol'=>'MNT'
			),
			'MAD'=>array
			(
				'name'=>__('Moroccan dirham','chauffeur-booking-system'),
				'symbol'=>'MAD'
			),
			'MZM'=>array
			(
				'name'=>__('Mozambican metical','chauffeur-booking-system'),
				'symbol'=>'MZM'
			),
			'NAD'=>array
			(
				'name'=>__('Namibian dollar','chauffeur-booking-system'),
				'symbol'=>'NAD'
			),
			'NPR'=>array
			(
				'name'=>__('Nepalese rupee','chauffeur-booking-system'),
				'symbol'=>'NPR'
			),
			'ANG'=>array
			(
				'name'=>__('Netherlands Antillean gulden','chauffeur-booking-system'),
				'symbol'=>'ANG'
			),
			'TWD'=>array
			(
				'name'=>__('New Taiwan dollar','chauffeur-booking-system'),
				'symbol'=>'&#78;&#84;&#36;'
			),
			'NZD'=>array
			(
				'name'=>__('New Zealand dollar','chauffeur-booking-system'),
				'symbol'=>'&#36;'
			),
			'NIO'=>array
			(
				'name'=>__('Nicaraguan cordoba','chauffeur-booking-system'),
				'symbol'=>'NIO'
			),
			'NGN'=>array
			(
				'name'=>__('Nigerian naira','chauffeur-booking-system'),
				'symbol'=>'NGN'
			),
			'KPW'=>array
			(
				'name'=>__('North Korean won','chauffeur-booking-system'),
				'symbol'=>'KPW'
			),
			'NOK'=>array
			(
				'name'=>__('Norwegian krone','chauffeur-booking-system'),
				'symbol'=>'&#107;&#114;'
			),
			'OMR'=>array
			(
				'name'=>__('Omani rial','chauffeur-booking-system'),
				'symbol'=>'OMR',
				'decimal_separator'=>'&#1643;'
			),
			'TOP'=>array
			(
				'name'=>__('Paanga','chauffeur-booking-system'),
				'symbol'=>'TOP'
			),
			'PKR'=>array
			(
				'name'=>__('Pakistani rupee','chauffeur-booking-system'),
				'symbol'=>'PKR'
			),
			'PAB'=>array
			(
				'name'=>__('Panamanian balboa','chauffeur-booking-system'),
				'symbol'=>'PAB'
			),
			'PGK'=>array
			(
				'name'=>__('Papua New Guinean kina','chauffeur-booking-system'),
				'symbol'=>'PGK'
			),
			'PYG'=>array
			(
				'name'=>__('Paraguayan guarani','chauffeur-booking-system'),
				'symbol'=>'PYG'
			),
			'PEN'=>array
			(
				'name'=>__('Peruvian nuevo sol','chauffeur-booking-system'),
				'symbol'=>'PEN'
			),
			'PHP'=>array
			(
				'name'=>__('Philippine peso','chauffeur-booking-system'),
				'symbol'=>'&#8369;'
			),
			'PLN'=>array
			(
				'name'=>__('Polish zloty','chauffeur-booking-system'),
				'symbol'=>'zł',
				'symbol_position'=>2,
			),
			'QAR'=>array
			(
				'name'=>__('Qatari riyal','chauffeur-booking-system'),
				'symbol'=>'QAR',
				'decimal_separator'=>'&#1643;'
			),
			'RON'=>array
			(
				'name'=>__('Romanian leu','chauffeur-booking-system'),
				'symbol'=>'lei'
			),
			'RUB'=>array
			(
				'name'=>__('Russian ruble','chauffeur-booking-system'),
				'symbol'=>'RUB'
			),
			'RWF'=>array
			(
				'name'=>__('Rwandan franc','chauffeur-booking-system'),
				'symbol'=>'RWF'
			),
			'SHP'=>array
			(
				'name'=>__('Saint Helena pound','chauffeur-booking-system'),
				'symbol'=>'SHP'
			),
			'WST'=>array
			(
				'name'=>__('Samoan tala','chauffeur-booking-system'),
				'symbol'=>'WST'
			),
			'STD'=>array
			(
				'name'=>__('Sao Tome and Principe dobra','chauffeur-booking-system'),
				'symbol'=>'STD'
			),
			'SAR'=>array
			(
				'name'=>__('Saudi riyal','chauffeur-booking-system'),
				'symbol'=>'SAR'
			),
			'SCR'=>array
			(
				'name'=>__('Seychellois rupee','chauffeur-booking-system'),
				'symbol'=>'SCR'
			),
			'RSD'=>array
			(
				'name'=>__('Serbian dinar','chauffeur-booking-system'),
				'symbol'=>'RSD'
			),
			'SLL'=>array
			(
				'name'=>__('Sierra Leonean leone','chauffeur-booking-system'),
				'symbol'=>'SLL'
			),
			'SGD'=>array
			(
				'name'=>__('Singapore dollar','chauffeur-booking-system'),
				'symbol'=>'&#36;',
				'decimal_separator'=>'.'
			),
			'SYP'=>array
			(
				'name'=>__('Syrian pound','chauffeur-booking-system'),
				'symbol'=>'SYP',
				'decimal_separator'=>'&#1643;'
			),
			'SKK'=>array
			(
				'name'=>__('Slovak koruna','chauffeur-booking-system'),
				'symbol'=>'SKK'
			),
			'SBD'=>array
			(
				'name'=>__('Solomon Islands dollar','chauffeur-booking-system'),
				'symbol'=>'SBD'
			),
			'SOS'=>array
			(
				'name'=>__('Somali shilling','chauffeur-booking-system'),
				'symbol'=>'SOS'
			),
			'ZAR'=>array
			(
				'name'=>__('South African rand','chauffeur-booking-system'),
				'symbol'=>'&#82;'
			),
			'KRW'=>array
			(
				'name'=>__('South Korean won','chauffeur-booking-system'),
				'symbol'=>'&#8361;'
			),
			'XDR'=>array
			(
				'name'=>__('Special Drawing Rights','chauffeur-booking-system'),
				'symbol'=>'XDR'
			),
			'LKR'=>array
			(
				'name'=>__('Sri Lankan rupee','chauffeur-booking-system'),
				'symbol'=>'LKR'
			),
			'SDG'=>array
			(
				'name'=>__('Sudanese pound','chauffeur-booking-system'),
				'symbol'=>'SDG'
			),
			'SRD'=>array
			(
				'name'=>__('Surinamese dollar','chauffeur-booking-system'),
				'symbol'=>'SRD'
			),
			'SZL'=>array
			(
				'name'=>__('Swazi lilangeni','chauffeur-booking-system'),
				'symbol'=>'SZL'
			),
			'SEK'=>array
			(
				'name'=>__('Swedish krona','chauffeur-booking-system'),
				'symbol'=>'&#107;&#114;'
			),
			'CHF'=>array
			(
				'name'=>__('Swiss franc','chauffeur-booking-system'),
				'symbol'=>'&#67;&#72;&#70;'
			),
			'TJS'=>array
			(
				'name'=>__('Tajikistani somoni','chauffeur-booking-system'),
				'symbol'=>'TJS'
			),
			'TZS'=>array
			(
				'name'=>__('Tanzanian shilling','chauffeur-booking-system'),
				'symbol'=>'TZS'
			),
			'THB'=>array
			(
				'name'=>__('Thai baht','chauffeur-booking-system'),
				'symbol'=>'&#3647;'
			),
			'TTD'=>array
			(
				'name'=>__('Trinidad and Tobago dollar','chauffeur-booking-system'),
				'symbol'=>'TTD'
			),
			'TND'=>array
			(
				'name'=>__('Tunisian dinar','chauffeur-booking-system'),
				'symbol'=>'TND'
			),
			'TRY'=>array
			(
				'name'=>__('Turkish new lira','chauffeur-booking-system'),
				'symbol'=>'&#84;&#76;'
			),
			'TMM'=>array
			(
				'name'=>__('Turkmen manat','chauffeur-booking-system'),
				'symbol'=>'TMM'
			),
			'AED'=>array
			(
				'name'=>__('UAE dirham','chauffeur-booking-system'),
				'symbol'=>'AED'
			),
			'UGX'=>array
			(
				'name'=>__('Ugandan shilling','chauffeur-booking-system'),
				'symbol'=>'UGX'
			),
			'UAH'=>array
			(
				'name'=>__('Ukrainian hryvnia','chauffeur-booking-system'),
				'symbol'=>'UAH'
			),
			'USD'=>array
			(
				'name'=>__('United States dollar','chauffeur-booking-system'),
				'symbol'=>'&#36;',
				'decimal_separator'=>'.',
				'thousand_separator'=>','
			),
			'UYU'=>array
			(
				'name'=>__('Uruguayan peso','chauffeur-booking-system'),
				'symbol'=>'UYU'
			),
			'UZS'=>array
			(
				'name'=>__('Uzbekistani som','chauffeur-booking-system'),
				'symbol'=>'UZS'
			),
			'VUV'=>array
			(
				'name'=>__('Vanuatu vatu','chauffeur-booking-system'),
				'symbol'=>'VUV'
			),
			'VEF'=>array
			(
				'name'=>__('Venezuelan bolivar','chauffeur-booking-system'),
				'symbol'=>'VEF'
			),
			'VND'=>array
			(
				'name'=>__('Vietnamese dong','chauffeur-booking-system'),
				'symbol'=>'VND'
			),
			'XOF'=>array
			(
				'name'=>__('West African CFA franc','chauffeur-booking-system'),
				'symbol'=>'XOF'
			),
			'ZMK'=>array
			(
				'name'=>__('Zambian kwacha','chauffeur-booking-system'),
				'symbol'=>'ZMK'
			),
			'ZWD'=>array
			(
				'name'=>__('Zimbabwean dollar','chauffeur-booking-system'),
				'symbol'=>'ZWD'
			),
			'RMB'=>array
			(
				'name'=>__('Chinese Yuan','chauffeur-booking-system'),
				'symbol'=>'&yen;'
			)
		);
		
		$currency=$this->useDefault($currency);
		
		$this->currency=$currency;
		
		$dictionary=$this->getDictionary();
		
		foreach($dictionary as $index=>$value)
		{
			if(!array_key_exists('currency_code',$value['meta'])) continue;
			
			$code=$value['meta']['currency_code'];
			
			if(!$this->isCurrency($code)) continue;
			
			$currency[$code]['symbol']=$value['meta']['symbol'];
			$currency[$code]['symbol_position']=$value['meta']['symbol_position'];
			
			$currency[$code]['decimal_separator']=$value['meta']['decimal_separator'];
			$currency[$code]['decimal_digit_number']=$value['meta']['decimal_digit_number'];
			
			$currency[$code]['thousand_separator']=$value['meta']['thousand_separator'];
		}

		return($currency);
	}
	
	/**************************************************************************/
	
	function useDefault($currency)
	{		
		foreach($currency as $index=>$value)
		{
			if(!array_key_exists('decimal_separator',$value))
				$currency[$index]['decimal_separator']='.';
			if(!array_key_exists('thousand_separator',$value))
				$currency[$index]['thousand_separator']='';
			if(!array_key_exists('symbol_position',$value))
				$currency[$index]['symbol_position']=1;	  
			if(!array_key_exists('decimal_digit_number',$value))
				$currency[$index]['decimal_digit_number']=2;	
		}
		
		return($currency);
	}
	
	/**************************************************************************/
	
	function getCurrency($currency=null)
	{
		if(is_null($currency))
			return($this->currency);
		else return($this->currency[$currency]);
	}
	
	/**************************************************************************/
	
	function isCurrency($currencyCode)
	{
		$currency=$this->getCurrency();
		return((is_array($currency)) && (array_key_exists($currencyCode,$currency)));
	}

	/**************************************************************************/
	
	static function getBaseCurrency()
	{
		return(CHBSOption::getOption('currency'));
	}
	
	/**************************************************************************/
	
	static function getFormCurrency()
	{
		if(array_key_exists('currency',$_GET))
			$currency=CHBSHelper::getGetValue('currency',false);
		else $currency=CHBSHelper::getPostValue('currency');
		
		return($currency);
	}
	
	/**************************************************************************/
	
	static function getExchangeRate()
	{
		$rate=1;
		
		if(CHBSCurrency::getBaseCurrency()!=CHBSCurrency::getFormCurrency())
		{
			$rate=0;
			$dictionary=CHBSOption::getOption('currency_exchange_rate');
			
			if(array_key_exists(CHBSCurrency::getFormCurrency(),$dictionary))
				$rate=$dictionary[CHBSCurrency::getFormCurrency()];
		}
		
		return($rate);
	}
	
	/**************************************************************************/
	
	function getDictionary($attr=array())
	{
		global $post;
		
		$dictionary=array();
		
		$default=array
		(
			'currency_id'=>0
		);
		
		$attribute=shortcode_atts($default,$attr);
		CHBSHelper::preservePost($post,$bPost);
		
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'publish',
			'posts_per_page'=>-1,
			'orderby'=>array('title'=>'asc')
		);
		
		if($attribute['currency_id'])
			$argument['p']=$attribute['currency_id'];

		$query=new WP_Query($argument);
		if($query===false) return($dictionary);
		
		while($query->have_posts())
		{
			$query->the_post();
			$dictionary[$post->ID]['post']=$post;
			$dictionary[$post->ID]['meta']=CHBSPostMeta::getPostMeta($post);
		}
		
		CHBSHelper::preservePost($post,$bPost,0);	
		
		return($dictionary);		
	}
	
	/**************************************************************************/
	
	function isSaved($currencyCode,$postId)
	{
		$argument=array
		(
			'post_type'=>self::getCPTName(),
			'post_status'=>'any',
			'post__not_in'=>array($postId),
			'posts_per_page'=>-1,
			'meta_key'=>PLUGIN_CHBS_CONTEXT.'_currency_code',
			'meta_value'=>$currencyCode,
			'meta_compare'=>'='
		);		
		
		$query=new WP_Query($argument);
		if($query===false) return(false);
		
		/***/

		if($query->found_posts!=1) return(false);		
		
		return(true);
	}
	
	/**************************************************************************/

	public static function getCPTName()
	{
		return(PLUGIN_CHBS_CONTEXT.'_currency');
	}
	
	/**************************************************************************/
	
	function registerCPT()
	{
		register_post_type
		(
			self::getCPTName(),
			array
			(
				'labels'=>array
				(
					'name'=>__('Currencies','chauffeur-booking-system'),
					'singular_name'=>__('Currencies','chauffeur-booking-system'),
					'add_new'=>__('Add New','chauffeur-booking-system'),
					'add_new_item'=>__('Add New Currency','chauffeur-booking-system'),
					'edit_item'=>__('Edit Currency','chauffeur-booking-system'),
					'new_item'=>__('New Currency','chauffeur-booking-system'),
					'all_items'=>__('Currencies','chauffeur-booking-system'),
					'view_item'=>__('View Currency','chauffeur-booking-system'),
					'search_items'=>__('Search Currencies','chauffeur-booking-system'),
					'not_found'=>__('No Currencies Found','chauffeur-booking-system'),
					'not_found_in_trash'=>__('No Currencies in Trash','chauffeur-booking-system'), 
					'parent_item_colon'=>'',
					'menu_name'=>__('Currencies','chauffeur-booking-system')
				),	
				'public'=>false,  
				'show_ui'=>true, 
				'show_in_menu'=>'edit.php?post_type='.CHBSBooking::getCPTName(),
				'capability_type'=>'post',
				'menu_position'=>2,
				'hierarchical'=>false,  
				'rewrite'=>false,  
				'supports'=>false  
			)
		);
		
		add_action('save_post',array($this,'savePost'));
		add_action('add_meta_boxes_'.self::getCPTName(),array($this,'addMetaBox'));
		add_filter('postbox_classes_'.self::getCPTName().'_chbs_meta_box_currency',array($this,'adminCreateMetaBoxClass'));
		
		add_filter('manage_edit-'.self::getCPTName().'_columns',array($this,'manageEditColumns')); 
		add_action('manage_'.self::getCPTName().'_posts_custom_column',array($this,'managePostsCustomColumn'));
		add_filter('manage_edit-'.self::getCPTName().'_sortable_columns',array($this,'manageEditSortableColumns'));		
	}
	
	/**************************************************************************/
	
	function addMetaBox()
	{
		add_meta_box(PLUGIN_CHBS_CONTEXT.'_meta_box_currency',__('Main','chauffeur-booking-system'),array($this,'addMetaBoxMain'),self::getCPTName(),'normal','low');		
	}
	
	/**************************************************************************/
	
	function addMetaBoxMain()
	{
		global $post;
		
		$data=array();
		
		$data['meta']=CHBSPostMeta::getPostMeta($post);
		
		$data['nonce']=CHBSHelper::createNonceField(PLUGIN_CHBS_CONTEXT.'_meta_box_currency');
		
		$data['dictionary']['currency']=$this->currency;
		
		$data['dictionary']['symbol_position']=$this->getSymbolPosition();
		
		$Template=new CHBSTemplate($data,PLUGIN_CHBS_TEMPLATE_PATH.'admin/meta_box_currency.php');
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
		CHBSHelper::setDefault($meta,'symbol_position',1);
		CHBSHelper::setDefault($meta,'symbol','');
		
		CHBSHelper::setDefault($meta,'decimal_separator','.');
		CHBSHelper::setDefault($meta,'decimal_digit_number',2);
		CHBSHelper::setDefault($meta,'thousand_separator','');
	}
	
	/**************************************************************************/
	
	function savePost($postId)
	{	  
		if(!$_POST) return(false);
		
		if(CHBSHelper::checkSavePost($postId,PLUGIN_CHBS_CONTEXT.'_meta_box_currency_noncename','savePost')===false) return(false);
		
		$Validation=new CHBSValidation();
		
		/***/
		
		$option=CHBSHelper::getPostOption();
		
		$key=array
		(
			'currency_code',
			'symbol',
			'symbol_position',
			'decimal_separator',
			'decimal_digit_number',
			'thousand_separator',
		);
		
		/***/

		if(!$this->isCurrency($option['currency_code'])) return;
		if($this->isSaved($option['currency_code'],$postId)) return;

		if(!$this->isSymbolPosition($option['symbol_position'])) $option['symbol_position']=1;
		
		if(!$Validation->isNumber($option['decimal_digit_number'],0,9)) $option['decimal_digit_number']=2;

		/***/
		
		foreach($key as $index)
			CHBSPostMeta::updatePostMeta($postId,$index,$option[$index]);

		wp_update_post(array('ID'=>$postId,'post_title'=>$option['currency_code']));
	}
	
	/**************************************************************************/
	
	function manageEditColumns($column)
	{
		$column=array
		(
			'cb'=>$column['cb'],
			'title'=>__('Code','chauffeur-booking-system'),
			'currency'=>__('Currency','chauffeur-booking-system'),
			'symbol'=>__('Symbol character','chauffeur-booking-system'),
			'symbol_position'=>__('Symbol position','chauffeur-booking-system'),
			'decimal_separator'=>__('Decimal separator','chauffeur-booking-system'),
			'decimal_digit_number'=>__('Decimal digits','chauffeur-booking-system'),
			'thousand_separator'=>__('Thousands separator','chauffeur-booking-system'),
		);
   
		return($column);		  
	}
	
	/**************************************************************************/
	
	function managePostsCustomColumn($column)
	{
		global $post;
		
		$meta=CHBSPostMeta::getPostMeta($post);
		
		if(!array_key_exists('currency_code',$meta)) return;
		
		switch($column) 
		{
			case 'currency':
				
				echo esc_html($this->currency[$meta['currency_code']]['name'].' ('.$meta['currency_code'].')');
				
			break;
			
			case 'symbol':
				
				echo esc_html($meta['symbol']);
			
			break;		
			
			case 'symbol_position':
			
				echo esc_html($this->getSymbolPositionName($meta['symbol_position']));
				
			break;		
			
			case 'decimal_separator':
				
				echo esc_html($meta['decimal_separator']);
				
			break;		
			
			case 'decimal_digit_number':
				
				echo esc_html($meta['decimal_digit_number']);
				
			break;
			
			case 'thousand_separator':
				
				echo esc_html($meta['thousand_separator']);
				
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