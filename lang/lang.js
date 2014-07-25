function translator(sElementId, sLang) {
	console.log(oLang);
	return oLang.find('#' + sElementId + ' translation[lang="' + sLang + '"]').text();
}

$(document).ready(function() {
	$.get('../mango_surveys_router/lang.xml', function(data) {
		oLang = $(data);
	});
});