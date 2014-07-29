// Added in startpage.pstpl

function save_answer(answer) {
	var token = $('#token').val();
	var questionId = $('h1').html().toLowerCase().replace(/ /g, '').replace('é', 'e').replace('-', '');
	$.ajax({
		type: 'POST',
		url: rooturl + '/mango/save_answer.php',
		data: 'token=' + token + '&sid=' + sid + '&questionId=' + questionId + '&answer=' + answer,
		async: false
	});
}

$(document).ready(
	function(){
		// ML Surveys
		var surveys = ['28197', '54495'];
		var sid = $('#sid').val();
		if(surveys.indexOf(sid) != -1) {
			$('#cta_contribute').click(
				function() {
					save_answer($('#player0_money_amount').val());
				}
			);
		}
	}
);