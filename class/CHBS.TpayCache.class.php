<?php

/******************************************************************************/
/******************************************************************************/

class CHBSTpayCache implements \Psr\SimpleCache\CacheInterface
{
	/**************************************************************************/
	
	private $prefix='chbs_tpay_';
	
	/**************************************************************************/
	
	public function get($key,$default=null)
	{
		$value=get_transient($this->prefix.$key);
		return($value===false ? $default : $value);
	}
	
	/**************************************************************************/
	
	public function set($key,$value,$ttl=null)
	{
		$expiration=0;
		
		if(!is_null($ttl))
		{
			if($ttl instanceof DateInterval)
			{
				$expiration=(int)$ttl->format('%s');
			}
			else $expiration=(int)$ttl;
		}
		
		return(set_transient($this->prefix.$key,$value,$expiration));
	}
	
	/**************************************************************************/
	
	public function delete($key)
	{
		return(delete_transient($this->prefix.$key));
	}
	
	/**************************************************************************/
	
	public function clear()
	{
		return(true);
	}
	
	/**************************************************************************/
	
	public function getMultiple($keys,$default=null)
	{
		$data=array();
		
		foreach($keys as $key)
		{
			$data[$key]=$this->get($key,$default);
		}
		
		return($data);
	}
	
	/**************************************************************************/
	
	public function setMultiple($values,$ttl=null)
	{
		foreach($values as $key=>$value)
		{
			$this->set($key,$value,$ttl);
		}
		
		return(true);
	}
	
	/**************************************************************************/
	
	public function deleteMultiple($keys)
	{
		foreach($keys as $key)
		{
			$this->delete($key);
		}
		
		return(true);
	}
	
	/**************************************************************************/
	
	public function has($key)
	{
		return(get_transient($this->prefix.$key)!==false);
	}
	
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/
