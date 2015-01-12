<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 *
 * Called by router.php
 */

require_once('../../lang/lang.php');

$sRooturl		= "http://{$_SERVER['SERVER_NAME']}";
$sLang			= $_GET['lang'];
$sLangFile		= '../../mango_surveys_router/lang/lang.xml';
$sTranslator	= new translator($sLang, $sLangFile);

?>

<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $sTranslator->exit_thank_you ?></title>
		<link rel="stylesheet" href="../../../upload/templates/mango/template.css">
		<script type="text/javascript" src="../../../../third_party/jquery/jquery-1.10.2.min.js"></script>
	</head>
	<body>
	    <div id="top"></div>
	    <div id="content">
		    <div id="left">
		        <h1><?php echo $sTranslator->exit_thank_you ?></h1>
		        <div class="reminder"></div>
		    </div>
		    <div id="right">
		    	<div class="game">
		    		<p>
			    		Cette enquête est maintenant terminée.
			    		Nous vous remercions encore une fois très chaleureusement d’y avoir participé :
			    		vos réponses nous aiderons à faire aboutir votre projet.
			    		Nous vous souhaitons une excellente continuation.</p>
		    		<p>
		    			Nous vous rappelons que toutes vos réponses seront traitées de manière strictement anonyme.
		    		</p>
		    		<p>
		    			N’hésitez pas à contacter Joyce Sultan (<a href="mailto:jsultan@povertyactionlab.org">jsultan@povertyactionlab.org</a> 
		    			ou par téléphone au  06.52.45.74.81) ou Alice Danon (<a href="mailto:jsultan@povertyactionlab.org">adanon@povertyactionlab.org</a>
		    			ou par téléphone au 07.82.69.35.82) pour toute question concernant ce projet.
		    		</p>
		    	</div>
		    </div>
		</div>
	</body>
</html>