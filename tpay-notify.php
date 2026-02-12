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
define('CHBS_TPAY_NOTIFY_ENDPOINT',true);

$wpLoad=__DIR__.'/wp-load.php';
if(!file_exists($wpLoad))
{
	echo 'TRUE';
	exit;
}

require_once($wpLoad);

if(class_exists('CHBSPaymentTpay'))
{
	if((isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string)$_SERVER['REQUEST_METHOD']) : '')!=='POST')
	{
		echo 'TRUE';
		exit;
	}

	try
	{
		(new CHBSPaymentTpay())->receivePayment();
	}
	catch(Throwable $exception)
	{
		error_log('[CHBS Tpay notify] '.$exception->getMessage());
		echo 'TRUE';
		exit;
	}

	echo 'TRUE';
	exit;
}

echo 'TRUE';
exit;
