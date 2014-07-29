<?php

// Called by experiments.js
require_once('experiment.php');

// $iExperimentId		= $_POST['experiment_id'];
$iExperimentId		= $_GET['experiment_id'];
$sPrefix			= 'export_results_';
$oExperimentClass	= new Experiment();
$aGames				= $oExperimentClass->getGamesFromExperiment($iExperimentId);
$aResults			= array();
foreach ($aGames as $aGame) {
	$sFunctionName = $sPrefix . $aGame['survey_id'];
	if(function_exists($sFunctionName)) {
		$sFunctionName($oExperimentClass, $aResults);
	} else {
		echo "This function doesn't exist : $sFunctionName<br/>";
	}
}

// Calculate result for game 'Coop - Questionnaire Anxiété'
function export_results_778262() {
	// This is a questionnaire, there is no result to export
	return false;
}

// Calculate result for game 'Coop - Jeu Catégorisation Peur/Colère'
function export_results_867323(&$oExperimentClass, &$aResults) {
	// No matching is needed for this game
	// The score is the total of correct answer
	$sQuery = "SELECT * FROM lime_survey_867323";
	$oResult = $oExperimentClass->oDbConnection->query($sQuery);
	while($aRow = mysqli_fetch_array($oResult)) {
		$iSum = 0;
		$sToken = $aRow['token'];
		foreach ($aRow as $sKey => $sValue) {
			if((strpos($sKey, '_correct') !== false) && ($sValue == 1)) {
				$iSum++;
			}
		}
		if(!array_key_exists($sToken, $aResults)) {
			$aResults[$sToken] = array();
		}
		$aResults[$sToken]['fear'] = $iSum;
	}
	return $aResults;
}

// Calculate result for game 'Coop - Jeu Public Good'
function export_results_876211(&$oExperimentClass, &$aResults) {
	// matching with 3 other people that have already participated
}

// Calculate result for game 'Coop - Jeu Preferences'
// MAtch with the last session with Paris 1
function export_results_948898(&$oExperimentClass, &$aResults) {
	// Matching is needed for this game
	$sMatching	= 'INSERT INTO lime_participant (user_token, game_id, participant_token) VALUES ';
	// Get tokens for that game
	$sQuery		= "SELECT DISTINCT token FROM lime_survey_948898 WHERE submitdate IS NOT NULL";
	$oResult	= $oExperimentClass->oDbConnection->query($sQuery);
	while($aRow = mysqli_fetch_array($oResult)) {
		// For each token, create the matching
		$sSubquery = "SELECT token, completed FROM lime_tokens_46831_20130528 UNION SELECT token, completed FROM lime_tokens_17124_20130528 UNION SELECT token, completed FROM lime_tokens_948898";
		$sQuery = "SELECT DISTINCT a.token FROM ($sSubquery) AS a WHERE ((NOT a.completed = 'N') AND (NOT a.token = '$aRow[token]')) ORDER BY RAND() LIMIT 3";
		$oResult2 = $oExperimentClass->oDbConnection->query($sQuery);
		$aRows = mysqli_fetch_all($oResult2);
		$aRowsLastIndex = count($aRows) - 1;
		foreach ($aRows as $iIndex => $aRow2) {
			$sMatching .= "('$aRow[token]', 1, '$aRow2[0]')";
			$sMatching .= ($aRowsLastIndex == $iIndex) ? ';' : ', ';
		}
	}
	// Save it
	// $oResult2 = $oExperimentClass->oDbConnection->query($sMatching);
	echo $sMatching;
}

?>