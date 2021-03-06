﻿// Called by experiments.php

// Reset form with empty fields
function resetForm() {
	// Select the default empty experiment
	if($('.list-experiments select').val() == 0) {
		$('.list-experiments option').first().attr('selected', 'selected');
	}
	// Reset the experiment Id
	$('.experiment-id').text('');
	// Empty experiment name
	$('.experiment-name input').val('');
	// Uncheck experiment parameters
	$('.login-phase').prop('checked', 0);
	$('.results-phase').prop('checked', 0);
	$('.generate-tokens').prop('checked', 0);
	$('.is-over').prop('checked', 0);
	// Remove all but first games
	$('.list-games').slice(1).remove();
	// Select the default empty game
	$('.list-games option').first().attr('selected', 'selected');
	// Change button label
	$('.btn-save').text('Add experiment');
	// Remove cancel button
	$('.btn-cancel').remove();
	// Remove delete experiment button
	$('.btn-delete').remove();
	// Remove export buttons
	$('.btn-export-earnings').remove();
	$('.btn-export-matching').remove();
	$('.btn-export-simulation').remove();
	$('.btn-export-social-motivation').remove();
	$('.btn-export-labyrinth').remove();
	// Enable all buttons
	$('.buttons .btn').attr('disabled', false);
	// Reset the experiments list
	$('.list-experiments select > option').remove();
	// Hide the experiment url
	$('.experiment-url').addClass('hide');
	$.ajax({
		type: 'POST',
		url: 'get_all_experiments.php',
		success: function(data) {
			var oExperiments = jQuery.parseJSON(data);
			$('.list-experiments select').append('<option value="0"></option>');
			$.each(oExperiments, function(index, item) {
				$('.list-experiments select').append('<option value="' + item.id + '">' + item.name + '</option>');
			});
		}
	});
}

function addNewGame(iGameIndex) {
	// Get game template
	var oGameNew = $('.list-games').last().clone(true);
	// Set correct label and class
	iGameIndex = typeof iGameIndex !== 'undefined' ? iGameIndex : (parseInt(oGameNew.attr('index')) + 1);
	oGameNew.attr('index', iGameIndex);
	oGameNew.find('label').text('Game ' + (parseInt(iGameIndex) + 1));
	// Unselect default game
	oGameNew.find('option').removeAttr('selected');
	// Remove the add button
	oGameNew.find('.add-game').remove();
	// Add the delete button
	if(!oGameNew.find('.delete-game').length) {
		var sRemoveButton = '<button type="button" class="btn btn-default btn-lg delete-game">';
		sRemoveButton += '<span class="glyphicon glyphicon-minus-sign"></span>';
		sRemoveButton += '</button>';
		oGameNew.find('.buttons-game').prepend(sRemoveButton);
	}
	$('.list-games').last().after(oGameNew);
}

function refreshForm(event, ui) {
	// Replace add game button in front of the first game
	$('.buttons-game:first').append($('.add-game'));
	// Reset the game number
	$('.list-games').each(
		function(index) {
			$(this).attr('index', index);
			$(this).find('label').text('Game ' + (index + 1));
		}
	);
}

