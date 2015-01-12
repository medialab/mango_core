function goout() {
	$('#movesubmitbtn').click();
	$('#defaultbtn').click();
}

$(document).ready(
	function() {
		var iSid			= $('#sid').val();
		// Coop - Introduction Session 01, Coop - Introduction Session 02, Coop - Jeu Catégorisation Peur/Colère, Coop - Jeu Dictator, Coop - Jeu Preferences, Coop - Jeu Public Good, Coop - Jeu Trust, Coop - Questionnaire Anxiété, Coop - Questionnaire Autisme, Coop - Questionnaire Bien Etre, Coop - Questionnaire Relations Sociales, Coop - Questionnaire Traits d'histoire de vie
		var aCoopSurveys	= ['232525', '233138', '867323', '336985', '948898', '876211', '914824', '778262', '271959', '776855', '984752', '718757'];
		// GC - Introduction, GC - Jeu Discounting, GC - Jeu Labyrinthe, GC - Jeu Meta Cognition, GC - Jeu Motivation Sociale, GC - Questionnaire Auto-efficacité, GC - Questionnaire Bien-être, GC - Questionnaire Echelle de tenacité, GC - Questionnaire Facteurs de réussite, GC - Questionnaire Prévoyance, GC - Questionnaire Réalisme
		var aGcSurveys		= ['115247', '537887', '969373', '639163', '982922', '499881', '937529', '448264', '899228', '519196', '158295'];
		// NRJ
		var aNRJSurveys		= ['173395'];
		// For survey 718757, Coop - Questionnaire Traits d'histoire de vie, hide Question Help
		if((aCoopSurveys.indexOf(iSid) != -1) || (aGcSurveys.indexOf(iSid) != -1) || (aNRJSurveys.indexOf(iSid) != -1)) {
			$('#movesubmitbtn').text('Suivant');
			if((aGcSurveys.indexOf(iSid) != -1) || (aNRJSurveys.indexOf(iSid) != -1)) {
				// Hide all question help messages
				$('.questionhelp').hide();
			} else {
				$('.question input.numeric').keyup(
					function(event) {
						// If a number is entered, hide the questionhelp message
						if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || (event.which == 16)) {
							$(this).parents('.question').siblings('.questionhelp').hide();
						// If a string is entered, display the current question help message
						} else {
							$(this).parents('.question').siblings('.questionhelp').show();
						}
					}
				);
			}
		}
	}
);