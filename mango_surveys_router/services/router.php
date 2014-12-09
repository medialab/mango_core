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
		$this->sLang 	= $_GET['lang'];
		$sFile			= "../lang/lang.xml";
		$this->_translator = new translator($this->sLang, $sFile);

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
	 * Return the current token passed as a GET argument
	 * Echo an error message if an error occurs
	 *
	 * @return the troken of the current token
	 **/
	private function getCurrentToken() {
		if(isset($_GET) && isset($_GET['token'])) {
			$sToken = $_GET['token'];
		} else {
			echo $this->_translator->error_token;
			exit();
		}
		return $sToken;
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
	 * Check if the current token has already completed this survey
	 * 
	 * @return bool, 1 if that token has already completed that survey 0 otherwise
	 **/
	function hasCompletedSurvey($iSurveyId, $sToken) {
		// Check if this token has already completed this survey
		$sQuery 		= "SELECT completed FROM lime_tokens_$iSurveyId WHERE token = '$sToken'";
		$oResult		= $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$isCompleted = $aRow['completed'];
		}
		return (isset($isCompleted) && ($isCompleted != 'N'));
	}

	/**
	 * Get the order of the next survey to be launched
	 * 
	 * @return $iSurveyOrderNext int order of the next suvey to be launched
	 **/
	function getNextSurveyOrder($iSurveyId) {
		$iExperimentId 	= $this->getCurrentExperiment();
		$sQuery			= "SELECT survey_order FROM mango_surveys_router WHERE experiment_id = $iExperimentId AND survey_id = $iSurveyId";
		$oResult		= $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$iSurveyOrderNext = (int) $aRow['survey_order'] + 1;
		}
		return $iSurveyOrderNext;
	}
	
	/**
	 * Return the id of the next survey to be launched
	 * Return -1 if there is no more survey to be launched
	 * 
	 * @return $iSurveyNextId int id of the next suvey to be launched
	 **/
	function getNextSurvey($iPreviousSurveyId = Null) {
		// Get current token
		$sToken					= $this->getCurrentToken();
		// Get current experiment id
		$iExperimentId 			= $this->getCurrentExperiment();
		// Get the order of the next survey
		if(!is_null($iPreviousSurveyId)) {
			$iSurveyOrderNext 	= $this->getNextSurveyOrder($iPreviousSurveyId);
		} else if(isset($_GET) && isset($_GET['sid'])) {
			$iSurveyId 			= (int) $_GET['sid'];
			$iSurveyOrderNext 	= $this->getNextSurveyOrder($iSurveyId);
		} else {
			$iSurveyOrderNext 	= 0;
		}
		// Get the id of the next survey to launch
		$sQuery 				= "SELECT survey_id FROM mango_surveys_router WHERE experiment_id = $iExperimentId AND survey_order = $iSurveyOrderNext";
		$oResult				= $this->oDbConnection->query($sQuery);
		$iSurveyNextId			= -1;
		while($aRow = mysqli_fetch_array($oResult)) {
			$iSurveyNextId 		= (int) $aRow['survey_id'];
		}
		if(($iSurveyNextId != -1) && ($this->hasCompletedSurvey($iSurveyNextId, $sToken))) {
			return $this->getNextSurvey($iSurveyNextId);
		} else {
			return $iSurveyNextId;
		}
	}

	/**
	 * Test if the current experiment has a result phase at the end
	 *
	 * @return boolean true if this experiment has a results phase, else return false
	 **/
	function hasResultsPhase($iExperimentId) {
		$sQuery 	= "SELECT results_phase FROM mango_experiment WHERE id = $iExperimentId";
		$oResult 	= $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$bExperimentResultsPhase = (int) $aRow['results_phase'];
		}
		return $bExperimentResultsPhase;
	}

	/**
	 * Test if the current experiment should generate its tokens on the fly
	 *
	 * @return boolean true if the experiment should generate its tokens on the fly
	 **/
	function shouldGenerateTokens($iExperimentId) {
		$sQuery 	= "SELECT generate_tokens FROM mango_experiment WHERE id = $iExperimentId";
		$oResult	= $this->oDbConnection->query($sQuery);
		while($aRow = mysqli_fetch_array($oResult)) {
			$bExperimentGenerateTokens = (int) $aRow['generate_tokens'];
		}
		return $bExperimentGenerateTokens;
	}

	function addToken($iSurveyId, $sToken) {
		if($iSurveyId != '' && $sToken != '') {
			// Check if token already exists into database
			$sQuery 	= "SELECT count(token) AS c FROM lime_tokens_$iSurveyId WHERE token = '$sToken'";
			$oResult 	= $this->oDbConnection->query($sQuery);
			$aRow 		= mysqli_fetch_array($oResult);
			if($aRow['c'] == 0) {
				// Create token into database
				$query = "INSERT INTO lime_tokens_$iSurveyId (firstname, emailstatus, token, language, sent, remindersent, remindercount, completed, validfrom, validuntil, mpid)
					VALUES ('$sToken', 'OK', '$sToken', 'fr', 'N', 'N', 0, 'N', NULL, NULL, NULL)";
				if($stmt = $this->oDbConnection->prepare($query)) {
					$stmt->execute();
					$stmt->close();
				}
			}
		}
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
		$iExperimentId	= $this->getCurrentExperiment();
		// Get the root url
		$sRootUrl		= "http://{$_SERVER['HTTP_HOST']}/" . $this->oParams->sInstallFolder;
		// Get current token
		$sToken			= $this->getCurrentToken();
		// If tokens should be generated on the fly
		if($this->shouldGenerateTokens($iExperimentId)) {
			$this->addToken($iSurveyId, $sToken);
		}
		if($iSurveyId == -1) {
			// If no more game but a result page, display it
			if($this->hasResultsPhase($iExperimentId)) {
				$sUrl = $sRootUrl . 'mango/mango_surveys_router/views/results_' . $iExperimentId . '.php?token=' . $sToken . '&lang=' . $this->sLang;
			// Else redirect to the ending page
			} else {
				if($iExperimentId == 1) {
					$sUrl = 'http://surveys.ipsosinteractive.com/mrIWeb/mrIWeb.dll?I.Project=S14008323&id=' . $sToken . '&rewards=4&stat=complete';
				} else {
					$sUrl = $sRootUrl . 'mango/mango_surveys_router/views/exit_' . $iExperimentId . '.php?lang=' . $this->sLang;
				}
			}
		} else {
			$sUrl = $sRootUrl . "index.php?r=survey/index/sid/$iSurveyId/lang/" . $this->sLang . "/token/$sToken";
		}
		if(isset($_GET) && isset($_GET['redirect'])) {
			header("Location: {$sUrl}");
		} else {
			echo $sUrl;
		}
	}
}

?>
