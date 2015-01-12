// Added in startpage.pstpl

function slider_error() {
	$('div.slider_callout').each(
		function() {
			if($(this).html() == '') {
				if(!$(this).parents('div[id^="question"]').attr('style')) {
					alert('This question is mandatory: if you want to select zero, please click on the zero.');
					document.limesurvey.move.value = '';
					document.limesurvey.submit();
				}
			}
		}
	);
}

$(document).ready(
	function() {
		$('#movenextbtn').click(
			function() {
				slider_error();
			}
		);
		$("input[accesskey='l']").click(
			function() {
				slider_error();
			}
		);
	}
);