<?php
// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];

#require "PHPMailer-master/PHPMailerAutoload.php";

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// retrieve values from POST methods
$sid=$_POST['custom'];
$status=$_POST['payment_status'];
$nome=$_POST['first_name'];
$cognome=$_POST['last_name'];
if ($status == 'Completed'){
    $stringa = "UPDATE " . $table . " SET q2017_2018='Pagato', metodo='".$nome . " ".$cognome ."' WHERE id='" . $sid ."'";
    $result = $mysqli->query($stringa);
}

#echo "CIAO!<br>" . $sid;
?>
