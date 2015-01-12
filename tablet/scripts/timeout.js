// Added in startpage.pstpl

// IE doesn't support the indexOf() function -> Fix it
if(!Array.indexOf) {
	Array.prototype.indexOf = function(obj) {
		for(var i = 0; i < this.length; i++) {
			if(this[i] == obj) {
				return i;
			}
		}
		return -1;
	}
}

var delay = 300000;

var surveys = [];
// enar1.1, enar1.2, enar2.1, enar2.2, enbr1.0, enbr2.0
surveys = surveys.concat(['57263', '23686', '91237', '45578', '56115', '85626']);
// Survey 1, Survey 2, Survey 3, Survey 4
surveys = surveys.concat(['22294', '93583', '48118', '81365']);
// grip
surveys = surveys.concat(['36463']);

// Return the correct warning message according to the language
function getMessage(lang) {
	if(lang == 'en') {
		return 'You have been inactive for more than 5 minutes. You can, however, take as much time as you need to think about your decisions.';
	} else if(lang == 'fr') {
		return 'Nous vous informons que avez été inactif pendant plus de 5 minutes. Vous pouvez cependant prendre le temps que vous voulez pour réfléchir à vos décisions.';
	} else {
		return 'This is an error.';
	}
}

// Convert the numbers of 1 digit to 2 digits
function twoDigits(number) {
	if(number < 10) {
		return '0' + number;
	} else {
		return number;
	}
}

// The timeout is the inactivity period of the user
function startTimeout() {
	document.onkeydown = resetTimeout;
	document.onmousemove = resetTimeout;
	document.onmousedown = resetTimeout;
	timeout = setTimeout('stopTimeout();startStopwatch();', delay);
}

function resetTimeout() {
	clearTimeout(timeout);
	timeout = setTimeout('stopTimeout();startStopwatch();', delay);
}

function stopTimeout() {
	clearTimeout(timeout);
	document.onkeydown = null;
	document.onmousemove = null;
	document.onmousedown = null;
	$('#dialog').dialog('open');
}

// The stopwatch is the delay the user takes to clic on ok
function startStopwatch() {
	var responsedelay = 0;
	stopwatch = setInterval('responsedelay++;', 1000);
}

function stopStopwatch() {
	var myDate = new Date();
	var formatDate = '' + myDate.getFullYear() + twoDigits(myDate.getMonth() + 1) + twoDigits(myDate.getDate()) + twoDigits(myDate.getHours()) + twoDigits(myDate.getMinutes()) + twoDigits(myDate.getSeconds());
	clearInterval(stopwatch);
	$.ajax({
		type: 'POST',
		url: rooturl + '/mango/save_timeout.php',
		data: 'token=' + token + '&sid=' + sid + '&gid=' + gid + '&date=' + formatDate + '&timeout=' + responsedelay,
		async: false
	});
}

$(document).ready(
	function() {
		var sid = $('#sid').val();
		var token = $('#token').val();
		var lang = $('html').attr('lang');
		var fieldnames = $('#fieldnames').val();
		if((surveys.indexOf(sid) != -1) && (fieldnames != null)) {
			var gid = fieldnames.split('X')[1];
			$('div.navigator').append('<div id="dialog"><p>' + getMessage(lang) + '</p><button type="button">OK</button></div>');
			startTimeout();
			$('#dialog').dialog({
				bgiframe: true, autoOpen: false, modal: true, closeOnEscape: false
			});
			$('#dialog button').click(
				function() {
					$('#dialog').dialog('close');
					stopStopwatch();
					startTimeout();
				}
			);
			$('div[class^="ui-dialog-titlebar"]').remove();
		}
	}
);