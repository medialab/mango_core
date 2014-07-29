<?php

require_once('experiment.php');

$oExperimentClass	= new Experiment();
$oExperiments		= $oExperimentClass->getAllExperiments();
echo json_encode($oExperiments);

?>