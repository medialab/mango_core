<?php


/********** VARIABLES **********/

$table_matching				= "mango_matching";

// Session variables
$sUserToken 				= $_GET["token"];
$iGameId 					= $_GET["game"];
$sExperimentId				= (isset($_SESSION) && isset($_SESSION['experiment'])) ? $_SESSION['experiment'] : 0;

/********** PROGRAM **********/

// DB connection
$oDBConnection				= json_decode(file_get_contents('../../config.json'));
$mysqli 					= mysqli_connect($oDBConnection->sDbHost, $oDBConnection->sDbUser, $oDBConnection->sDbPassword, $oDBConnection->sDbDatabase) or die("Error " . mysqli_error($link));

if ($mysqli->connect_error) {
    die("Erreur de connexion (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

// Get matching for that game
$aResults	= array();
$sQuery		= "SELECT participant_id FROM $table_matching WHERE user_token = '$sUserToken' AND game_id = '$iGameId'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($db_participant_id);
	while($stmt->fetch()) {
		$aResults[] = $db_participant_id;
	}
	$stmt->close();
}

echo implode(',', $aResults);

$mysqli->close();

?>