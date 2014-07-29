function translator(sElementId, sLang) {
	console.log(oLang);
	return oLang.find('#' + sElementId + ' translation[lang="' + sLang + '"]').text();
}

$(document).ready(function() {
	$.get('./../lang/lang.xml', function(data) {
		oLang = $(data);
	});
});