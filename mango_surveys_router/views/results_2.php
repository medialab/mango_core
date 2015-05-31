<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 * 
 **/

require_once('../../lang/lang.php');


/********** VARIABLES **********/

// List of games played
$aGames			= array(1, 2, 4);
$iEndowment		= 10;
$sTableEarning	= 'mango_earning';
$iExperimentId	= 2;

// Session variables
$sToken			= $_GET['token'];
$sLang			= $_GET['lang'];
$translator		= new translator($sLang, "../lang/lang.xml");
$aGamesOrder	= array(2, 4, 1);
$oDBConnection	= json_decode(file_get_contents('../../config.json'));

/********** PROGRAM **********/

// DB connection
$mysqli 		= mysqli_connect($oDBConnection->sDbHost, $oDBConnection->sDbUser, $oDBConnection->sDbPassword, $oDBConnection->sDbDatabase) or die("Error " . mysqli_error($link));

// Choose randomly one game
$iTmp 			= array_rand($aGames);
$iChosenGame	= $aGames[$iTmp];

// Get matching
$aMatching		= array();
$sQuery			= "SELECT game_id, participant_token FROM `mango_matching` WHERE user_token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iGameId, $sParticipantToken);
	while($stmt->fetch()) {
		$aMatching[$iGameId][$sParticipantToken] = 0;
	}
	$stmt->close();	
}

// Calculate score for the public good game, iGameId = 1
$iTotal1		= 0;
$sQuery 		= "SELECT 876211X2131X74641 FROM lime_survey_876211 WHERE token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iParticipantContribution1);
	$stmt->fetch();
	$iTotal1 += (int) $iParticipantContribution1;
	$stmt->close();
}

$sSubQuery		= "SELECT token, 57263X771X15241 AS answer FROM lime_survey_57263_20111220";
$sSubQuery		.= " UNION SELECT token, 23686X1409X31401 AS answer FROM lime_survey_23686_20111220";
$sSubQuery		.= " UNION SELECT token, 91237X840X17021 AS answer FROM lime_survey_91237_20111220";
$sSubQuery		.= " UNION SELECT token, 45578X1438X31961 AS answer FROM lime_survey_45578_20111220";
$sSubQuery		.= " UNION SELECT token, 56115X797X15811 AS answer FROM lime_survey_56115_20111220";
$sSubQuery		.= " UNION SELECT token, 85626X894X18221 AS answer FROM lime_survey_85626_20111220";
$sQuery 		= "SELECT t.token, t.answer FROM ($sSubQuery) AS t WHERE t.token IN ('" . implode('\', \'', array_keys($aMatching[1])) . "')";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iDbToken, $iDbAnswer);
	while($stmt->fetch()) {
		$iTotal1 += (int) $iDbAnswer;
		$aMatching[1][$iDbToken] = $iDbAnswer;
	}
	$stmt->close();	
}

// Calculate score for dictator game, iGameId = 2
$sQuery 		= "SELECT 336985X2144X76941 FROM lime_survey_336985 WHERE token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iParticipantContribution2);
	$stmt->fetch();
	$stmt->close();
}

// Calculate score for the trust game, iGameId = 4
$sQuery 		= "SELECT 914824X2148X77031 FROM lime_survey_914824 WHERE token = '$sToken'";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iParticipantContribution4);
	$stmt->fetch();
	$stmt->close();
}

