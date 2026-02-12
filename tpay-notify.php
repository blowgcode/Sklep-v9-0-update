<?php

declare(strict_types=1);

@ini_set('display_errors','0');
@ini_set('html_errors','0');
error_reporting(0);

if(!headers_sent())
{
	header('Content-Type: text/plain; charset=UTF-8');
}

$_REQUEST['action']='payment_tpay';
$_GET['action']='payment_tpay';

$wpLoad=__DIR__.'/wp-load.php';
if(!file_exists($wpLoad))
{
	echo 'FALSE';
	exit;
}

require_once($wpLoad);

if(class_exists('CHBSPaymentTpay'))
{
	(new CHBSPaymentTpay())->receivePayment();
	echo 'FALSE';
	exit;
}

echo 'FALSE';
exit;
