<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 **/

require_once('../../lang/lang.php');


/********** VARIABLES **********/

// List of games played
// Discounting
$aGames			= array(8);
$sTableEarning	= 'mango_earning';
$iExperimentId	= 4;

// Session variables
$sToken			= $_GET['token'];
$sLang			= $_GET['lang'];
$translator		= new translator($sLang, "../lang/lang.xml");
$aGamesOrder	= array(8);
$oDBConnection	= json_decode(file_get_contents('../../config.json'));

/********** PROGRAM **********/
// DB connection
$mysqli 		= mysqli_connect($oDBConnection->sDbHost, $oDBConnection->sDbUser, $oDBConnection->sDbPassword, $oDBConnection->sDbDatabase) or die("Error " . mysqli_error($link));

// Choose randomly one game
$iTmp 			= array_rand($aGames);
$iChosenGame	= $aGames[$iTmp];

// Select earning for the discounting game, iGameId = 8
$sQuery 		= "SELECT 537887X2209X9133earning, 537887X2209X9133period FROM lime_survey_537887 WHERE token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iEarning8, $sPeriod8);
	$stmt->fetch();
	$stmt->close();
}

// Select email address
$sQuery 		= "SELECT email FROM lime_tokens_537887 WHERE token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($sEmail);
	$stmt->fetch();
	$stmt->close();
}

// Check if token is in earning table
$sQuery 		= "SELECT COUNT(token) FROM $sTableEarning WHERE token = '$sToken' AND experiment_id = $iExperimentId";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($bExists);
	$stmt->fetch();
	$stmt->close();
}

if(!$bExists) {
	// Add this token into earning table
	$sQuery 		= "INSERT INTO $sTableEarning (token, experiment_id, email) VALUES ('$sToken', $iExperimentId, '$sEmail')";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->close();
	}
}

// Check if earning is already calculated
$sQuery 		= "SELECT COUNT(chosenGame) FROM $sTableEarning WHERE token = '$sToken' AND NOT chosenGame IS NULL AND experiment_id = $iExperimentId";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($isDone);
	$stmt->fetch();
	$stmt->close();
}

if($isDone) {
	$sQuery 		= "SELECT earning8, chosenGame, earning FROM $sTableEarning WHERE token = '$sToken' AND experiment_id = $iExperimentId";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->bind_result($iEarning8, $iChosenGame, $iEarning);
		$stmt->fetch();
		$stmt->close();
	}
} else {
	$iChosenGame	= 8;
	$iEarning 		= $iEarning8;
	// Save results into database
	$sQuery 		= "UPDATE $sTableEarning SET earning8 = $iEarning8, chosenGame = $iChosenGame, earning = $iEarning, period8 = '$sPeriod8' WHERE token = '$sToken' AND experiment_id = $iExperimentId";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->close();	
	}
}

$mysqli->close();

?>

<!DOCTYPE html>
<html lang="<?php echo $sLang ?>">
	<head>
		<title><?php echo $translator->results_title?></title>
		<link rel="stylesheet" href="../../../upload/templates/mango/template.css">
		<script type="text/javascript" src="../../../third_party/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript">
			function end() {
				window.location.replace("exit_4.php?lang=<?php echo $sLang ?>");
			}
		</script>
	</head>
	<body>
	    <!--HAUT DE PAGE-->
	    <div id="top">
	    </div>
	    <!--CONTENU DE PAGE -->
	    <div id="content">
	    <!-- PARTIE GAUCHE -->
	    <div id="left">
	        <h1><?php echo $translator->results_title?></h1>
	        <div class="reminder"></div>
	    </div> <!-- fin de left -->
	    <div id="right">

<?php

/********** FUNCTIONS **********/

/*
 * Display an amount with its currency according $sLang
 * @param $amount the amount to display
 * @return String
 */
function display_amount($amount) {
	global $sLang;
	
	if($sLang == "en") {
		return "$ " . (int) $amount;
	} elseif($sLang == "fr") {
		return (int) $amount . " €";
	}
}

# Eighth Game
# Discounting Game
function print_game_8($translator, $iOrder, $iChosenGame) {
	global $iEarning8, $sPeriod8;

	$iGameId = 8;
	
	if ($iChosenGame == $iGameId) {
		$sClass = "selected_game";
	} else {
		$sClass = "game";
	}

	$html = "<div class='$sClass'>";
	
	$html .= "<div class='montant_final'>
		<span class='intitule'>Vous recevrez " . display_amount($iEarning8) . " " . strtolower($sPeriod8) . " via PayPal.</span>
		</div>
		</div><!--fin de div partie -->";
	
	return $html;
}

# Final Sum Up
function print_sum_up($translator, $iEarning, $sEmail) {
	$html = "<div class='gain_final'>";
	$html .= "<span>
			<u>
				<b>Note :<b>
			</u><br/>
			<p>
				Cette indemnisation vous sera envoyée via PayPal à l'adresse email suivante : <a href=\"mailto:$sEmail\">$sEmail</a>.
				Si cette adresse email n'est pas correcte ou n'est pas celle que vous consultez régulièrement,
				merci d'en communiquer une autre à Joyce Sultan ou Alice Danon dont les coordonnées sont ci-dessous :
				<ul class=\"puced\">
					<li>
						Joyce Sultan : <a href=\"mailto:jsultan@povertyactionlab.org\">jsultan@povertyactionlab.org</a> (06.52.45.74.81)
					</li>
					<li>
						Alice Danon : <a href=\"mailto:adanon@povertyactionlab.org\">adanon@povertyactionlab.org</a> (07.82.69.35.82)
					</li>
				</ul>
			</p>
			<p>
				Il n'est pas nécessaire d'avoir déjà un compte PayPal pour être payé(e).
			</p>
			<p>
				Vous recevrez un email vous annonçant le paiement de votre gain via PayPal.
				Si vous avez déjà un compte PayPal lié à l'adresse email que vous nous avez communiquée,
				votre compte sera directement crédité. Si vous n'avez pas encore de compte PayPal il vous
				sera proposé d'en créer un, afin de créditer votre compte du montant perçu. Quoi qu'il en
				soit, rien ne vous oblige à lier votre compte bancaire à votre compte PayPal. Dans ce cas,
				votre argent restera sur votre compte PayPal et vous pourrez donc le dépenser sur Internet.
			</p>
			<p>
				Vous trouverez en cliquant sur
				<a href=\"https://www.paypal.com/fr/webapps/helpcenter/helphub/topic/?topicID=GET_TO_KNOW_PAYPAL&parentID=PAYPAL_BASICS&m=BT\" target=\"_blank\">cette page</a>
				tous les éléments qui vous permettront de créer un compte PayPal.
			</p>
		</span>";
	
	$html .= "</div><br/><br/>";
	
	return $html;
}

?>

	<p>
		L'enquête est à présent terminée : nous vous remercions énormément pour votre participation !
	</p>
	<p>
		Vos réponses au questionnaire 5 ont déterminé le montant de votre indemnisation.
	</p>

<?php

# Display managment according to the survey id
$iterator = 1;
foreach ($aGamesOrder as $iGameId) {
	$var = "print_game_$iGameId";
	echo $var($translator, $iterator, $iChosenGame);
	$iterator++;
}
echo print_sum_up($translator, $iEarning, $sEmail);

echo "<input id='endbutton' type='button' value='" . $translator->results_button_finish . "' onclick='end();'/>";

?>

			</div>
		</div>
	</body>
</html>