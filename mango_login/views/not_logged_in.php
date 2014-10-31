<?php

require_once('../lang/lang.php');

/********** VARIABLES **********/

$iExperimentId  = isset($_GET['experiment']) ? $_GET['experiment'] : (isset($_POST['experiment']) ? $_POST['experiment'] : -1);
$sLang          = isset($_GET['lang']) ? $_GET['lang'] : (isset($_POST['lang']) ? $_POST['lang'] : 'en');
$oFile          = '../mango_login/lang/lang.xml';
$oTranslator    = new translator($sLang, $oFile);

?>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $oTranslator->login ?></title>
        <link rel="stylesheet" href="../../upload/templates/mango/template.css" />
        <link rel="stylesheet" href="css/plugin.css" />
        <script type="text/javascript" src="../../third_party/jquery/jquery-1.10.2.min.js"></script>
    </head>
    <body>
        <div id="top">      </div>
        <div id="content">
            <div id="left"> 
                <h1><?php echo $oTranslator->login ?></h1>
                <div class="reminder"></div>
            </div>
            <div id="right">
                <div id="wrapper" class="login-form">
                    <p id="tokenmessage"><?php echo $oTranslator->login_text ?><br/></p>
                    <!-- login form box -->
                    <form method="post" action="index.php" name="loginform">
                        <div class="form-group">
                            <label for="login_input_username">Login</label>
                            <input id="login_input_username" class="login_input" type="text" name="token" required />
                        </div>
                        <div class="form-group">
                            <label for="login_input_password">Password</label>
                            <input id="login_input_password" class="login_input" type="password" name="user_password" autocomplete="off" required />
                        </div>
                        <div class="form-group">
                            <input id="login_experiment" class="login_input" type="hidden" name="experiment" value="<?php echo $iExperimentId ?>" />
                            <input id="login_lang" class="login_input" type="hidden" name="lang" value="<?php echo $sLang ?>" />
                        </div>
                        <div class="form-group">
                        <?php
                            // show potential errors / feedback (from login object)
                            if (isset($login)) {
                                if ($login->errors) {
                                    foreach ($login->errors as $error) {
                                        echo '<span class="error">' . $error . '</span>';
                                    }
                                }
                                if ($login->messages) {
                                    foreach ($login->messages as $message) {
                                        echo '<span class="message">' . $message . '</span>';
                                    }
                                }
                            }
                        ?>
                        </div>
                        <div class="form-group">
                            <input type="submit"  name="login" value="<?php echo $oTranslator->login_button ?>" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>