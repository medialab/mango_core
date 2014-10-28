<?php

require_once("lang/lang.php");

/********** VARIABLES **********/

$iExperimentId	= $_GET["experiment"];
$sLang			= $_GET["lang"];
$oFile			= "lang.xml";
$oTranslator	= new translator($sLang, $oFile);

?>
<html>
	<head>
		<meta charset="utf-8" />
		<title><?php echo $oTranslator->login ?></title>
		<link rel="stylesheet" href="../upload/templates/mango/template.css" />
		<script type="text/javascript" src="../third_party/jquery/jquery-1.10.2.min.js"></script>
		<script type="text/javascript">
			function filter(word) {
				// do the same thing that preg_replace(‘/[^_a-z0-9-]/i’, ”, word)
				return word.replace(/[^_a-z0-9-]/gi, "");
			}
			
			function submitForm() {
				var sToken = filter(document.form.token.value);
				var sLang = "<?php echo $sLang ?>";
				
				$.ajax({
					type: "POST",
					url: "redirect.php",
					data: "token=" + sToken + "&experiment=" + <?php echo $iExperimentId ?>,
					success: function(data) {
						switch(data) {
							case "wrong_login" :
								sUrl = "/limesurvey/services/error_01.php?lang=" + sLang;
								break;
							case "quota_exceeded" :
								sUrl = "/limesurvey/services/error_0.php?lang=" + sLang;
								break;
							default :
								sUrl = "/limesurvey/index.php?token=" + sToken + "&sid=" + data + "&lang=" + sLang;
								break;
						}
						window.location.replace(sUrl);
					}
				});
			}
			
			$(document).keypress(
				function(event) {
					if (event.keyCode == "13") {
						return false;
					}
				}
			);
		</script>
	</head>
	<body>
		<div id="top">		</div>
		<div id="content">
			<div id="left"> 
				<h1><?php echo $oTranslator->login ?></h1>
				<div class="reminder"></div>
			</div>
			<div id="right">
				<div id="wrapper" class="login-form">
					<p id="tokenmessage"><?php echo $oTranslator->login_text ?><br/></p>
					<form name="form" role="form">
						<div class="form-group">
							<label>Login</label><input name="token" type="text" />
						</div>
						<div class="form-group">
							<label>Password</label><input name="token" type="text" />
						</div>
						<div class="form-group">
							<input id="endbutton" class="submit" type="button" onclick="submitForm();" value="<?php echo $oTranslator->login_button ?>" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>