$(document).ready(
	function(){
		var token = $("#token").val();
		var sid = $("#sid").val();
		$.ajax({
			type: "GET",
			url: rooturl + "/limesurvey/services/mango_surveys_router/plugin.php",
			data: "token=" + token + "&sid=" + sid,
				success: function(data) {
					window.location.href = data;
				}
		});
	}
);