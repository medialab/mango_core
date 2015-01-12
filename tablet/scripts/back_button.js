// Added in startpage.pstpl

var bajb_backdetect = {
	FrameLoaded: 0,
	FrameTimeout: null,

	BAJBFrame: function() {
		if (bajb_backdetect.FrameLoaded > 1) {
			bajb_backdetect.OnBack();
		}
		bajb_backdetect.FrameLoaded++;
		bajb_backdetect.SetupFrames();
	},
	
	OnBack: function() {
		location.reload();
	},

	SetupFrames: function()	{
		clearTimeout(bajb_backdetect.FrameTimeout);
		var BBiFrame = document.getElementById('BAJBOnBack');
		var checkVar = BBiFrame.src.substr(-11, 11);

		if (bajb_backdetect.FrameLoaded == 1 && checkVar != "HistoryLoad") {
			BBiFrame.src = "blank.html?HistoryLoad";
		} else {
			if (checkVar != "HistoryLoad") {
				bajb_backdetect.FrameTimeout = setTimeout("bajb_backdetect.SetupFrames();", 700);
			}
		}
	},

	Initialise: function() {
		if (navigator.appName == "Microsoft Internet Explorer") {
			document.write('<iframe src="blank.html" style="display:none;" id="BAJBOnBack" onunload="alert(\'de\')" onload="bajb_backdetect.BAJBFrame();"></iframe>');
		}
	}
};

bajb_backdetect.Initialise();