$(document).ready(
	function () {
		// Add sortable table
		$('.sortable').sortable({
			update : function(event, ui) {
				refreshForm(event, ui);
			}
		});
		// Load another experiment
		$('.list-experiments select').change(
			function() {
				var iExperimentId = $(this).val();
				resetForm();
				$.ajax({
					type: 'POST',
					url: 'get_experiment.php',
					data: {experiment_id: iExperimentId},
					success: function(data) {
						var oExperiment = jQuery.parseJSON(data);
						// Set experiment id
						$('.experiment-id').text(oExperiment.id);
						// Set experiment name
						$('.experiment-name input').val(oExperiment.name);
						// Set experiment parameters
						$('.login-phase').prop('checked', parseInt(oExperiment.login_phase));
						$('.results-phase').prop('checked', parseInt(oExperiment.results_phase));
						$('.generate-tokens').prop('checked', parseInt(oExperiment.generate_tokens));
						$('.is-over').prop('checked', parseInt(oExperiment.is_over));
						// Remove all but first games
						$('.list-games').slice(1).remove();
						$('.list-games option').first().attr('selected', 'selected');
						for(var i = 0; i < oExperiment.games.length; i++) {
							if(!$('.list-games[index="' + oExperiment.games[i].survey_order + '"] select').length) {
								addNewGame(oExperiment.games[i].survey_order);
							}
							$('.list-games[index="' + oExperiment.games[i].survey_order  + '"] select').val(oExperiment.games[i].survey_id);
						}
						// Change button label
						$('.btn-save').text('Update experiment');
						// Add cancel buttons
						$('.buttons').prepend('<button type="button" class="btn btn-primary btn-cancel">Cancel</button>');
						$('.buttons').append('<button type="button" class="btn btn-primary btn-delete">Delete experiment</button>');
						$('.buttons').append('<button type="button" class="btn btn-primary btn-export-earnings">Export earnings</button>');
						if(oExperiment.id == 2) {
							$('.buttons').append('<button type="button" class="btn btn-primary btn-export-matching">Export matching</button>');
							$('.buttons').append('<button type="button" class="btn btn-primary btn-export-simulation">Export simulation</button>');
						}
						if(oExperiment.id == 4) {
							$('.buttons').append('<button type="button" class="btn btn-primary btn-export-social-motivation">Export social motivation</button>');
							$('.buttons').append('<button type="button" class="btn btn-primary btn-export-labyrinth">Export labyrinth</button>');
						}
						// Set experiment url
						$('.experiment-url').removeClass('hide');
						$('.experiment-url').attr('href', oExperiment.url);
					}
				});
			}
		);

		// Add an new game into an experiment
		$('.add-game').click(
			function() {
				addNewGame();
			}
		);

		// Delete selected game
		$('.form-experiment').on('click', '.delete-game', function() {
			$(this).parents('.list-games').remove();
		});

		// Cancel current experiment
		$('.form-experiment').on('click', '.btn-cancel', function() {
			resetForm();
		});

		// Save or update experiment
		$('.form-experiment').on('click', '.btn-save',
			function() {
				// Disable interaction buttons
				$('.buttons .btn').attr('disabled', true);
				var iExperimentId = $('.experiment-id').text();
				var sExperimentName = $('.experiment-name input').val();
				var bExperimentLoginPhase = $('.login-phase').prop('checked');
				var bExperimentResultsPhase = $('.results-phase').prop('checked');
				var bExperimentGenerateTokens = $('.generate-tokens').prop('checked');
				var bExperimentIsOver = $('.is-over').prop('checked');
				var bGameError = false;
				var aExperimentGames = new Array();
				$('.list-games').each(function(index) {
					iSurveyId = $(this).find('select').val();
					aExperimentGames[parseInt($(this).attr('index'))] = iSurveyId;
					if(iSurveyId == '') bGameError = true;
				});
				if(sExperimentName == '') {
					$('.messages').append('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + translator('error_experiment_name', 'fr') + '</div>').delay(3000).slideUp(1000);
					$('.btn-save').removeAttr('disabled');
				} else if(bGameError) {
					$('.messages').append('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' + translator('error_game', 'fr') + '</div>').delay(300).slideUp(1000);
					$('.btn-save').removeAttr('disabled');
				} else {
					$.ajax({
						type: 'POST',
						url: 'save_experiment.php',
						data: {experiment_id: iExperimentId, experiment_name: sExperimentName, experiment_login_phase: bExperimentLoginPhase, experiment_results_phase: bExperimentResultsPhase, experiment_generate_tokens : bExperimentGenerateTokens, experiment_is_over : bExperimentIsOver, experiment_games: aExperimentGames},
						success: function() {
							// Enable interaction buttons
							resetForm();
							$('.messages').append('<div class="alert alert-success">Experiment added!</div>').delay(10000).slideUp(1000);
						}
					});
				}
			}
		);

		// Delete current experiment
		$('.form-experiment').on('click', '.btn-delete',
			function() {
				// Disable interactions buttons
				$('.buttons .btn').attr('disabled', true);
				// Get current experiment id
				var iExperimentId = $('.experiment-id').text();
				if(iExperimentId != '' && iExperimentId != 0) {
					$.ajax({
						type: 'POST',
						url: 'delete_experiment.php',
						data: {experiment_id: iExperimentId},
						success: function() {
							resetForm();
							$('.messages').append('<div class="alert alert-success">Experiment deleted!</div>').delay(10000).slideUp(1000);
						}
					});
				}
			}
		);

		// Export the earnings according to the experiment selected
		$('.form-experiment').on('click', '.btn-export-earnings',
			function() {
				// Disable interactions buttons
				$('.buttons .btn').attr('disabled', true);
				// Get current experiment id
				var iExperimentId = $('.experiment-id').text();
				if(iExperimentId != '' && iExperimentId != 0) {
					$.ajax({
						type: 'POST',
						url: 'export_earnings.php',
						data: {experiment_id: iExperimentId},
						success: function(data) {
							var sResult = jQuery.parseJSON(data);
							if(sResult.status == 'error') {
								$('.messages').append('<div class="alert alert-danger">' + sResult.message + '</div>').delay(10000).slideUp(1000);
							} else {
								// Download earnings file
								window.location.href = '../downloads/export_earning_' + iExperimentId + '.csv';
							}
							// Enable interaction buttons
							$('.buttons .btn').attr('disabled', false);
						}
					});
				}
			}
		);

		// Export the matchings according to the experiment selected
		$('.form-experiment').on('click', '.btn-export-matching',
			function() {
				// Disable interactions buttons
				$('.buttons .btn').attr('disabled', true);
				// Get current experiment id
				var iExperimentId = $('.experiment-id').text();
				if(iExperimentId != '' && iExperimentId != 0) {
					$.ajax({
						type: 'POST',
						url: 'export_matching.php',
						data: {experiment_id: iExperimentId},
						success: function(data) {
							var sResult = jQuery.parseJSON(data);
							if(sResult.status == 'error') {
								$('.messages').append('<div class="alert alert-danger">' + sResult.message + '</div>').delay(10000).slideUp(1000);
							} else {
								// Download earnings file
								window.location.href = '../downloads/export_matching_' + iExperimentId + '.csv';
							}
							// Enable interaction buttons
							$('.buttons .btn').attr('disabled', false);
						}
					});
				}
			}
		);

		// Export the simulation according to the experiment selected
		$('.form-experiment').on('click', '.btn-export-simulation',
			function() {
				// Disable interactions buttons
				$('.buttons .btn').attr('disabled', true);
				// Get current experiment id
				var iExperimentId = $('.experiment-id').text();
				if(iExperimentId != '' && iExperimentId != 0) {
					$.ajax({
						type: 'POST',
						url: 'export_simulation.php',
						data: {experiment_id: iExperimentId},
						success: function(data) {
							var sResult = jQuery.parseJSON(data);
							if(sResult.status == 'error') {
								$('.messages').append('<div class="alert alert-danger">' + sResult.message + '</div>').delay(10000).slideUp(1000);
							} else {
								// Download earnings file
								window.location.href = '../downloads/export_simulation_' + iExperimentId + '.csv';
							}
							// Enable interaction buttons
							$('.buttons .btn').attr('disabled', false);
						}
					});
				}
			}
		);

		// Export the social motivation results
		$('.form-experiment').on('click', '.btn-export-social-motivation',
			function() {
				// Disable interactions buttons
				$('.buttons .btn').attr('disabled', true);
				$.ajax({
					type: 'POST',
					url: 'export_social_motivation.php',
					success: function(data) {
						var sResult = jQuery.parseJSON(data);
						if(sResult.status == 'error') {
							$('.messages').append('<div class="alert alert-danger">' + sResult.message + '</div>').delay(10000).slideUp(1000);
						} else {
							// Download earnings file
							window.location.href = '../downloads/export_social_motivation.csv';
						}
						// Enable interaction buttons
						$('.buttons .btn').attr('disabled', false);
					}
				});
			}
		);

		// Export the labyrinth results
		$('.form-experiment').on('click', '.btn-export-labyrinth',
			function(e) {
				e.preventDefault();
				$('.experiment-url').after('<a href="../../mango_frustrating_task/files/answers.txt" class="to-click" download>Download</a>')
				$('.to-click').hide();
				$('.to-click')[0].click();
				$('.to-click').attr('href', '../../mango_frustrating_task/files/answers_event.txt');
				$('.to-click')[0].click();
				$('.to-click').attr('href', '../../mango_frustrating_task/files/answers_focus.txt');
				$('.to-click')[0].click();
				$('.to-click').attr('href', '../../mango_frustrating_task/files/answers_trace.txt');
				$('.to-click')[0].click();
			}
		);
	}
);