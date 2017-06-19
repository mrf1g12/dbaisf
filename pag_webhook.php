<?php

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

//Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

// Format University names
function format_uni($uni)
{
    if (strpos($uni, 'AQUILA') !== false) { //If $uni contains "AQUILA", then...
        return "L'AQUILA";
    }
    if (strpos($uni, 'TRE') !== false) return "ROMA TRE";
    if (strpos($uni, 'Vergata') !== false) return "ROMA Tor Vergata";
    if (strpos($uni, 'Sapienza') !== false) return "ROMA La Sapienza";
    if (strpos($uni, 'INSUBRIA') !== false) return "INSUBRIA";
    if (strpos($uni, 'CALABRIA') !== false) return "CALABRIA";
    if (strpos($uni, 'SALENTO') !== false) return "SALENTO";
    if (strpos($uni, 'BARI') !== false) return "BARI";
    if (strpos($uni, 'Seconda') !== false) return "CASERTA";
    if (strpos($uni, 'NAPOLI') !== false) return "NAPOLI";
    if (strpos($uni, 'Sacro Cuore') !== false) return "BRESCIA";
    return str_replace("Università degli Studi di ","",$uni);
}

function format_data($date)
{
    return $date['year'] . "-" . $date['month'] . "-" . $date['day'];
}

//Get unique submissionID - nothing to change here
$sid = $mysqli->real_escape_string($_REQUEST['submissionID']);

//Get form field values and decode - nothing to change here
$fieldvalues = $_REQUEST['rawRequest'];

echo $fieldvalues;

if (!empty($_REQUEST)){
    $obj = json_decode($fieldvalues, true);

    //Replace the field names from your form here
    $name = $mysqli->real_escape_string(ucwords($obj['q40_nome1']));
    $surname = $mysqli->real_escape_string(ucwords($obj['q41_cognome1']));
    $birthday = $mysqli->real_escape_string(format_data($obj['q48_dataDi']));
    $email = $mysqli->real_escape_string($obj['q42_email1']);
    $new_reg = $mysqli->real_escape_string(strtoupper($obj['43_nuova_iscrizione']));
    $method = $mysqli->real_escape_string($obj['q45_metodoDi']);
    
    $stringa = "SELECT * FROM " . $table . " WHERE nome = '" . $name . "' AND cognome = '" . $surname ."' AND data_nascita = '" . $birthday . "'";
    $result1 = $mysqli->query($stringa);
    
    echo $result1->num_rows;
    #header ("location: www.google.it?num=" . $result1->num_rows);

    if ($result1->num_rows == 0) {
        echo "FANCULO!";
        #echo "Existing Record Updated!";
    } elseif ($method == 'PayPal'){
        $row = $result1->fetch_assoc();
        $stringa = "UPDATE " . $table . " SET q2017='Pagato' WHERE id='" . $row['id'] ."'";
        $result = $mysqli->query($stringa);
    } 
}

$mysqli->close();
?>