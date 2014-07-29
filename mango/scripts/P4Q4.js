// Added in survey 'Questionnaire Elève COLLEGE'
// Question Group 'Partie IV. Décris-toi!'
// Question 4

$(document).ready(
	function() {
		$('#question3588 .answers ul').attr('id', 'sortable');
		// $('#question3588 input[id^='answer22212X1498X3588']').attr('readonly', true);
		$('#question3588 input[id^='answerFIELDNAME']').attr('readonly', true);
		// $('#question3588 #answer22212X1498X35881').val('Ta famille');
		$('#question3588 #answerFIELDNAME1').val('Ta famille');
		// $('#question3588 #answer22212X1498X35882').val('L'école');
		$('#question3588 #answerFIELDNAME2').val('L\'école');
		// $('#question3588 #answer22212X1498X35883').val('Ton envie d'y arriver');
		$('#question3588 #answerFIELDNAME3').val('Ton envie d\'y arriver');
		// $('#question3588 #answer22212X1498X35884').val('L'argent');
		$('#question3588 #answerFIELDNAME4').val('L\'argent');
		// $('#question3588 #answer22212X1498X35885').val('Ton sport ou ton activité culturelle');
		$('#question3588 #answerFIELDNAME5').val('Ton sport ou ton activité culturelle');
		// $('#question3588 #answer22212X1498X35886').val('Tes copains');
		$('#question3588 #answerFIELDNAME6').val('Tes copains');
		// $('#question3588 #answer22212X1498X35887').val('L'amour');
		$('#question3588 #answerFIELDNAME7').val('L\'amour');
		// $('#question3588 #answer22212X1498X35888').val('Tes efforts');
		$('#question3588 #answerFIELDNAME8').val('Tes efforts');
		// $('#question3588 #answer22212X1498X35889').val('Ton talent');
		$('#question3588 #answerFIELDNAME9').val('Ton talent');
		$('#question3588 #sortable').sortable({
			update: function(event, ui) {
				// $("#question3588 label[for^='answer22212X1498X3588']").each(
				$("#question3588 label[for^='answerFIELDNAME']").each(
					function(index) {
						$(this).text(index + 1 + ' : ');
					}
				);
			}
		}).disableSelection();
	}
);