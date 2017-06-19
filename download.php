<?php

require('access.php');
$ID = $_SESSION['ID'];

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


// fetch the data
$stringa = $_GET['query']; 
$type = $_GET['type']; 

//echo $stringa;


// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');
// output the column headings
if ($type=='normal'){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    fputcsv($output, array('Cognome', 'Nome', 'Email', 'Università', 'Studi', 'Data', 'Quota 2017', 'Approvazione'));
} elseif ($type=='email'){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data_email.csv');
    fputcsv($output, array('Email'));
} elseif ($type=='full'){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data_full.csv');
    fputcsv($output, array('ID','Cognome', 'Nome', 'Luogo di nascita', 'Data di nascita', 'CF', 'Indirizzo', 'CAP', 'Email', 'Università', 'Studi', 'Data', 'Metodo','Quota 2016', 'Quota 2017', 'Approvazione'));
}

//fputcsv($output, array('STRINGA: ',$stringa));


$result = $mysqli->query($stringa);

// loop over the rows, outputting them
while($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}


#$stringa = "SELECT * FROM " . $table . " WHERE id = ".$sid;
#$result = $mysqli->query($stringa);
#$entries = $result->num_rows;


// fetch data
#$row = $result->fetch_array();


//$result->free();
$mysqli->close();

#echo $query;    

#header("location: " . $red_page);


?>