<?php

/***
 * Router developped for Amazon Mechanical Turk
 * The first user to connect will have the token 1, the second one the 2 ...
 ***/

// MTurk - Jeu Catégorisation Peur/Colère
$iSurveyId	= 352789;
// It have to be in english
$sLang		= 'en';

// DB connection
$oDBConnection			= json_decode(file_get_contents('config.json'));
$mysqli 				= mysqli_connect($oDBConnection->sDbHost, $oDBConnection->sDbUser, $oDBConnection->sDbPassword, $oDBConnection->sDbDatabase) or die("Error " . mysqli_error($link));

// Get the next token
$sQuery = "SELECT mturk_login_iterator FROM mango_constants";
if ($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iMturkIterator);
	$stmt->fetch();
	$stmt->close();
}

// Create token into database
$query = "INSERT INTO lime_tokens_$iSurveyId (firstname, emailstatus, token, language, sent, remindersent, remindercount, completed, validfrom, validuntil, mpid) VALUES ('$iMturkIterator', 'OK', '$iMturkIterator', '$sLang', 'N', 'N', 0, 'N', NULL, NULL, NULL)";
if($stmt = $mysqli->prepare($query)) {
	$stmt->execute();
	$stmt->close();
}

// Increment the next token
$iMturkNextIterator = $iMturkIterator + 1;
$sQuery = "UPDATE mango_constants SET mturk_login_iterator = '$iMturkNextIterator'";
if ($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->close();
}

// Redirect to the survey with the correct token
$sRootUrl	= "http://{$_SERVER['HTTP_X_FORWARDED_HOST']}/";
$sUrl 		= $sRootUrl . "index.php?r=survey/index/sid/$iSurveyId/lang/$sLang/token/$iMturkIterator";
header("Location: {$sUrl}");

$mysqli->close();

?>