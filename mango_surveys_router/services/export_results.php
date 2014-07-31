<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by scripts/experiments.js
 **/

require_once('experiment.php');

$iExperimentId		= $_POST['experiment_id'];
$sPrefix			= 'export_scores_';
$oExperimentClass	= new Experiment();
$aGames				= $oExperimentClass->getGamesFromExperiment($iExperimentId);
$aScores			= array();

// 1. Check if this experiment id exists into database, if it doesn't return fail
$sQuery		= "SELECT COUNT(id) AS c FROM mango_experiment WHERE id = $iExperimentId";
$oResult	= $oExperimentClass->oDbConnection->query($sQuery);
$aRows		= $oResult->fetch_array();
if($aRows[c] == 0) {
	$aReturn['status']	= 'error';
	$aReturn['message']	= 'This experiment id doesn\'t exist into database!';
	echo json_encode($aReturn);
	exit;
}

// 2. If user is not into mango_earning table, add it
$sQueryEarning	= "INSERT INTO mango_earning (token, experiment_id) VALUES ";
$sQuery			= "SELECT lime_tokens_" . $aGames[0][survey_id] . ".token AS token, mango_earning.token AS created FROM lime_tokens_" . $aGames[0][survey_id] . " LEFT JOIN mango_earning ON lime_tokens_" . $aGames[0][survey_id] . ".token = mango_earning.token AND mango_earning.experiment_id = $iExperimentId WHERE mango_earning.token IS NULL";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
$iResultCount	= $oResult->num_rows;
$aRows			= $oResult->fetch_all();
// Clean MySQL results
$oResult->close();
// For each token
foreach($aRows as $iIndex => $aRow) {
	$sQueryEarning	.= "('$aRow[0]', $iExperimentId)";
	$sQueryEarning	.= ($iIndex != $iResultCount - 1) ? ', ' : ';';
}
if($iResultCount != 0) {
	$oExperimentClass->oDbConnection->multi_query($sQueryEarning);
	// Needed to flush multi_query results to avoid the error "Commands out of sync"
	while(mysqli_more_results($oExperimentClass->oDbConnection)) {
	    mysqli_store_result($oExperimentClass->oDbConnection);
	    mysqli_next_result($oExperimentClass->oDbConnection);
	}
}

// 3. For each game of the experiment, calculate score if needed
$sQueryUpdate = "";
foreach($aGames as $aGame) {
	$sFunctionName = $sPrefix . $aGame[survey_id];
	// If no score to calculate, no function is implemented
	if(function_exists($sFunctionName)) {
		$sFunctionName($iExperimentId, $oExperimentClass, $aScores, $sQueryUpdate);
	}
}

// 4. For each token, select randomly the chosen game
// List of games for experiment Coop - Session 02
if($iExperimentId == '2') {
	$aGamesList		= array(1, 2, 4);
}
$sQuery			= "SELECT token, earning1, earning2, earning4 FROM mango_earning WHERE chosenGame IS NULL";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
$aRows			= $oResult->fetch_all();
foreach($aRows as $aRow) {
	$iChosenIndex	= array_rand($aGamesList);
	$iChosenGame	= $aGamesList[$iChosenIndex];
	if(!is_null($aScores[$aRow[0]][$iChosenGame]['score'])) {
		$sQueryUpdate	.= "UPDATE mango_earning SET chosenGame = $iChosenGame WHERE token = '$aRow[0]'; UPDATE mango_earning SET earning = " . $aScores[$aRow[0]][$iChosenGame]['score'] . " WHERE token = '$aRow[0]'; ";
	}
}

// 5. Save earning
$oExperimentClass->oDbConnection->multi_query($sQueryUpdate);
// Needed to flush multi_query results to avoid the error "Commands out of sync"
while(mysqli_more_results($oExperimentClass->oDbConnection)) {
    mysqli_store_result($oExperimentClass->oDbConnection);
    mysqli_next_result($oExperimentClass->oDbConnection);
}

// 6. Export final scores into CSV file
$sFileContent	= "token,earning" . PHP_EOL;
$sQuery			= "SELECT token,earning FROM mango_earning WHERE experiment_id = $iExperimentId";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
$aRows			= $oResult->fetch_all();
foreach($aRows as $aRow) {
	$sFileContent .= "$aRow[0],$aRow[1]" . PHP_EOL;
}
$fFile = fopen('../export_' . (($iExperimentId < 10) ? '0' . $iExperimentId : $iExperimentId) . '.csv', 'w');
fwrite($fFile, $sFileContent);
fclose($fFile);
$aReturn['status']	= 'success';
$aReturn['message']	= 'Please download the file export_' . (($iExperimentId < 10) ? '0' . $iExperimentId : $iExperimentId) . '.csv';
echo json_encode($aReturn);
exit;

