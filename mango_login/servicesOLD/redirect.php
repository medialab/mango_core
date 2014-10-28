<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 * Called by mango/mango_login/services/login.php
 **/

/********** VARIABLES **********/

// Post variables
$sToken					= $_POST["token"];
$iExperimentId			= $_POST["experiment"];

// DB Connection
$oParams				= json_decode(file_get_contents('../../config.json'));
$sTableUsers			= 'mango_users';


/********** FUNCTIONS **********/

/********** PROGRAM **********/

# DB connection
$mysqli	= mysqli_connect($oParams->sDbHost, $oParams->sDbUser, $oParams->sDbPassword, $oParams->sDbDatabase) or die("Error " . mysqli_error($link));

if ($mysqli->connect_error) {
    die("Erreur de connexion (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

# Set charset to utf-8
$mysqli->set_charset("utf8");

# check if the user is in the users base
/*
$query = "SELECT password FROM $sTableUsers WHERE login = '$sToken' AND experiment_id = $iExperimentId";
if ($stmt = $mysqli->prepare($query)) {
	$stmt->execute();
	$stmt->bind_result($token, $user_group);
	$stmt->fetch();
	$stmt->close();
}
*/

echo password_hash("rasmuslerdorf", PASSWORD_DEFAULT)."\n";

// echo $user_survey;

$mysqli->close();

?>