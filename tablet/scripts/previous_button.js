// Added in startpage.pstpl

$(document).ready(
	function() {
		// Grip Survey and ML Surveys
		var surveys = ['36463', '28197', '54495'];
		var sid = $('#sid').val();
		if(surveys.indexOf(sid) != -1) {
			$('#moveprevbtn').hide();
		}
	}
);