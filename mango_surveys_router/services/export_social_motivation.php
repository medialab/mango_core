<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by scripts/experiments.js
 **/

require_once('experiment.php');

$oExperimentClass	= new Experiment();

// 1. Export social motivation into CSV file
$sFileContent	= "id,token,rank,screen_resolution,window_resolution,rewarded_line,displayed_line,user_answer,rewarded_score,is_rewarded,video_displayed,is_correct,delay_answer" . PHP_EOL;
$sQuery			= "SELECT id,token,rank,screen_resolution,window_resolution,rewarded_line,displayed_line,user_answer,rewarded_score,is_rewarded,video_displayed,is_correct,delay_answer FROM mango_social_motivation";
$oResult		= $oExperimentClass->oDbConnection->query($sQuery);
while($aRow = $oResult->fetch_array()) {
	$sFileContent .= $aRow['id'] . ',' . $aRow['token'] . ',' . $aRow['rank'] . ',' . $aRow['screen_resolution'] . ',' . $aRow['window_resolution'] . ',' . $aRow['rewarded_line'] . ',' . $aRow['displayed_line'] . ',' . $aRow['user_answer'] . ',' . $aRow['rewarded_score'] . ',' . $aRow['is_rewarded'] . ',' . $aRow['video_displayed'] . ',' . $aRow['is_correct'] . ',' . $aRow['delay_answer'] . PHP_EOL;
}
$fFile = fopen('../downloads/export_social_motivation.csv', 'w+');
fwrite($fFile, $sFileContent);
fclose($fFile);
$aReturn['status']	= 'success';
$aReturn['message']	= 'Please download the file export_social_motivation.csv';
echo json_encode($aReturn);

exit;

?>