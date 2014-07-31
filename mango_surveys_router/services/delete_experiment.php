<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by scripts/experiments.js
 **/

require_once('experiment.php');

$iExperimentId		= $_POST['experiment_id'];

$oExperimentClass	= new Experiment();
$oExperimentClass->deleteExperiment($iExperimentId);

?>