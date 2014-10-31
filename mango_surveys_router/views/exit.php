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
		    		<?php echo $sTranslator->exit_message ?>
		    	</div>
		    </div>
		</div>
	</body>
</html>