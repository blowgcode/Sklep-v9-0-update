<?php

declare(strict_types=1);

@ini_set('display_errors','0');
@ini_set('html_errors','0');
error_reporting(0);

if(!headers_sent())
{
	header('Content-Type: text/plain; charset=UTF-8');
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
}

if(strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'))!=='POST')
{
	echo 'TRUE';
	exit;
}

define('CHBS_TPAY_NOTIFY_ENDPOINT',true);

$wpLoad=__DIR__.'/wp-load.php';
if(!file_exists($wpLoad))
{
	echo 'TRUE';
	exit;
}

require_once($wpLoad);

try
{
	if(class_exists('CHBSPaymentTpay'))
		(new CHBSPaymentTpay())->receivePayment();
}
catch(Throwable $exception)
{
	error_log('[CHBS Tpay notify] '.$exception->getMessage());
}

echo 'TRUE';
exit;
