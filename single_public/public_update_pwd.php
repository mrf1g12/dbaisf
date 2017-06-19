<?php

//require "PHPMailer-master/PHPMailerAutoload.php";

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$api = $dbinfo[13];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');
function format_date($date){
    $data=explode("/",$date);
    return $data[2]."-".$data[1]."-".$data[0];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Log in</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>

    <?php


    $sid = $_POST['id'];
    $email = (string)$_POST['email'];
    $old_pwd = (string)$_POST['old_password'];
    $new_pwd = (string)$_POST['new_password'];

    //echo "email= " . $email . "<br> old_pwd= " . $old_pwd . "<br> new_pwd= " . $new_pwd . "<br> id=" . $sid . "<br>";

    if ($sid==''){
        header("location: " . "single_login.php");
    } else {

        //echo $sid;

        $stringa = "SELECT * FROM " . $table . " WHERE id='" . $sid ."'";
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        if ($entries==0){
            echo "errore irreversibile";
        } else if ($entries>1){
            echo "errore irreversibile";
        } else {
            $row = $result->fetch_assoc();

            //echo "row email= " . $row['email'] . "<br> row password= " . $row['password'];

            if (trim(strtolower($email))===trim(strtolower($row['email'])) && trim($old_pwd)===trim($row['password'])){
                $stringa = "UPDATE " . $table . " SET password = \"" . $new_pwd . "\" WHERE id = '" . $sid . "'";
                $result = $mysqli->query($stringa);

                echo "
                        <body>
                        <br>
                            <div class=\"row\">
                                <div class=\"col-md-4\"></div>
                                <div class=\"col-md-4\" style=\"text-align:center\"><img style=\"width:342px;height:124px;\" src=\"../AISF_logo.png\" alt=\"AISF_logo\"></div>
                                <div class=\"col-md-4\"></div>
                            </div>
                        <br>
                        <div class=\"row\">
                            <div class=\"col-md-2\"></div>
                            <div class=\"col-md-8\">
                                <h2 style=\"text-align:center\">Modifica della password avvenuta con successo!</h2>
                            </div>
                            <div class=\"col-md-2\"></div>
                        </div>
                        <br><br>
                        <br><br>
                ";                
            } else {
                echo "
                        <body>
                        <br>
                            <div class=\"row\">
                                <div class=\"col-md-4\"></div>
                                <div class=\"col-md-4\" style=\"text-align:center\"><img style=\"width:342px;height:124px;\" src=\"../AISF_logo.png\" alt=\"AISF_logo\"></div>
                                <div class=\"col-md-4\"></div>
                            </div>
                        <br>
                        <div class=\"row\">
                            <div class=\"col-md-2\"></div>
                            <div class=\"col-md-8\">
                                <h2 style=\"text-align:center\">Modifica della password fallita</h2>
                                <h3 style=\"text-align:center\">Email o password errati!</h3>
                            </div>
                            <div class=\"col-md-2\"></div>
                        </div>
                        <br><br>
                        <br><br>
                ";                
            }        
        }
    }
    ?>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form action="singleEntry_public.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $sid;?>"/>
                <button class="btn btn-default" type="submit" style="float: left;">Torna al tuo record</button>
            </form>
            <a class="btn btn-default" href="http://www.ai-sf.it" align="right">Logout</a>
        </div>
        <div class="col-md-4"></div>
    </div>


