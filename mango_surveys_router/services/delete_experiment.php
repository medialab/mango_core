<?php

// Called by form.js
require_once('experiment.php');

$iExperimentId		= $_POST['experiment_id'];

$oExperimentClass	= new Experiment();
$oExperimentClass->deleteExperiment($iExperimentId);

?>