<?php

$password = 'daffe82896ec65488527d99d688eb8e2abd6496c'; // SHA-1 encryption for password
$Lpassword = array('eugenio'=>'daffe82896ec65488527d99d688eb8e2abd6496c',
                   'michele'=>'daffe82896ec65488527d99d688eb8e2abd6496c',
                   'lorenzo'=>'091c55b129e7c784175492cc9bdae91c5ab68796',
                   'lucio'=>'091c55b129e7c784175492cc9bdae91c5ab68796',
                   'oc_member'=>'89946a3a91f8713a489aefb96a436cec79fc6fc8',

                  );

session_start();

// db variables
//if ( !isset($_SESSION['dbconnect']) ) {
//$_SESSION['dbconnect'] = array("user"=>"root", "password"=>"root", "host"=>"localhost", "port"=>"3306", "db"=>"aisf", "table"=>"test");
//}


// if loggedIn is not set, set it to false
if ( !isset($_SESSION['passwordOk']) )
{
    $_SESSION['passwordOk'] = false;
}



// get POST values from form below
if (isset($_POST['password']) and isset($_POST['VID'])) {
    if (array_key_exists($_POST['VID'], $Lpassword)) {
        if ( $Lpassword[$_POST['VID']] == sha1($_POST['password']) ) {
            $_SESSION['passwordOk'] = true;
            $_SESSION['VID'] = $_POST['VID'];
        } else {
            header('Location: ' . 'badPassword.php');
        }
    } else {
        header('Location: ' . 'badPassword.php');
    }
} 


// open if
if ( !$_SESSION['passwordOk'] or !isset($_SESSION['VID']) ):
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Log in</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:162px;height:243px;" src="LOGO.jpg" alt="ICPS_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 style="text-align:center">Participants selection system</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br><br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><b>Log in with your VID and password:</b></div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form method="POST">
                    VID: <input type="text" name="VID">        Password: <input type="password" name="password">
                    <input type="submit" name="submit" value="Login" > 
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>


    </body>
</html>


<?php
//close if
exit();
endif;
?>
