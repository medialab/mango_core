<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Plugin dependency : lang
 * 
 * Url to send to the user to start the experiment
 * (...)/mango/mango_surveys_router/plugin.php?experiment=XX&token=XX&lang=XX&redirect
 * 
 * Url to call with AJAX at the end of a LimeSurvey game
 * (...)/mango/mango_surveys_router/plugin.php?token=XX&sid=XX&redirect
 */

require_once('router.php');

session_start();

$router = new Router();
$router->launchExperiment();
$iSurveyNextId = $router->getNextSurvey();
$router->launchSurvey($iSurveyNextId);

?>
