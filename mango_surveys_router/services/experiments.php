<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 */

require_once('experiment.php');

$oExperimentClass = new Experiment();

$sHtml = '<html>';
$sHtml .= '<head>';
$sHtml .= '<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />';
$sHtml .= '<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>';
$sHtml .= '<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>';
$sHtml .= '<script type="text/javascript" src="../scripts/experiments.js"></script>';
$sHtml .= '<script type="text/javascript" src="../../lang/lang.js"></script>';
$sHtml .= '<link rel="stylesheet" type="text/css" href="../css/experiments.css" />';
$sHtml .= '<meta charset="utf-8" />';
$sHtml .= '</head>';
$sHtml .= '<body>';
$sHtml .= '<div class="container">';
$sHtml .= '<div class="row"><h1>Experiment form</h1></div>';
$sHtml .= '<div class="row"><div class="messages col-sm-9"></div></div>';
$sHtml .= '<div class="row">';
$sHtml .= '<form class="form-horizontal form-experiment" role="form">';

// Experiments list
$sHtml .= '<div class="form-group list-experiments">';
$sHtml .= '<label class="col-sm-2 control-label">Experiment</label>';
$sHtml .= '<div class="col-sm-5">';
$sHtml .= '<select class="form-control">';
$sHtml .= '<option value="0"></option>';
$aExperiments = $oExperimentClass->getAllExperiments();
foreach($aExperiments as $aExperiment) {
	$sHtml .= '<option value="' . $aExperiment['id'] . '">' . $aExperiment['name'] . '</option>';
}
$sHtml .= '</select>';
$sHtml .= '</div>';
$sHtml .= '</div>';

// Experiment Id
$sHtml .= '<div class="form-group">';
$sHtml .= '<label class="col-sm-2 control-label">Id</label>';
$sHtml .= '<div class="col-sm-5">';
$sHtml .= '<input type="text" class="form-control experiment-id" value="" disabled>';
$sHtml .= '</div>';
$sHtml .= '</div>';

// Experiment name
$sHtml .= '<div class="form-group experiment-name">';
$sHtml .= '<label class="col-sm-2 control-label">Name</label>';
$sHtml .= '<div class="col-sm-5">';
$sHtml .= '<input type="text" class="form-control" placeholder="Enter the experiment name"></input>';
$sHtml .= '</div>';
$sHtml .= '</div>';

// Experiment parameters
$sHtml .= '<div class="form-group">';
$sHtml .= '<div class="col-sm-5 col-sm-offset-2">';
$sHtml .= '<input type="checkbox" class="login-phase" />Phase de login<br/>';
$sHtml .= '<input type="checkbox" class="results-phase" />Phase de résultats';
$sHtml .= '</div>';
$sHtml .= '</div>';

// Games list
$aGames = $oExperimentClass->getAllGames();
$sHtml .= '<div class="games sortable">';
$sHtml .= '<div class="form-group list-games" index="0">';
$sHtml .= '<label for="" class="col-sm-2 control-label">Game 1</label>';
$sHtml .= '<div class="col-sm-5">';
$sHtml .= '<select class="form-control">';
$sHtml .= '<option value="0"></option>';
foreach($aGames as $iKey => $aGame) {
	$sHtml .= '<option value="' . $aGame['surveyls_survey_id'] . '">' . utf8_encode($aGame['surveyls_title']) . '</option>';
}
$sHtml .= '</select>';
$sHtml .= '</div>';
$sHtml .= '<div class="col-sm-2 buttons-game">';
$sHtml .= '<button type="button" class="btn btn-default btn-lg delete-game">';
$sHtml .= '<span class="glyphicon glyphicon-minus-sign"></span>';
$sHtml .= '</button>';
$sHtml .= '<button type="button" class="btn btn-default btn-lg add-game">';
$sHtml .= '<span class="glyphicon glyphicon-plus-sign"></span>';
$sHtml .= '</button>';
$sHtml .= '</div>';
$sHtml .= '</div>';
$sHtml .= '</div>';

// Buttons
$sHtml .= '<div class="col-sm-7 text-center buttons">';
$sHtml .= '<button type="button" class="btn btn-primary btn-save">Add experiment</button>';
$sHtml .= '</div>';

$sHtml .= '</form>';
$sHtml .= '</div>';
$sHtml .= '</div>';
$sHtml .= '</body>';
$sHtml .= '</html>';

echo $sHtml;

?>