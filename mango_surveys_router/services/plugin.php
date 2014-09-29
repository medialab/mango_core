<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Plugin dependency : lang
 * 
 * Url to send to the user to start the experiment
 * (...)/mango/mango_surveys_router/services/plugin.php?experiment=XX&token=XX&lang=XX&redirect
 * 
 * Url to set as End Url :
 * 1. Limsurvey Admin Panel -> Select your survey -> Survey properties -> Edit text elements -> End Url
 * 2. Limsurvey Admin Panel -> Select your survey -> Survey properties -> General settings -> Presentation & navigation -> Automatically load URL when survey complete? : Yes
 * (...)/mango/mango_surveys_router/services/plugin.php?token={TOKEN}&sid={SID}&redirect
 **/

// Load dependencies
require_once('router.php');
require_once('Mobile_Detect.php');
require_once('../../lang/lang.php');

// Mobile detection
$detection 			= new Mobile_Detect();

if($detection->isMobile() || $detection->isTablet()) {
	echo 'Cette expérimentation n\'est pas disponible sur Smartphone et sur tablette. Merci d\'utiliser un ordinateur pour prendre part à cette expérimentation.';
} else {
	session_start();
	
	$router			= new Router();
	$router->launchExperiment();

	$iSurveyNextId	= $router->getNextSurvey();
	$router->launchSurvey($iSurveyNextId);
}

?>