$sSubQuery		= "SELECT token, 56115X817X16241_" . (int) $iParticipantContribution4 . " AS answer FROM lime_survey_56115_20111220";
$sSubQuery		.= " UNION SELECT token, 85626X911X18511_" . (int) $iParticipantContribution4 . " AS answer FROM lime_survey_85626_20111220";
$sQuery 		= "SELECT t.answer FROM ($sSubQuery) AS t WHERE t.token IN (" . implode(', ', array_keys($aMatching[4])) . ")";
if($stmt = $mysqli->prepare($sQuery)) {
	$stmt->execute();
	$stmt->bind_result($iPartnerContribution4);
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
	$sQuery 		= "INSERT INTO $sTableEarning (token, experiment_id) VALUES ('$sToken', $iExperimentId)";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->bind_result($bExists);
		$stmt->fetch();
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
	$sQuery 		= "SELECT earning1, earning2, earning4, chosenGame, earning, red_cross_earning FROM $sTableEarning WHERE token = '$sToken' AND experiment_id = $iExperimentId";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->bind_result($iEarning1, $iEarning2, $iEarning4, $iChosenGame, $iEarning, $iRedCrossEarning);
		$stmt->fetch();
		$stmt->close();
	}
} else {
	$iEarning1			= number_format($iEndowment - (int) $iParticipantContribution1 + 0.4 * $iTotal1, 2);

	$iEarning2			= number_format($iEndowment - (int) $iParticipantContribution2, 2);

	$iEarning4			= number_format($iEndowment - (int) $iParticipantContribution4 + $iPartnerContribution4, 2);

	$iRedCrossEarning		= number_format(0, 2);

	switch($iChosenGame) {
		case 1 :
			$iEarning = $iEarning1;
			break;
		case 2 :
			$iEarning = $iEarning2;
			break;
		case 4 :
			$iEarning = $iEarning4;
			break;
	}

	// Save results into database
	$sQuery 		= "UPDATE $sTableEarning SET earning1 = $iEarning1, earning2 = $iEarning2, earning4 = $iEarning4, chosenGame = $iChosenGame, earning = $iEarning WHERE token = '$sToken' AND experiment_id = $iExperimentId";
	if($stmt = $mysqli->prepare($sQuery)) {
		$stmt->execute();
		$stmt->close();	
	}
}

$mysqli->close();

?>

