<?php

/******************************************************************************/
/******************************************************************************/

require_once('define.php');

/******************************************************************************/

require_once(PLUGIN_CHBS_CLASS_PATH.'CHBS.File.class.php');
require_once(PLUGIN_CHBS_CLASS_PATH.'CHBS.Include.class.php');
require_once(PLUGIN_CHBS_CLASS_PATH.'CHBS.Widget.class.php');

CHBSInclude::includeClass(PLUGIN_CHBS_LIBRARY_PATH.'/stripe/init.php',array('Stripe\Stripe'));
CHBSInclude::includeFileFromDir(PLUGIN_CHBS_CLASS_PATH);

/******************************************************************************/
/******************************************************************************/