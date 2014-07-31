<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by scripts/experiments.js
 **/

require_once('experiment.php');

$oExperimentClass	= new Experiment();
$oExperiments		= $oExperimentClass->getAllExperiments();

echo json_encode($oExperiments);

?>