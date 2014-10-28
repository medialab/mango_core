<?php
# redirect_wikipedia.php
# Called by login.php

# TODO :
# Check user with some special caracter in their username!


/********** VARIABLES **********/

# Post parameters

$lang 					= $_POST["lang"];
$login					= $_POST["login"];
$username				= urldecode($_POST["username"]);
$group 					= $_POST["group"];
$duration				= $_POST["duration"];
$editcounts				= $_POST["editcounts"];
$last6monthseditcount	= $_POST["last6monthseditcount"];
$secretkey				= $_POST["secretkey"];

# DB variables

$db_host				= "localhost";
$db_user				= "limesurvey";
$db_password			= "Zp(WsqcV{@";
$db_db					= "limesurvey";

$table_tokens			= "lime_tokens_";
$table_earning			= "mango_earning";
$table_constants		= "mango_constants";
$table_users_surveys	= "lime_users_surveys";

// ERC 2010 enar1.1
$survey_1 = 57263;
// ERC 2010 enar1.2
$survey_2 = 23686;
// ERC 2010 enar2.1
$survey_3 = 91237;
// ERC 2010 enar2.2
$survey_4 = 45578;
// ERC 2010 enbr1.0
$survey_5 = 56115;
// ERC 2010 enbr2.0
$survey_6 = 85626;

# Quotas

$admins_quota = 650;
$youngs_quota = 200;
$olds_quota = 650;

$rooturl = "/limesurvey/";


/********** FUNCTIONS **********/

# filter the login to get the limesurvey token
function filter($string) {
	return preg_replace("/[^_a-z0-9-]/i", "", $string);
}

# Get the user group ("admins", "youngs" or "olds") according to the its wikipedia's user metrics
function get_user_group($group, $duration, $editcounts, $last6monthseditcount) {
	$groups = explode(",", $group);
	if(in_array("bot", $groups)) {
		return "";
	}
	if(in_array("sysop", $groups)) {
		return "admins";
	}
	if($duration <= 30) {
		return "youngs";
	}
	if($duration >= 180 and $editcounts >= 300 and $last6monthseditcount >= 20) {
		return "olds";
	}
}

# Generete our MD5 key
function generate_md5_key($login, $group, $duration, $editcounts, $last6monthseditcount) {
	$inputs_serialized = "42" . serialize(array("login" => intval($login), "group" => $group, "duration" => intval($duration), "editcounts" => intval($editcounts), "last6monthseditcount" => intval($last6monthseditcount)));
	return md5($inputs_serialized);
}

# Check if the participant_group's quota is not excedeed
function is_quota_exceeded($user_group) {
	global $mysqli, $survey_1, $survey_2, $survey_3, $survey_4, $survey_5, $survey_6, $table_users_surveys, $admins_quota, $youngs_quota, $olds_quota;

	// $subquery = "SELECT token FROM lime_survey_$survey_1";
	// $subquery .= " UNION SELECT token FROM lime_survey_$survey_2";
	// $subquery .= " UNION SELECT token FROM lime_survey_$survey_3";
	// $subquery .= " UNION SELECT token FROM lime_survey_$survey_4";
	// $subquery .= " UNION SELECT token FROM lime_survey_$survey_5";
	// $subquery .= " UNION SELECT token FROM lime_survey_$survey_6";
	// $query = "SELECT COUNT(*) FROM ($subquery) AS tmp, $table_users_surveys AS users WHERE tmp.token = users.token AND user_group='$user_group'";
	$query = "SELECT COUNT(DISTINCT $table_earning.token) FROM $table_earning, $table_users_surveys WHERE $table_earning.token = $table_users_surveys.token AND $table_users_surveys.user_group = '$user_group' AND $table_earning.earning IS NOT NULL";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();
	}
	if(($user_group == "admins" and $count < $admins_quota) or ($user_group == "youngs" and $count < $youngs_quota) or ($user_group == "olds" and $count < $olds_quota)) {
		return 0;
	} else {
		return 1;
	}
}

# Check if the user has already participated
function has_participated($token) {
	global $mysqli, $table_users_surveys;
	
	$query = "SELECT token FROM $table_users_surveys WHERE token = '$token'";
	if ($stmt = $mysqli->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($participated);
		$stmt->fetch();
		$stmt->close();
	}
	return $participated;
}

# If the user has already participated, get his survey id
function get_participated_survey($token) {
	global $mysqli, $survey_1, $survey_2, $survey_3, $survey_4, $survey_5, $survey_6, $table_tokens;
	
	foreach (range(1, 6) as $number) {
		$result = "";
		$s = ${"survey_" . $number};
		$query = "SELECT token FROM $table_tokens$s WHERE token = '$token'";
		if ($stmt = $mysqli->prepare($query)) {
			$stmt->execute();
			$stmt->bind_result($result);
			$stmt->fetch();
			$stmt->close();
		}
		if (!empty($result)) {
			$return = $s;
		}
	}
	return $return;
}

