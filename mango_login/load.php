<?php

/**
 * A PHP service to upload a list of users to add as Limesurvey users
 *
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 */

/*** VARIABLES ***/

$sFilePath		= 'csv/users.csv';
$sDelimiter		= ';';
$sTableUsers	= 'mango_users';
$iExperimentId	= 4;

/*** MAIN ***/

$iRow			= 0;
$sQuery			= "INSERT INTO $sTableUsers (login, password, experiment_id) VALUES ";
// Upload file
$fFile			= fopen($sFilePath, 'r');
// Parse file
if ($fFile !== FALSE) {
    while (($aLine = fgetcsv($fFile, 0, $sDelimiter)) !== FALSE) {
    	// Skip first row, because of headers
    	if($iRow != 0) {
    		if($iRow != 1) {
    			$sQuery .= ", ";
    		}
    		$sQuery .= "('" . $aLine[0] . "', '" . password_hash($aLine[1], PASSWORD_DEFAULT) . "', $iExperimentId)";
    	}
        $iRow++;
    }
    fclose($fFile);
    // Create connection to the DB
    $oParams = json_decode(file_get_contents('../config.json'));
    $oDbConnection = new mysqli($oParams->sDbHost, $oParams->sDbUser, $oParams->sDbPassword, $oParams->sDbDatabase) or die("Error " . mysqli_error($link));
	// Execute the request to add all users
	$oDbConnection->query($sQuery);

} else {
	echo 'Error while opening the file ' . $sFilePath . '</br>';
}


// For each user, add it as user of the experimentation needed

?>