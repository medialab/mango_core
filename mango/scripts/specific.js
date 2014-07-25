$(document).ready(
	function(){
		var iSid = $('#sid').val();
		// Coop - Introduction, Coop - Jeu catégorisation Peur/Colère, Coop - Jeu Preferences, Coop - Jeu Public Good, Coop - Questionnaire Anxiété, Coop - Questionnaire Autisme, Coop - Questionnaire Bien Etre, Coop - Questionnaire Relations Sociales, Coop - Questionnaire Traits d'histoire de vie
		var aCoopSurveys = ['232525', '867323', '948898', '876211', '778262', '271959', '776855', '984752', '718757'];
		// For survey 718757, Coop - Questionnaire Traits d'histoire de vie, hide Question Help
		if(aCoopSurveys.indexOf(iSid) != -1) {
			$('.questionhelp').hide();
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
		if(aCoopSurveys.indexOf(iSid) != -1) {
			$('#movesubmitbtn .ui-button-text').text('Suivant');
		}
	}
);