function get_next_survey($sid) {
	global $survey_1, $survey_2, $survey_3, $survey_4, $survey_5, $survey_6;

	$new_sid = 0;
	switch($sid) {
		case $survey_1 :
			$new_sid = $survey_3;
			break;
		case $survey_2 :
			$new_sid = $survey_4;
			break;
		case $survey_3 :
			$new_sid = $survey_2;
			break;
		case $survey_4 :
			$new_sid = $survey_1;
			break;
		case $survey_5 :
			$new_sid = $survey_6;
			break;
		case $survey_6 :
			$new_sid = $survey_5;
			break;
	}
	return $new_sid;
}


/********** PROGRAM **********/

# DB connection
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_db);

if ($mysqli->connect_error) {
    die("Erreur de connexion (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

$token = filter($login);
$user_group = get_user_group($group, $duration, $editcounts, $last6monthseditcount);
$key = generate_md5_key($login, $group, $duration, $editcounts, $last6monthseditcount);

if(empty($token) or empty($user_group) or $key != $secretkey) {
	$url = $rooturl . "services/error_01.php?lang=$lang";
} else {
	if(is_quota_exceeded($user_group)) {
		$url = $rooturl . "services/error_02.php?lang=$lang";
	} else {
		if(has_participated($token)) {
			$sid = get_participated_survey($token);
			$url = $rooturl . "index.php?token=$token&sid=$sid&lang=$lang";
		} else {
			# Get the user_role according to its user_group
			$query = "SELECT wikipedia_" . $user_group . "_role FROM $table_constants";
			if($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($user_role);
				$stmt->fetch();
				$stmt->close();
			}
			# Change the user_role in base to the next one
			switch($user_role) {
				case "A" :
					$new_user_role = "B";
					break;
				case "B" :
					$new_user_role = "A";
					break;
			}
			# Set the new_user_role
			$query = "UPDATE $table_constants SET wikipedia_" . $user_group . "_role = '$new_user_role'";
			if($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->close();
			}
		
			# Get the user_survey according to its user_group and its user_role
			$query = "SELECT wikipedia_" . $user_group . "_survey_" . $user_role . " FROM $table_constants";
			if($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($user_survey);
				$stmt->fetch();
				$stmt->close();
			}
			# Change the user_survey in base to the next one
			$new_user_survey = get_next_survey($user_survey);
			# Set the new_user_survey
			$query = "UPDATE $table_constants SET wikipedia_" . $user_group . "_survey_" . $user_role . " = '$new_user_survey'";
			if($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->close();
			}
			
			# Check if the user is in lime_tokens_survey
			$query = "SELECT token FROM lime_tokens_$user_survey WHERE token = '$token'";
			if ($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($user_token);
				$stmt->fetch();
				$stmt->close();
			}
			if(!$user_token){
				# Add token to the tokens table
				$query = "INSERT INTO lime_tokens_$user_survey (emailstatus, token, language, sent, remindersent, remindercount, completed, validfrom, validuntil, mpid) VALUES ('OK', '$token', 'en', 'N', 'N', 0, 'N', NULL, NULL, NULL)";
				if($stmt = $mysqli->prepare($query)) {
					$stmt->execute();
					$stmt->close();
				}
			}
			
			# Check if the user is in lime_users_surveys
			$query = "SELECT token FROM $table_users_surveys WHERE token = '$token'";
			if ($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($user_users_surveys);
				$stmt->fetch();
				$stmt->close();
			}
			if(!$user_users_surveys){
				# Add token to the user_survey tokens table
				$query = "INSERT INTO $table_users_surveys (token, login, username, user_group, lang, wikipedia_group, duration, editcounts, last6monthseditcount, secretkey) VALUES ('$token', '$login', '" . str_replace("'", "''", utf8_decode($username)) . "', '$user_group', '$lang', '$group', '$duration', '$editcounts', '$last6monthseditcount', '$secretkey')";
				if($stmt = $mysqli->prepare($query)) {
					$stmt->execute();
					$stmt->close();
				}
			}
			
			# Check if the user is in mango_earning
			$query = "SELECT token FROM $table_earning WHERE token = '$token'";
			if ($stmt = $mysqli->prepare($query)) {
				$stmt->execute();
				$stmt->bind_result($user_earning);
				$stmt->fetch();
				$stmt->close();
			}
			if(!$user_earning){
				# Add token to the earnings table
				$query = "INSERT INTO $table_earning (token, email) VALUES ('$token', '');";
				$query .= "INSERT INTO $table_earning (token, email) VALUES ('$token', 'donate@wikimedia.org');";
				$query .= "INSERT INTO $table_earning (token, email) VALUES ('$token', 'International Committee of the Red Cross');";
				$mysqli->multi_query($query);
			}
			
			$url = $rooturl . "index.php?sid=$user_survey&lang=$lang&token=$token";
		}
	}
}

$mysqli->close();
?>
<script type="text/javascript" src="/limesurvey/scripts/jquery/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(
		function() {
			window.location.href = "<?php echo $url ?>";
		}
	);
</script>