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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;