<?php
/* 
Plugin Name: Chauffeur Taxi Booking System for WordPress Add-on: GA Events
Requires Plugins: chauffeur-booking-system
Plugin URI: https://codecanyon.net/collections/11597341-add-ons-for-chauffeur-taxi-booking-system
Description: The Google Analytics Add-On for the Chauffeur Taxi Booking System allows you to track key events like vehicle selection, extra options, payment method choice, form navigation, and order submission directly in Google Analytics, providing valuable insights into customer behavior.
Author: QuanticaLabs
Version: 1.1
Author URI: https://codecanyon.net/user/quanticalabs
*/

load_plugin_textdomain('chauffeur-booking-system-extension-ga-event',false,'chauffeur-booking-system-extension-ga-event/languages');

require_once('define.php');
require_once('class/CHBSE.GAEvent.Extension.class.php');

$Extension=new CHBSEGAEExtension();

register_activation_hook(__FILE__,array($Extension,'activate'));

add_action('init',array($Extension,'init'),100);