/**
 * Calculate score for game 'Coop - Jeu Catégorisation Peur/Colère'
 * The score is the total of correct answer
 * 
 * @param $iExperimentId int id of the experiment to export
 * @param $oExperimentClass object experiment object
 * @param $aScores array containing all the scores calculated for eah game for each token (participant)
 * @param $sQueryUpdate string MySQL query that will be executed later, containing the UPDATE commands
 *
 * @return void
 **/
function export_scores_867323($iExperimentId, &$oExperimentClass, &$aScores, &$sQueryUpdate) {
	// No matching is needed for this game
	// No earning for this game
	// Get all the anwers of that survey
	$sQuery		= "SELECT lime_survey_867323.*, mango_earning.score6 FROM lime_survey_867323 LEFT JOIN mango_earning ON lime_survey_867323.token = mango_earning.token WHERE mango_earning.score6 IS NULL";
	$oResult	= $oExperimentClass->oDbConnection->query($sQuery);
	// For each token (participant)
	while($aRow = $oResult->fetch_array()) {
		$iSum = 0;
		$sToken = $aRow[token];
		// Foreach question, if it is a "correct" question
		foreach ($aRow as $sKey => $sValue) {
			if((strpos($sKey, '_correct') !== false) && ($sValue == 1)) {
				$iSum++;
			}
		}
		$aScores[$sToken][6]['score']	= $iSum;
		$sQueryUpdate					.= "UPDATE mango_earning SET score6 = $iSum WHERE token = '$aRow[token]'; ";
	}
	// Clean MySQL results
	$oResult->close();
}

/**
 * Calculate score for game 'Coop - Jeu Dictator'
 * The score is : 10 - token_contribution
 *
 * @param $iExperimentId int id of the experiment to export
 * @param $oExperimentClass object experiment object
 * @param $aScores array containing all the scores calculated for eah game for each token (participant)
 * @param $sQueryUpdate string MySQL query that will be executed later, containing the UPDATE commands
 *
 * @return void
 **/
function export_scores_336985($iExperimentId, &$oExperimentClass, &$aScores, &$sQueryUpdate) {
	// No matching is needed for this game
	// Get tokens for that game
	$sQuery1		= "SELECT DISTINCT lime_survey_336985.token, mango_earning.earning2 FROM lime_survey_336985 LEFT JOIN mango_earning ON lime_survey_336985.token = mango_earning.token WHERE lime_survey_336985.submitdate IS NOT NULL AND mango_earning.earning2 IS NULL";
	$oResult1		= $oExperimentClass->oDbConnection->query($sQuery1);
	while($aRows1 = $oResult1->fetch_array()) {
		// For each token, calculate score if no score has been saved yet
		$sQuery2								= "SELECT 336985X2144X76941 FROM lime_survey_336985 WHERE token = '$aRows1[token]'";
		$oResult2								= $oExperimentClass->oDbConnection->query($sQuery2);
		$aRows2									= $oResult2->fetch_array();
		$iMyContribution						= $aRows2[0];
		$iEarning								= 10 - $iMyContribution;
		$aScores[$aRows1[token]][2]['score']	= $iEarning;
		$sQueryUpdate							.= "UPDATE mango_earning SET earning2 = $iEarning WHERE token = '$aRows1[token]'; ";
	}
	// Clean MySQL results
	if($oResult1->num_rows > 0) {
		$oResult2->close();
	}
	$oResult1->close();
}

/**
 * Calculate score for game 'Coop - Jeu Trust'
 * The score is : 10 - token_contribution
 *
 * @param $iExperimentId int id of the experiment to export
 * @param $oExperimentClass object experiment object
 * @param $aScores array containing all the scores calculated for eah game for each token (participant)
 * @param $sQueryUpdate string MySQL query that will be executed later, containing the UPDATE commands
 *
 * @return void
 **/
