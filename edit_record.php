<?php

require('access.php');
$ID = $_SESSION['ID'];

require "PHPMailer-master/PHPMailerAutoload.php";
include "jotform-api-php/JotForm.php";

if ($ID!='admin'){
    header('Location: ' . 'badPrivileges.php'); 
}

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
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


$sid = $_GET['id'];
$red_page = $_GET['single_page'];

$surname = $_GET['new_surname'];
$name = $_GET['new_name'];
$birthplace = $_GET['new_birthplace'];
$birthday = $_GET['new_birthday'];
$address = $_GET['new_address'];
$cf = $_GET['new_cf'];
$email = $_GET['new_email'];
$university = $_GET['new_uni'];
$study = $_GET['new_studi'];
$data = $_GET['new_data'];


$stringa = "UPDATE " . $table . " SET nome = \"" . $name . "\", cognome = \"" . $surname . "\", luogo_nascita = \"" . $birthplace . "\", data_nascita = '" . $birthday . "', CF = '" . $cf . "', indirizzo = '" . $address . "', email = '" . $email . "', uni = '" . $university . "', studi = '" . $study . "', data = '" . $data . "' WHERE id = '" . $sid . "'";
$result = $mysqli->query($stringa);

#$jotformAPI = new JotForm($api);
#$edit = array("40" => $name, "41" => $surname, "26" => $cf, "5" => $email);
#$jotformAPI->editSubmission($sid, $edit);

//$result->free();
$mysqli->close();

#echo $sid;    

header("location: " . "singleEntry.php?id=".$sid);

?>