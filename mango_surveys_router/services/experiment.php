<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by services/*.php
 **/

class Experiment {

	/*** Variables ***/
	
	var $sDbConnection = null;

	/*** Constructor ***/
	
	function __construct() {
		// DB Connection
		$oParams				= json_decode(file_get_contents('../../config.json'));
		$this->oDbConnection	= mysqli_connect($oParams->sDbHost, $oParams->sDbUser, $oParams->sDbPassword, $oParams->sDbDatabase) or die("Error " . mysqli_error($link));
	}

	/*** Functions ***/

	/**
	 * Select all the experiments of limesurvey
	 *
	 * @return $aExperiments array list of all the experiments available
	 **/
	function getAllExperiments() {
		$aExperiments = array();
		$sQuery = "SELECT id, name FROM mango_experiment";
		$oResult = $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$aExperiments[] = $aRow;
		}
		return $aExperiments;
	}

	/**
	 * Load the name and the games of a given experiment
	 *
	 * @param $iExperimentId int id of the experiment to load
	 *
	 * @return $aRow array name and list of this experiment games
	 **/
	function getExperiment($iExperimentId) {
		$sQuery = "SELECT id, name, login_phase, results_phase FROM mango_experiment WHERE id = $iExperimentId";
		$oResult = $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$aGames = $this->getGamesFromExperiment($iExperimentId);
			$aRow['games'] = $aGames;
			$aRow['url'] = $this->getUrl($iExperimentId);
			return $aRow;
		}
	}

	/**
	 * Select all the games of limesurvey
	 *
	 * @return $aGames array of games (surveys)
	 **/
	function getAllGames() {
		$aGames = array();
		$sQuery = "SELECT surveyls_survey_id, surveyls_title FROM lime_surveys_languagesettings ORDER BY surveyls_title";
		$oResult = $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$aGames[] = $aRow;
		}
		return $aGames;
	}

	/**
	 * Select all the games of a given experiment
	 *
	 * @param $iExperimentId int id of the experiment to load
	 * 
	 * @return $aGames array of games (surveys)
	 **/
	function getGamesFromExperiment($iExperimentId) {
		$aGames = array();
		$sQuery = "SELECT survey_id, survey_order FROM mango_surveys_router WHERE experiment_id = $iExperimentId ORDER BY survey_order ASC";
		$oResult = $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$aGames[] = $aRow;
		}
		return $aGames;
	}

	/**
	 * Save an experiment and its games
	 *
	 * @param $iExperimentId int id of the experiment to save
	 * @param $sExperimentName string name of the experiment to save
	 * @param $bExperimentLoginPhase boolean if the experiment has a login phase
	 * @param $bExperimentResultsPhase boolean if the experiment has a results phase
	 * @param $aExperimentGames array list of games of the experiment to save
	 *
	 * @return void
	 **/
	function saveExperiment($iExperimentId, $sExperimentName, $bExperimentLoginPhase, $bExperimentResultsPhase, $aExperimentGames) {
		// If this experiment doesn't exist, add it
		if($iExperimentId == '') {
			$sQuery = "INSERT INTO mango_experiment (name, login_phase, results_phase) VALUES ('$sExperimentName', $bExperimentLoginPhase, $bExperimentResultsPhase)";
			$oResult = $this->oDbConnection->query($sQuery);
			$iExperimentId = $this->getExperimentIdByName($sExperimentName);
			// Add all games
			$sQuery = "INSERT INTO mango_surveys_router (experiment_id, survey_id, survey_order) VALUES ";
			$iLastKey = end(array_keys($aExperimentGames));
			foreach ($aExperimentGames as $iKey => $sValue) {
				if($sValue != '') {
					$sQuery .= "($iExperimentId, $sValue, $iKey)";
					$sQuery .= $iKey!=$iLastKey ? ", " : ";";
				}
			}
			$oResult = $this->oDbConnection->query($sQuery);
		// If this experiment already exists, update it
		} else {
			$sQuery = "UPDATE mango_experiment SET name = '$sExperimentName', login_phase = $bExperimentLoginPhase, results_phase = $bExperimentResultsPhase WHERE id = $iExperimentId";
			$oResult = $this->oDbConnection->query($sQuery);
			// Remove all experiment games
			$sQuery = "DELETE FROM mango_surveys_router WHERE experiment_id = $iExperimentId";
			$oResult = $this->oDbConnection->query($sQuery);
			// Add all games
			$sQuery = "INSERT INTO mango_surveys_router (experiment_id, survey_id, survey_order) VALUES ";
			$iLastKey = end(array_keys($aExperimentGames));
			foreach ($aExperimentGames as $iKey => $sValue) {
				if($sValue != '') {
					$sQuery .= "($iExperimentId, $sValue, $iKey)";
					$sQuery .= $iKey!=$iLastKey ? ", " : ";";
				}
			}
			$oResult = $this->oDbConnection->query($sQuery);
		}
	}

	/**
	 * Delete an experiment and its games
	 *
	 * @param $iExperimentId int id of the experiment to save
	 *
	 * @return void
	 **/
	function deleteExperiment($iExperimentId) {
		// Remove all experiment games
		$sQuery = "DELETE FROM mango_surveys_router WHERE experiment_id = $iExperimentId";
		$oResult = $this->oDbConnection->query($sQuery);
		// Remove experiment
		$sQuery = "DELETE FROM mango_experiment WHERE id = $iExperimentId";
		$oResult = $this->oDbConnection->query($sQuery);
	}

	/**
	 * Retrieve an experiment according to its name
	 *
	 * @param $sExperimentName string name of the experiment to search
	 *
	 * @return $aRow['id'] int id of the experiment searched
	 **/
	function getExperimentIdByName($sExperimentName) {
		$sQuery = "SELECT id FROM mango_experiment WHERE name = '$sExperimentName'";
		$oResult = $this->oDbConnection->query($sQuery);
		$aRow = mysqli_fetch_array($oResult);
		return $aRow['id'];
	}

	function getUrl($iExperimentId) {
		return 'http://grebdioz.sciences-po.fr/mango/mango_surveys_router/services/plugin.php?experiment=' . $iExperimentId . '&token=test&lang=fr&redirect';

	}
}

?>