// Added in startpage.pstpl

var timing_sid;
var timing_page;
var timing_token;
var timing_timing;
var timing_second = 0;

function record() {
	window.clearTimeout(timing_timing);
	if(rooturl && timing_token && timing_sid && timing_page) {
		$.ajax({
			type: 'POST',
			url: rooturl + '/mango/save_timing.php',
			data: 'token=' + timing_token + '&sid=' + timing_sid + '&page=' + timing_page + '&timing=' + timing_second,
			async: false
		});
	}
}

$(document).ready(
	function() {
		var timing_timing = window.setInterval(function() {timing_second++;}, 1000);
		var timing_sid = $('#sid').val();
		var timing_token = $('#token').val();
		var timing_page = $('h1').html();
		$('#movenextbtn').click(
			function(){
				record();
			}
		);
		$('#moveprevbtn').click(
			function(){
				record();
			}
		);
		$('input[accesskey="l"]').click(
			function(){
				record();
			}
		);
		$(document).keypress(
			function(event) {
				if (event.keyCode == '13') {
					record();
				}
			}
		);
	}
);