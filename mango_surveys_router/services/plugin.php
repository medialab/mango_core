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

require_once('router.php');

session_start();

$router			= new Router();
$router->launchExperiment();

$iSurveyNextId	= $router->getNextSurvey();
$router->launchSurvey($iSurveyNextId);

?>
