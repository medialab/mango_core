# Goal


# Installation

## Install the mango template
* Copy the mango template into your /limesurvey/upload/templates folder

## Install the mango router plugin
* If you have no mango folder into your limesurvey directory, create it
* Copy the config.json file into your /limesurvey/mango folder
* Edit config.json and set your own configuration. The value of key **sInstallFolder** should match the value of the path in the URL used to access the application (e.g. empty value if "http://locahost/", "limesurvey" if "http://localhost/limesurvey").
* Copy the lang folder into your /limesurvey/mango folder
* Copy the mango_surveys_router folder into your /limesurvey/mango folder

## Add your first mango survey
* Copy the mango_dictator folder it into your /limesurvey/mango folder
* Log in to your admin panel
* Import the survey into /limesurvey/mango/mango_dictator/survey/limesurvey_survey_336985.lss

## Create and edit experiments
An experiment is a serie of surveys. Mango routs users throught this serie.
admin panel for expermients : limesurvey_host/mango/mango_surveys_router/services/experiments.php

# Credits
[medialab](http://www.medialab.sciences-po.fr/)
