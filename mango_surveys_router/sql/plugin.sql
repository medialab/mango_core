CREATE TABLE IF NOT EXISTS `mango_surveys_router` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `experiment_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `survey_order` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `mango_experiment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `login_phase` tinyint(1) NOT NULL DEFAULT 0,
  `results_phase` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `mango_matching` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_token` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `game_id` int(11) NOT NULL,
  `participant_token` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `experiment_id` int(11) NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `mango_earning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `experiment_id` int(11) DEFAULT NULL,
  `hasUsedCalculator1` tinyint(1) DEFAULT NULL,
  `hasUsedCalculator4` tinyint(1) DEFAULT NULL,
  `chosenDecision1` int(11) DEFAULT NULL,
  `earning1` float DEFAULT NULL,
  `earning2` float DEFAULT NULL,
  `chosenDecision4_1` int(11) DEFAULT NULL,
  `chosenDecision4_2` int(11) DEFAULT NULL,
  `chosenDecision4_3` int(11) DEFAULT NULL,
  `earning4` float DEFAULT NULL,
  `chosenDecision5` int(11) DEFAULT NULL,
  `chosenGame` int(11) DEFAULT NULL,
  `earning5` float DEFAULT NULL,
  `score6` int(11) DEFAULT NULL,
  `earning` float DEFAULT 0,
  `email` text COLLATE utf8_unicode_ci,
  `paied` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;