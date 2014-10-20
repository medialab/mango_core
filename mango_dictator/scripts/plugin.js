function getMovie() {
	return document['ExternalInterfaceExample'];
}

$(document).ready(
	function() {
		$('.start_animation').click(
			function() {
				$(this).hide();
				getMovie().launch();
			}
		);
	}
);