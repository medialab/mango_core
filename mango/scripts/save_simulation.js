// Added in startpage.pstpl

function save_simulation(gameName) {
	var token = $('#token').val();
	$.ajax({
		type: 'POST',
		url: rooturl + 'mango/save_simulation.php',
		data: 'token=' + token + '&gameName=' + gameName,
		async: false
	});
}