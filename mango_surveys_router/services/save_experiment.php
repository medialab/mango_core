<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by scripts/experiments.js
 **/

require_once('experiment.php');

$iExperimentId				= $_POST['experiment_id'];
$sExperimentName			= $_POST['experiment_name'];
$bExperimentLoginPhase		= $_POST['experiment_login_phase'];
$bExperimentResultsPhase	= $_POST['experiment_results_phase'];
$aExperimentGames			= $_POST['experiment_games'];

$oExperimentClass			= new Experiment();
$oExperimentClass->saveExperiment($iExperimentId, $sExperimentName, $bExperimentLoginPhase, $bExperimentResultsPhase, $aExperimentGames);

?>