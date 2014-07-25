<?php

require_once('experiment.php');

$iExperimentId		= $_POST['experiment_id'];
$oExperimentClass	= new Experiment();
$oExperiment		= $oExperimentClass->getExperiment($iExperimentId);
echo json_encode($oExperiment);

?>