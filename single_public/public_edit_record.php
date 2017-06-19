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


$sid = $_POST['id'];

//echo $sid;


$name = ucwords($_POST['nome']);
$surname = ucwords($_POST['cognome']);
$birthday = format_date($_POST['data_nascita']);
$birthplace = ucwords($_POST['luogo_nascita']);
$cf = strtoupper($_POST['cf']);
$addr1 = $_POST['indirizzo'];
$city = $_POST['citta'];
$prov = $_POST['prov'];
$cap = $_POST['cap'];
$state = $_POST['stato'];
$address = $addr1 . " " . $city . " " . $prov . " " . $cap . " " . $state;
$email = $_POST['email'];
$uni1 = $_POST['uni'];
if ($uni1=='altro'){
    $university = $_POST['altra_uni'];
} elseif ($uni1=='estero'){
    $university = "ESTERO: " . $_POST['estero_uni'];
} else 
    $university = $_POST['uni'];
$study = $_POST['studi'];
//$data = $mysqli->real_escape_string("2017-11-10");

if (!empty($name) and !empty($surname) and !empty($birthday)){

    $stringa = "UPDATE " . $table . " SET nome = \"" . $name . "\", cognome = \"" . $surname . "\", luogo_nascita = \"" . $birthplace . "\", data_nascita = '" . $birthday . "', CF = '" . $cf . "', indirizzo = '" . $address . "', email = '" . $email . "', uni = '" . $university . "', studi = '" . $study . "' WHERE id = '" . $sid . "'";
    $result = $mysqli->query($stringa);
    #echo $stringa;

    #header ("location: www.google.it?num=" . $result1->num_rows);


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
<body>
    
    <br>
    
    <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="../AISF_logo.png" alt="AISF_logo"></div>
        <div class="col-md-4"></div>
    </div>
    
    <br>
    
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <h2 style="text-align:center">Modifica avvenuta con successo!</h2>
        </div>
        <div class="col-md-2"></div>
    </div>
    
    <br><br>
    <br><br>
    <div class="row" style="text-align:center">
    <div class="col-md-2"></div>
    <div class="col-md-4">
        <form action="singleEntry_public.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $sid;?>"/>
            <button class="btn btn-default" type="submit" style="float: center;">Torna al tuo record</button>
        </form>
    </div>
    <div class="col-md-4"><a class="btn btn-default" href="single_login.php" >Logout</a></div>
    <div class="col-md-2"></div>
    </div>

    
    </body>
</html>