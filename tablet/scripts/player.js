// Added in startpage.pstpl

var playing = 0;

function play(soundName) {
	playing = 1;
	document.getElementById('sound').innerHTML = '<embed src="/upload/surveys/24557/sons/' + soundName + '" hidden=true autostart=true loop=false>';
}

function stop() {
	playing = 0;
	document.getElementById('sound').innerHTML = '';
}

function playOrStop(soundName) {
	if(playing) {
		stop();
	} else {
		play(soundName);
	}
}