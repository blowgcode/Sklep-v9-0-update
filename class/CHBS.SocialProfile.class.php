<?php

/******************************************************************************/
/******************************************************************************/

class CHBSSocialProfile
{
	/**************************************************************************/
	
	function __construct()
	{		
		$this->socialProfile=array
		(
			'app-store'=>array('App Store','app-store','',''),
			'behance'=>array('Behance','behance','',''),
			'bing'=>array('Bing','bing','',''),
			'buymeacoffee'=>array('Buy Me a Coffe','buymeacoffee','',''),
			'deviantart'=>array('Deviantart','deviantart','',''),
			'discord'=>array('Discord','discord','',''),
			'dribbble'=>array('Dribbble','dribbble','',''),
			'dropbox'=>array('Dropbox','dropbox','',''),
			'email'=>array('E-mail','email','',''),
			'envato'=>array('Envato','envato','',''),
			'facebook'=>array('Facebook','facebook','https://www.facebook.com/QuanticaLabs/','2'),
			'facebook-messenger'=>array('Facebook messanger','facebook-messenger','',''),
			'figma'=>array('Figma','figma','',''),
			'flickr'=>array('Flickr','flickr','',''),
			'foursquare'=>array('Foursquare','foursquare','',''),
			'github'=>array('Github','github','',''),
			'google'=>array('Google','google','',''),
			'google-play'=>array('Google Play','google-play','',''),
			'houzz'=>array('Houzz','houzz','',''),
			'instagram'=>array('Instagram','instagram','',''),
			'line'=>array('Line','line','',''),
			'linkedin'=>array('Linkedin','linkedin','',''),
			'location'=>array('Location','location','',''),
			'mastodon'=>array('Mastodon','mastodon','',''),
			'medium'=>array('Medium','medium','',''),
			'mobile'=>array('Mobile','mobile','',''),
			'microsoft-teams'=>array('Microsoft Teams','microsoft-teams','',''),
			'paypal'=>array('Paypal','paypal','',''),
			'pinterest'=>array('Pinterest','pinterest','http://pinterest.com/quanticalabs','3'),
			'reddit'=>array('Reddit','reddit','',''),
			'rss'=>array('RSS','rss','',''),
			'share'=>array('Share','share','',''),
			'skype'=>array('Skype','skype','',''),
			'slack'=>array('Slack','slack','',''),
			'snapchat'=>array('Snapchat','snapchat','',''),
			'soundcloud'=>array('Soundcloud','soundcloud','',''),
			'spotify'=>array('Spotify','spotify','',''),
			'stripe'=>array('Stripe','stripe','',''),
			'telegram'=>array('Telegram','telegram','',''),
			'threads'=>array('Threads','threads','',''),
			'tiktok'=>array('Tiktok','tiktok','',''),
			'tinder'=>array('Tinder','tinder','',''),
			'tumblr'=>array('Tumblr','tumblr','',''),
			'twitch'=>array('Twitch','twitch','',''),
			'qq'=>array('QQ','qq','',''),
			'qzone'=>array('Qzone','qzone','',''),
			'vimeo'=>array('Vimeo','vimeo','',''),
			'vine'=>array('Vine','vine','',''),
			'vk'=>array('VK','vk','',''),
			'weibo'=>array('Weibo','weibo','',''),
			'xing'=>array('Xing','xing','',''),
			'yelp'=>array('Yelp','yelp','',''),
			'youtube'=>array('Youtube','youtube','',''),
			'viber'=>array('Viber','viber','',''),
			'wechat'=>array('Wechat','wechat','',''),
			'whatsapp'=>array('Whatsapp','whatsapp','',''),
			'zoom'=>array('Zoom','zoom','',''),
			'x'=>array('X','x','http://x.com/quanticalabs','1')
		);	
	}
	
	/**************************************************************************/
	
	function getSocialProfile()
	{
		return($this->socialProfile);
	}
	
	/**************************************************************************/
	
	function isSocialProfile($index)
	{
		return(array_key_exists($index,$this->getSocialProfile()) ? true : false);
	}
		
	/**************************************************************************/
}

/******************************************************************************/
/******************************************************************************/