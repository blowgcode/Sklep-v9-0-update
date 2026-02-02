<?php

/******************************************************************************/
/******************************************************************************/

namespace Psr\SimpleCache;

if(!interface_exists('Psr\\SimpleCache\\CacheInterface'))
{
	interface CacheInterface
	{
		public function get($key,$default=null);
		public function set($key,$value,$ttl=null);
		public function delete($key);
		public function clear();
		public function getMultiple($keys,$default=null);
		public function setMultiple($values,$ttl=null);
		public function deleteMultiple($keys);
		public function has($key);
	}
}

if(!interface_exists('Psr\\SimpleCache\\InvalidArgumentException'))
{
	interface InvalidArgumentException extends \Throwable
	{
	}
}

namespace Psr\Cache;

if(!interface_exists('Psr\\Cache\\CacheItemInterface'))
{
	interface CacheItemInterface
	{
		public function getKey();
		public function get();
		public function isHit();
		public function set($value);
		public function expiresAt($expiration);
		public function expiresAfter($time);
	}
}

if(!interface_exists('Psr\\Cache\\CacheItemPoolInterface'))
{
	interface CacheItemPoolInterface
	{
		public function getItem($key);
		public function getItems(array $keys=array());
		public function hasItem($key);
		public function clear();
		public function deleteItem($key);
		public function deleteItems(array $keys);
		public function save(CacheItemInterface $item);
		public function saveDeferred(CacheItemInterface $item);
		public function commit();
	}
}

/******************************************************************************/
/******************************************************************************/
