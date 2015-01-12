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

// 1. Check if this experiment id exists into database, return an error
$sQuery		= "SELECT COUNT(id) AS c FROM mango_experiment WHERE id = $iExperimentId";
$oResult	= $oExperimentClass->oDbConnection->query($sQuery);
$aRows		= $oResult->fetch_array();
if($aRows[c] == 0) {
	$aReturn['status']	= 'error';
	$aReturn['message']	= 'This experiment id doesn\'t exist into database!';
	echo json_encode($aReturn);
	exit;
}

// 2. Export final mango simulation into CSV file
$sFileContent	= "id,token,game,updated" . PHP_EOL;
$sQuery			= "SELECT id,token,game,updated FROM mango_simulation";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
while($aRow = $oResult->fetch_array()) {
	$sFileContent .= $aRow['id'] . ',' . $aRow['token'] . ',' . $aRow['game'] . ',' . $aRow['updated'] . PHP_EOL;
}
$fFile = fopen('../downloads/export_simulation_' . $iExperimentId . '.csv', 'w');
fwrite($fFile, $sFileContent);
fclose($fFile);
$aReturn['status']	= 'success';
$aReturn['message']	= 'Please download the file export_earning_' . $iExperimentId . '.csv';
echo json_encode($aReturn);

exit;

?>