<head>
	<title><?php echo $translator->results_title?></title>
	<link rel="stylesheet" href="../../../upload/templates/mango/template.css">
	<script type="text/javascript" src="../../../third_party/jquery/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">
		var regex = new RegExp("token=(.*)&");
		var token = regex.exec(window.location.href)[1];

		function numbers_only(myfield, e, dec) {
			if (window.event) {
			   key = window.event.keyCode;
			} else if (e) {
				key = e.which;
			} else {
				return true;
			}
			keychar = String.fromCharCode(key);
			if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27)) {
			   return true;
			} else if ((("0123456789.").indexOf(keychar) > -1)) {
				return true;
			} else {
				return false;
			}
		}
		
		function check_earning(value) {
			var regexp = /^\d{1,2}(\.\d{1,2})?$/;
			var validation = regexp.test(value);
			if(!validation) {
				alert("<?php echo $translator->results_error_1 ?>");
			} else {
				var total = parseFloat($("#my_earning").val());
				if ($("#red_cross_earning").length) {
					total += parseFloat($("#red_cross_earning").val());
				}
				if ($("#wikimedia_foundation_earning").length) {
					total += parseFloat($("#wikimedia_foundation_earning").val());
				}
				$("#total_earning").text(total.toFixed(2));
			}
		}

		function end() {
			var email_address = $("#email_address").text();
			var my_earning = $("#my_earning").val();
			var regexp = /^\d{1,2}(\.\d{1,2})?$/;
			var validation = regexp.test(my_earning);
			if(!validation) {
				alert("<?php echo $translator->results_error_1 ?>");
				return;
			}
			if ($("#red_cross_earning").length) {
				var red_cross_earning = $("#red_cross_earning").val();
			} else {
				var red_cross_earning = 0;
			}
			validation = regexp.test(red_cross_earning);
			if(!validation) {
				alert("<?php echo $translator->results_error_1 ?>");
				return;
			}
			if ($("#wikimedia_foundation_earning").length) {
				var wikimedia_foundation_earning = $("#wikimedia_foundation_earning").val();
			} else {
				var wikimedia_foundation_earning = 0;
			}
			validation = regexp.test(wikimedia_foundation_earning);
			if(!validation) {
				alert("<?php echo $translator->results_error_1 ?>");
				return;
			}
			var total = parseFloat($('.gain_final #total_earning').text());
			var final_earning = parseFloat($(".final_earning").text());
			if(!(total == final_earning)) {
				alert("<?php echo $translator->results_error_2 ?>");
				return;
			}
			$.ajax({
				type: "POST",
				url: "../../save_earnings.php",
				data: "token=" + token + "&email=" + email_address + "&my_earning=" + my_earning + "&red_cross_earning=" + red_cross_earning + "&wikimedia_foundation_earning=" + wikimedia_foundation_earning,
				async: false,
				complete: function() {
					window.location.replace('http://surveys.ipsosinteractive.com/mrIWeb/mrIWeb.dll?I.Project=S14008324&id=' + token + '&rewards=4&stat=complete');
				}
			});
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

# First Game
# Public Good Game
function print_game_1($translator, $iOrder, $iChosenGame, $aMatching, $iEndowment) {

	global $iParticipantContribution1;

	$iGameId = 1;

	if ($iChosenGame == $iGameId) {
		$sClass = "selected_game";
	} else {
		$sClass = "game";
	}

	$html = "<div class='$sClass'>
		<span class='titre_partie'>" . $translator->results_section . " $iOrder</span>";
	
	$iTotal = 0;
	$iTotal += $iParticipantContribution1;
	$html .= "<div class='montant'>
		<span class='valeur'> " . display_amount($iParticipantContribution1) . "</span>
		<span class='intitule'>" . $translator->results_game_1_text_2 . "</span>
		</div>";

	$html .= " <div class='clearer'>" . $translator->results_game_1_text_3 . "<br/></div> ";
	foreach(array_keys($aMatching[$iGameId]) as $iPartner) {
		$iContribution 	= $aMatching[$iGameId][$iPartner];
		$iTotal			+= $iContribution;
		$html 			.= "<div class='montant'>
			<span class='valeur'>" . display_amount($iContribution) . "</span>
			<span class='intitule'>.</span>
			</div>";
	}

	$commonProject = number_format(1.6 * $iTotal, 2);
	$personalReturn = number_format($commonProject / 4, 2);
	$earnings[$iGameId] = number_format($iEndowment - $iParticipantContribution1 + $personalReturn, 2);
	$html .= "<div class='montant'>
		<span class='valeur'>" . display_amount($iTotal) . "</span>
		<span class='intitule'>" . $translator->results_game_1_text_4 . " </span>
		</div>
		<div class='montant'>
		<span class='valeur'>" . display_amount($commonProject) . "</span>
		<span class='intitule'>" . $translator->results_game_1_text_5 . " </span>
		</div>
		<div class='montant'>
		<span class='valeur'>" . display_amount($personalReturn) . "</span>
		<span class='intitule'>" . $translator->results_game_1_text_6 . "</span>
		</div>
		<div class='montant_final'>
		<span class='valeur'>" . display_amount($earnings[$iGameId]) . "</span>
		<span class='intitule'>" . $translator->results_game_final_text . " </span>
		</div>
		</div>";
	
	return $html;
}

# Second Game
# Dictator Game
function print_game_2($translator, $iOrder, $iChosenGame, $aMatching, $iEndowment) {
	global $iParticipantContribution2, $iEarning2;

	$iGameId = 2;
	
	if ($iChosenGame == $iGameId) {
		$sClass = "selected_game";
	} else {
		$sClass = "game";
	}

	$html = "<div class='$sClass'>
		<span class='titre_partie'>" . $translator->results_section . " $iOrder</span>";

	$html .= "<div class='montant'>
		<span class='valeur'>" . display_amount($iParticipantContribution2) . "</span>
		<span class='intitule'>" . $translator->results_game_2_text_4 . "</span>
		</div>";
	
	$html .= "<div class='montant_final'>
		<span class='valeur'>" . display_amount($iEarning2) . "</span>
		<span class='intitule'>" . $translator->results_game_final_text . " </span>
		</div>
		</div><!--fin de div partie -->";
	
	return $html;
}

# Fourth Game
# Trust Game
function print_game_4($translator, $iOrder, $iChosenGame, $aMatching, $iEndowment) {
	global $iParticipantContribution4, $iPartnerContribution4, $iEarning4;

	$iGameId = 4;

	if ($iChosenGame == $iGameId) {
		$sClass = "selected_game";
	} else {
		$sClass = "game";
	}

	$html = "<div class='$sClass'>
		<span class='titre_partie'>" . $translator->results_section . " $iOrder</span>";

	$html .= "<div class='montant'>
		<span class='valeur'>" . display_amount($iParticipantContribution4) . "</span>
		<span class='intitule'>" . $translator->results_game_transfer . "</span>
		</div>";

	$html .= "<div class='montant'>
		<span class='valeur'>" . display_amount($iParticipantContribution4 * 3) . "</span>
		<span class='intitule'>" . $translator->results_game_4_text_1 . "</span>
		</div>";

	$html .= "<div class='montant'>
		<span class='valeur'>" . display_amount($iPartnerContribution4) . "</span>
		<span class='intitule'>" . $translator->results_game_4_text_2 . "</span>
		</div>";

	$html .= "<div class='montant_final'>
		<span class='valeur'>" . display_amount($iEarning4) . "</span>
		<span class='intitule'>" . $translator->results_game_final_text . " </span>
		</div>
		</div>";
	
	return $html;
}

# Display the chosen game
function print_chosen_game($translator, $iChosenGame, $aGamesOrder) {
	$html = '';

	$html = '<br /><div>' . $translator->results_game_sum_up_text_11 . ' ' . (array_search($iChosenGame, $aGamesOrder) + 1) . '.</div>';

	return $html;
}

# Final Sum Up
function print_sum_up($translator, $iEarning, $iRedCrossEarning) {
	$emailAddress = "";
	$iFinalEarning = number_format((int) $iEarning + (int) $iRedCrossEarning, 2);
	$iEarning = number_format($iEarning, 2);
	$iRedCrossEarning = number_format($iRedCrossEarning, 2);

	$html = "<div class='gain_final'>
		<span class='gain_partie'><span class='final_earning'>$iFinalEarning</span> €</span>";
	$html .= "<span class='titre_partie'>" . $translator->results_game_sum_up_text_1 . "</span>";

	$html .= "<br/><br/>" . $translator->results_game_sum_up_text_10;
	
	// Give part of the earnings in cash
	$html .= "<br/><br/><span style='float: right'>€ <input type='text' id='my_earning' size='5' maxlength='5' value='$iEarning' onKeyPress='return numbers_only(this, event)' onChange='check_earning(this.value)'/></span>";
	$html .= $translator->results_game_sum_up_text_9 . "<br/><br/>";
	
	// Transfer part of the earnings to the IRC
	$html .= "<span style='float: right'>€ <input type='text' id='red_cross_earning' size='5' maxlength='5' value='$iRedCrossEarning' onKeyPress='return numbers_only(this, event)' onChange='check_earning(this.value)'/></span>";
	$html .= $translator->results_game_sum_up_text_4 . " <a href='" . $translator->results_game_sum_up_text_5 . "' target='_blank'>" . $translator->results_game_sum_up_text_6 . "</a> " . $translator->results_game_sum_up_text_3 . ".</i><br/><br />";
	
	// Total
	$html .= "<hr align='right' width='20%'>";
	$html .= "<span style='float: right; text-align: center; width: 18%;'>Total : ";
	$html .= "<span id='total_earning'>$iFinalEarning</span> €";
	$html .= "</span><br />";

	$html .= "</div><br />";

	$html .= "<div class='error'>ATTENTION ! Merci de cliquer sur le bouton \"Terminer\" et d'attendre que la page Ipsos soit complètement chargée pour valider vos réponses (et récupérer votre gain le cas échéant).</div><br /><br />";
	
	return $html;
}

# Display managment according to the survey id
$iterator = 1;
foreach ($aGamesOrder as $iGameId) {
	$var = "print_game_$iGameId";
	echo $var($translator, $iterator, $iChosenGame, $aMatching, $iEndowment);
	$iterator++;
}

echo print_chosen_game($translator, $iChosenGame, $aGamesOrder);

echo print_sum_up($translator, $iEarning, $iRedCrossEarning);

echo "<input id='endbutton' type='button' value='" . $translator->results_button_finish . "' onclick='end();'/>";

?>

		</div>
	</div>
</body>
