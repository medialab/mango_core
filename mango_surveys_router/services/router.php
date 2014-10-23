<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by services/plugin.php
 **/

require_once('../../lang/lang.php');

class Router {
	
	/*** Variables ***/
	
	var $_translator;
	var $sDbConnection	= null;
	var $oParams		= null;
	
	/*** Constructor ***/
	
	function __construct() {
		$lang = $_GET['lang'];
		$file = "../lang/lang.xml";
		$this->_translator = new translator($lang, $file);

		// DB Connection
		$this->oParams			= json_decode(file_get_contents('../../config.json'));
		$this->oDbConnection	= mysqli_connect($this->oParams->sDbHost, $this->oParams->sDbUser, $this->oParams->sDbPassword, $this->oParams->sDbDatabase) or die("Error " . mysqli_error($link));
	}
	
	/*** Functions ***/

	/**
	 * Return the id of the current experiment stored into the PHP session
	 * Echo an error message if an error occurs
	 *
	 * @return the id of the current experiment
	 **/
	private function getCurrentExperiment() {
		if(isset($_SESSION) && isset($_SESSION['experiment'])) {
			$iExperimentId = (int) $_SESSION['experiment'];
		} else {
			echo $this->_translator->error_experiment;
			exit();
		}
		return $iExperimentId;
	}
	
	/**
	 * Called at the beginning of the experiment
	 * Set the experiment id passed as get parameter into a session variable
	 * 
	 * @return void
	 **/
	function launchExperiment() {
		if(isset($_GET) && isset($_GET['experiment'])) {
			$_SESSION['experiment'] = (int) $_GET['experiment'];
		}
	}
	
	/**
	 * Return the id of the next survey to be launched
	 * Return -1 if there is no more survey to be launched
	 * 
	 * @return $iSurveyNextId int id of the next suvey to be launched
	 **/
	function getNextSurvey() {
		$iExperimentId = $this->getCurrentExperiment();
		// Get the order of the next survey
		if(isset($_GET) && isset($_GET['sid'])) {
			$iSurveyId = (int) $_GET['sid'];
			$sQuery = "SELECT survey_order FROM mango_surveys_router WHERE experiment_id = $iExperimentId AND survey_id = $iSurveyId";
			$oResult = $this->oDbConnection->query($sQuery);
			while($aRow = mysqli_fetch_array($oResult)) {
				$iSurveyOrderNext = (int) $aRow['survey_order'] + 1;
			}
		} else {
			$iSurveyOrderNext = 0;
		}
		// Get the id of the next survey to launch
		$sQuery = "SELECT survey_id FROM mango_surveys_router WHERE experiment_id = $iExperimentId AND survey_order = $iSurveyOrderNext";
		$oResult = $this->oDbConnection->query($sQuery);
		$iSurveyNextId = -1;
		while($aRow = mysqli_fetch_array($oResult)) {
			$iSurveyNextId = (int) $aRow['survey_id'];
		}
		return $iSurveyNextId;
	}

	/**
	 * Test if the current experiment has a result phase at the end
	 *
	 * @return boolean true if this experiment has a results phase, else return false
	 **/
	function hasResultsPhase() {
		$iExperimentId = $this->getCurrentExperiment();
		$sQuery = "SELECT results_phase FROM mango_experiment WHERE id = $iExperimentId";
		$oResult = $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$bExperimentResultsPhase = (int) $aRow['results_phase'];
		}
		return $bExperimentResultsPhase;
	}
	
	/**
	 * Either print or redirect to the next survey to launch
	 * according to the token, the experiment id and the current survey
	 * 
	 * @param int $iSurveyId id of the survey to be launched
	 *
	 * @return void
	 **/
	function launchSurvey($iSurveyId) {
		$iExperimentId = $this->getCurrentExperiment();
		// Get the root url
		$sRootUrl = "http://{$_SERVER['HTTP_HOST']}/" . $this->oParams->sInstallFolder;
		// Get the user token
		$iToken = ((isset($_GET) && isset($_GET['token'])) ? $_GET['token'] : -1);
		if($iToken == -1) {
			echo $this->_translator->error_token;
			exit();
		}
		$sLang = (isset($_SESSION['s_lang']) ? $_SESSION['s_lang'] : (isset($_GET['lang']) ? $_GET['lang'] : 'en'));
		if($iSurveyId == -1) {
			// If no more game but a result page, display it
			if($this->hasResultsPhase()) {
				$sUrl = $sRootUrl . "mango/results_" . $iExperimentId . ".php";
			// Else redirect to the ending page
			} else {
				$sUrl = $sRootUrl;
			}
		} else {
			$sUrl = $sRootUrl . "index.php?r=survey/index/sid/$iSurveyId/lang/$sLang/token/$iToken";
		}
		if(isset($_GET) && isset($_GET['redirect'])) {
			header("Location: {$sUrl}");
		} else {
			echo $sUrl;
		}
	}
}

?>
