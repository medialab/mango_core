<?php

require_once("../../lang/lang.php");

/********** VARIABLES **********/

$iExperimentId  = $_GET["experiment"];
$sLang          = $_GET["lang"];
$oFile          = "../../mango_login/lang/lang.xml";
$oTranslator    = new translator($sLang, $oFile);

// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}

?>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php echo $oTranslator->login ?></title>
        <link rel="stylesheet" href="../../../upload/templates/mango/template.css" />
        <script type="text/javascript" src="../../../third_party/jquery/jquery-1.10.2.min.js"></script>
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
                            <input id="login_input_username" class="login_input" type="text" name="user_name" required />
                        </div>
                        <div class="form-group">
                            <label for="login_input_password">Password</label>
                            <input id="login_input_password" class="login_input" type="password" name="user_password" autocomplete="off" required />
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