<?php

// Called by experiments.js
require_once('experiment.php');

// $iExperimentId		= $_POST['experiment_id'];
$iExperimentId		= $_GET['experiment_id'];
$sPrefix			= 'export_scores_';
$oExperimentClass	= new Experiment();
$aGames				= $oExperimentClass->getGamesFromExperiment($iExperimentId);
$aScores			= array();

// 1. If user is not into mango_earning table, add it
$sQueryEarning	= "INSERT INTO mango_earning (token) VALUES ";
$sQuery			= "SELECT lime_tokens_" . $aGames[0][survey_id] . ".token AS token, mango_earning.token AS created FROM lime_tokens_" . $aGames[0][survey_id] . " LEFT JOIN mango_earning ON lime_tokens_" . $aGames[0][survey_id] . ".token = mango_earning.token AND mango_earning.experiment_id = $iExperimentId WHERE mango_earning.token IS NULL";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
$iResultCount	= $oResult->num_rows - 1;
$aRows			= mysqli_fetch_all($oResult);
foreach ($aRows as $iIndex => $aRow) {
	$sQueryEarning	.= "('$aRow[0]')";
	$sQueryEarning	.= ($iIndex != $iResultCount) ? ', ' : ';';
}
$oExperimentClass->oDbConnection->query($sQueryEarning);

// 2. For each game of the experiment, calculate score if needed
foreach ($aGames as $aGame) {
	$sFunctionName = $sPrefix . $aGame[survey_id];
	// If no score to calculate, no function is implemented
	if(function_exists($sFunctionName)) {
		$sFunctionName($iExperimentId, $oExperimentClass, $aScores);
	}
}

// 3. Selected randomly the chosen game

// Calculate score for game 'Coop - Jeu Catégorisation Peur/Colère'
function export_scores_867323($iExperimentId, &$oExperimentClass, &$aScores) {
	// No matching is needed for this game
	// No earning for this game
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
		$aScores[$sToken][6]['score'] = $iSum;
	}
}

// Calculate score for game 'Coop - Jeu Preferences'
function export_scores_948898($iExperimentId, &$oExperimentClass, &$aScores) {
	// No matching is needed for this game
	// No earning for this game
}

// Calculate score for game 'Coop - Jeu Public Good'
// Match with the last session with Paris 1
function export_scores_876211($iExperimentId, &$oExperimentClass, &$aScores) {
	// Matching is needed for this game, with 3 other people that have already participated
	$sQueryMatching	= "INSERT INTO mango_matching (user_token, game_id, participant_token, experiment_id) VALUES ";
	$sQueryEarning	= "";
	$bSaveMatching	= false;
	$aPlayers		= array();
	// Get tokens for that game
	$sQuery1		= "SELECT DISTINCT token FROM lime_survey_948898 WHERE submitdate IS NOT NULL";
	$oResult1		= $oExperimentClass->oDbConnection->query($sQuery1);
	while($aRows1 = mysqli_fetch_array($oResult1)) {
		// For each token, check is matching is already done
		$sQuery2	= "SELECT participant_token as participant FROM mango_matching WHERE user_token = '$aRows1[token]' AND game_id = 1 AND experiment_id = $iExperimentId";
		$oResult2	= $oExperimentClass->oDbConnection->query($sQuery2);
		// If matching is not done, do it
		if($oResult2->num_rows == 0) {
			// If matching is not done, create it
			$bSaveMatching		= true;
			$sSubquery3			= "SELECT token, completed FROM lime_tokens_46831_20130528 UNION SELECT token, completed FROM lime_tokens_17124_20130528 UNION SELECT token, completed FROM lime_tokens_948898";
			$sQuery3			= "SELECT DISTINCT a.token FROM ($sSubquery3) AS a WHERE ((NOT a.completed = 'N') AND (NOT a.token = '$aRow[token]')) ORDER BY RAND() LIMIT 3";
			$oResult3			= $oExperimentClass->oDbConnection->query($sQuery3);
			$aRows3				= mysqli_fetch_all($oResult3);
			$aRowsLastIndex3	= count($aRows3) - 1;
			foreach ($aRows3 as $iIndex => $aRow3) {
				$sQueryMatching	.= "('$aRows1[token]', 1, '$aRow3[0]', $iExperimentId)";
				$sQueryMatching	.= ($aRowsLastIndex3 == $iIndex) ? ';' : ', ';
				$aPlayers[]		= $aRow3[0];
			}
		} else {
			$aRows2		= mysqli_fetch_all($oResult2, MYSQLI_ASSOC);
			foreach ($aRows2 as $aRow2) {
				$aPlayers[]		= $aRow2[participant];
			}
		}
		$aScores[$aRows1[token]][1]['players'] = $aPlayers;
		// Calculate score
		$sQuery4			= "SELECT 876211X2131X74641 FROM lime_survey_876211 WHERE token = '$aRows1[token]'";
		$oResult4			= $oExperimentClass->oDbConnection->query($sQuery4);
		$aRows4				= mysqli_fetch_array($oResult4);
		$iMyContribution	= $aRows4[0];
		$iSumContributions	= $aRows4[0];
		foreach ($aPlayers as $aPlayer) {
			$sSubquery4			= "SELECT token, 46831X529X9811 AS score FROM lime_survey_46831_20130528 UNION SELECT token, 17124X603X11611 AS result FROM lime_survey_17124_20130528 UNION SELECT token, 876211X2131X74641 AS result FROM lime_survey_876211";
			$sQuery4			= "SELECT score FROM ($sSubquery4) AS tmp WHERE token = '$aPlayer'";
			$oResult4			= $oExperimentClass->oDbConnection->query($sQuery4);
			$aRows4				= mysqli_fetch_array($oResult4);
			$iSumContributions	+= $aRows4[0];
		}
		$iEarning = 10 - $iMyContribution + 0.4 * $iSumContributions;
		$aScores[$aRows1[token]][1]['score'] = $iEarning;
		$sQueryEarning .= "UPDATE mango_earning SET earning1 = $iEarning WHERE token = '$aRows1[token]';";
	}
	// Save matching
	if($bSaveMatching) {
		$oExperimentClass->oDbConnection->query($sQueryMatching);
	}
	// Save earning
	$oExperimentClass->oDbConnection->query($sQueryEarning);
}

?>