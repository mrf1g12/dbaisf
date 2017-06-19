<?php

$Lpassword = array('admin'=>'465b37d89068c53303af42cb76271f6c26351941',
                   'esecutivo_aisf'=>'8596b50b09eff1a223c1fdcf52b6f44a783a9164'
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
if (isset($_POST['password']) and isset($_POST['ID'])) {
    if (array_key_exists($_POST['ID'], $Lpassword)) {
        if ( $Lpassword[$_POST['ID']] == sha1($_POST['password']) ) {
            $_SESSION['passwordOk'] = true;
            $_SESSION['ID'] = $_POST['ID'];
        } else {
            header('Location: ' . 'badPassword.php');
        }
    } else {

        // connect to db
        $dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
        $dbinfo = explode(" ", $dbinfo);
        $user = $dbinfo[1];
        $password = $dbinfo[3];
        $db = $dbinfo[5];
        $host = $dbinfo[7];
        $port = $dbinfo[9];
        $table = $dbinfo[11];

        $mysqli = new mysqli($host, $user, $password, $db);

        if ($mysqli->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        // utf-8 encoding
        mysqli_set_charset($mysqli, 'utf8');

        $stringa = "SELECT password FROM LC WHERE username='" . $_POST['ID'] . "'";
        //echo $stringa;
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        if ($entries==0) {
            $_SESSION['passwordOk'] = false;
            header('Location: ' . 'badPassword.php');
        } else {
            $row = $result->fetch_array();
            if ($row['password'] != $_POST['password']) {
                $_SESSION['passwordOk'] = false;
                header('Location: ' . 'badPassword.php');
            } else{ 
                $_SESSION['passwordOk'] = true;
                $_SESSION['ID'] = $_POST['ID'];
            }
        }
    } 
}



// open if
if ( !$_SESSION['passwordOk'] or !isset($_SESSION['ID']) ):
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
            <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h1 style="text-align:center">Archivio telematico AISF</h1>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br><br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><b>Autenticati con ID e password:</b></div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form method="POST">
                    ID: <input type="text" name="ID">        Password: <input type="password" name="password">
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