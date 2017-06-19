<?php

require('access.php');
$ID = $_SESSION['ID'];

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

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}


$sid = $_GET['id']; 
$statusquo = $_GET['statusquo'];
$red_page = $_GET['page'];

echo $statusquo;

if ($statusquo=='Non Approvato'){
    $stringa = "UPDATE " . $table . " SET appr='Approvato' WHERE id=\"" . $sid ."\"";
    $result = $mysqli->query($stringa);
} else {
    $stringa = "UPDATE " . $table . " SET appr='Non Approvato' WHERE id=\"" . $sid ."\"";
    $result = $mysqli->query($stringa);
}

//$result->free();
$mysqli->close();

#echo $red_page;    

header("location: " . $red_page);


?>