function export_scores_914824($iExperimentId, &$oExperimentClass, &$aScores, &$sQueryUpdate) {
	// No matching is needed for this game
	// Get tokens for that game
	$sQuery1		= "SELECT DISTINCT lime_survey_914824.token, mango_earning.earning4 FROM lime_survey_914824 LEFT JOIN mango_earning ON lime_survey_914824.token = mango_earning.token WHERE lime_survey_914824.submitdate IS NOT NULL AND mango_earning.earning4 IS NULL";
	$oResult1		= $oExperimentClass->oDbConnection->query($sQuery1);
	while($aRows1 = $oResult1->fetch_array()) {
		// For each token, calculate score if no score has been saved yet
		$sQuery2								= "SELECT 914824X2148X77031 FROM lime_survey_914824 WHERE token = '$aRows1[token]'";
		$oResult2								= $oExperimentClass->oDbConnection->query($sQuery2);
		$aRows2									= $oResult2->fetch_array();
		$iMyContribution						= $aRows2[0];
		$iEarning								= 10 - $iMyContribution;
		$aScores[$aRows1[token]][4]['score']	= $iEarning;
		$sQueryUpdate							.= "UPDATE mango_earning SET earning4 = $iEarning WHERE token = '$aRows1[token]'; ";
	}
}

/**
 * Calculate score for game 'Coop - Jeu Public Good'
 * Matching with 3 other participants done with the last session with Paris 1
 * The score is : 10 - token_contribution + (1.6 / 4) * sum_all_contributions
 *
 * @param $iExperimentId int id of the experiment to export
 * @param $oExperimentClass object experiment object
 * @param $aScores array containing all the scores calculated for eah game for each token (participant)
 * @param $sQueryUpdate string MySQL query that will be executed later, containing the UPDATE commands
 *
 * @return void
 **/
function export_scores_876211($iExperimentId, &$oExperimentClass, &$aScores, &$sQueryUpdate) {
	// Matching
	$sQueryMatching	= "INSERT INTO mango_matching (user_token, game_id, participant_token, experiment_id) VALUES ";
	$bSaveMatching	= false;
	$aPlayers		= array();
	// Get tokens for that game
	$sQuery1		= "SELECT DISTINCT lime_survey_948898.token, mango_earning.earning1 FROM lime_survey_948898 LEFT JOIN mango_earning ON lime_survey_948898.token = mango_earning.token WHERE lime_survey_948898.submitdate IS NOT NULL AND mango_earning.earning1 IS NULL";
	$oResult1		= $oExperimentClass->oDbConnection->query($sQuery1);
	while($aRows1 = $oResult1->fetch_array()) {
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
			$aRows3				= $oResult3->fetch_all();
			$aRowsLastIndex3	= count($aRows3) - 1;
			foreach ($aRows3 as $iIndex => $aRow3) {
				$sQueryMatching	.= "('$aRows1[token]', 1, '$aRow3[0]', $iExperimentId)";
				$sQueryMatching	.= ($aRowsLastIndex3 == $iIndex) ? ';' : ', ';
				$aPlayers[]		= $aRow3[0];
			}
		} else {
			$aRows2		= $oResult2->fetch_all(MYSQLI_ASSOC);
			foreach ($aRows2 as $aRow2) {
				$aPlayers[]		= $aRow2[participant];
			}
		}
		$aScores[$aRows1[token]][1]['players'] = $aPlayers;
		// Calculate score
		$sQuery4			= "SELECT 876211X2131X74641 FROM lime_survey_876211 WHERE token = '$aRows1[token]'";
		$oResult4			= $oExperimentClass->oDbConnection->query($sQuery4);
		$aRows4				= $oResult4->fetch_array();
		$iMyContribution	= $aRows4[0];
		$iSumContributions	= $aRows4[0];
		foreach ($aPlayers as $aPlayer) {
			$sSubquery4			= "SELECT token, 46831X529X9811 AS score FROM lime_survey_46831_20130528 UNION SELECT token, 17124X603X11611 AS result FROM lime_survey_17124_20130528 UNION SELECT token, 876211X2131X74641 AS result FROM lime_survey_876211";
			$sQuery4			= "SELECT score FROM ($sSubquery4) AS tmp WHERE token = '$aPlayer'";
			$oResult4			= $oExperimentClass->oDbConnection->query($sQuery4);
			$aRows4				= $oResult4->fetch_array();
			$iSumContributions	+= $aRows4[0];
		}
		$iEarning = 10 - $iMyContribution + 0.4 * $iSumContributions;
		$aScores[$aRows1[token]][1]['score'] = $iEarning;
		$sQueryUpdate .= "UPDATE mango_earning SET earning1 = $iEarning WHERE token = '$aRows1[token]'; ";
	}
	// Save matching
	if($bSaveMatching) {
		$oExperimentClass->oDbConnection->multi_query($sQueryMatching);
		// Needed to flush multi_query results to avoid the error "Commands out of sync"
		while(mysqli_more_results($oExperimentClass->oDbConnection)) {
		    mysqli_store_result($oExperimentClass->oDbConnection);
		    mysqli_next_result($oExperimentClass->oDbConnection);
		}
	}
}

?>