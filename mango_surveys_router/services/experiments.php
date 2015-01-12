<?php

/**
 * @author Anne L'Hôte <anne.lhote@gmail.com>
 */

require_once('experiment.php');

$oExperimentClass = new Experiment();

?>

<html>
	<head>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../scripts/experiments.js"></script>
		<script type="text/javascript" src="../../lang/lang.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/experiments.css" />
		<meta charset="utf-8" />
	</head>
	<body>
		<div class="container">
			<div class="row"><h1>Experiment form</h1></div>
			<div class="row"><div class="messages col-sm-9"></div></div>
			<div class="row">
				<form class="form-horizontal form-experiment" role="form">
					<div class="form-group list-experiments">
						<label class="col-sm-2 control-label">Experiment</label>
						<div class="col-sm-5">
							<select class="form-control">
								<option value="0"></option>
								<?php
									$aExperiments = $oExperimentClass->getAllExperiments();
									foreach($aExperiments as $aExperiment) {
										echo '<option value="' . $aExperiment['id'] . '">' . $aExperiment['name'] . '</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Id</label>
						<div class="col-sm-5">
							<p class="form-control-static experiment-id"></p>
						</div>
					</div>
					<div class="form-group experiment-name">
						<label class="col-sm-2 control-label">Name</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" placeholder="Enter the experiment name"></input>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-5 col-sm-offset-2">
							<input type="checkbox" class="login-phase" />Login phase<br />
							<input type="checkbox" class="results-phase" />Results phase<br />
							<input type="checkbox" class="generate-tokens" />On the fly token creation<br />
							<input type="checkbox" class="is-over" />Is over
						</div>
					</div>
					<?php
						$aGames = $oExperimentClass->getAllGames();
					?>
					<div class="games sortable">
						<div class="form-group list-games" index="0">
							<label for="" class="col-sm-2 control-label">Game 1</label>
							<div class="col-sm-5">
								<select class="form-control">
									<option value="0"></option>
									<?php
										foreach($aGames as $iKey => $aGame) {
											echo '<option value="' . $aGame['surveyls_survey_id'] . '">' . utf8_encode($aGame['surveyls_title']) . '</option>';
										}
									?>
								</select>
							</div>
							<div class="col-sm-2 buttons-game">
								<button type="button" class="btn btn-default btn-lg delete-game">
									<span class="glyphicon glyphicon-minus-sign"></span>
								</button>
								<button type="button" class="btn btn-default btn-lg add-game">
									<span class="glyphicon glyphicon-plus-sign"></span>
								</button>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12 buttons">
							<button type="button" class="btn btn-primary btn-save">Add experiment</button>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-5 col-sm-offset-2">
							<a class="experiment-url hide" href="" target="_blank">Test url</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>