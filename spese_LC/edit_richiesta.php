<?php

require('../access.php');
$ID = $_SESSION['ID'];


if ($ID!='admin' && $ID!='esecutivo_aisf'){
    header('Location: ' . '../badPrivileges.php'); 
}

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


$rid = $_GET['rid'];

$descrizione = $_GET['new_descrizione'];
$importo = $_GET['new_importo'];
$beneficiario = $_GET['new_beneficiario'];
$iban = $_GET['new_iban'];
$url_doc = $_GET['new_url_doc'];


$stringa = "UPDATE spese_LC SET descrizione= \"" . $descrizione . "\", importo = \"" . $importo . "\", beneficiario = \"" . $beneficiario . "\", IBAN = \"" . $iban . "\", url_doc = '" . $url_doc . "' WHERE RID='".$rid."'";
$result = $mysqli->query($stringa);

#$jotformAPI = new JotForm($api);
#$edit = array("40" => $name, "41" => $surname, "26" => $cf, "5" => $email);
#$jotformAPI->editSubmission($sid, $edit);

//$result->free();
$mysqli->close();

#echo $sid;    

header("location: " . "single_mov.php?rid=".$rid);

?>