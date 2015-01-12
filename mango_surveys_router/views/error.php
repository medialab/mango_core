<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 *
 * Called by router.php
 */

require_once('../../lang/lang.php');

$sRooturl		= "http://{$_SERVER['SERVER_NAME']}";
$sLang			= $_GET['lang'];
$sError			= $_GET['error'];
$sLangFile		= '../../mango_surveys_router/lang/lang.xml';
$sTranslator	= new translator($sLang, $sLangFile);

?>

<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $sTranslator->error_title ?></title>
		<link rel="stylesheet" href="../../../upload/templates/mango/template.css">
		<script type="text/javascript" src="../../../../third_party/jquery/jquery-1.10.2.min.js"></script>
	</head>
	<body>
		<!-- Page top -->
		<div id="top">
		</div>

		<!-- Page content -->
		<div id="content">
			<!-- Left part -->
			<div id="left">
				<h1><?php echo $sTranslator->error_title ?></h1>
				<div class="reminder"></div>
			</div>
			<!-- Right part -->
			<div id="right">
				<div style="text-align:center;">
					<br /><br />
					<?php echo $sTranslator->$sError ?>
				</div>
			</div>
		</div>
	</body>
</html>