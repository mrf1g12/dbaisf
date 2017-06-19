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
if (!empty($_REQUEST)){
    $obj = json_decode($fieldvalues, true);

    //Replace the field names from your form here
    $name = $mysqli->real_escape_string(ucwords($obj['q40_nome']));
    $surname = $mysqli->real_escape_string(ucwords($obj['q41_cognome']));
    $birthday = $mysqli->real_escape_string(format_data($obj['q50_dataDi']));
    $birthplace = $mysqli->real_escape_string($obj['q42_luogoDi42']);
    $cf = $mysqli->real_escape_string(strtoupper($obj['q26_codiceFiscale']));
    $addr1 = $mysqli->real_escape_string($obj['q37_indirizzoDi37']['addr_line1']);
    $addr2 = $mysqli->real_escape_string($obj['q37_indirizzoDi37']['addr_line2']);
    $city = $mysqli->real_escape_string($obj['q37_indirizzoDi37']['city']);
    $prov = $mysqli->real_escape_string($obj['q37_indirizzoDi37']['state']);
    $cap = $mysqli->real_escape_string($obj['q37_indirizzoDi37']['postal']);
    $state = $mysqli->real_escape_string($obj['q32_stato32']);
    $address = $addr1 . " " . $addr2 . " " . $city . " " . $prov . " " . $cap . " " . $state;
    $email = $mysqli->real_escape_string($obj['q5_email']);
    $uni1 = $obj['q18_universita'];
    if ($uni1=='Altro'){
        $altro = $mysqli->real_escape_string($obj['q19_altraUniversita']);
        $university = $mysqli->real_escape_string($altro);
    } elseif ($uni1=='ESTERO'){
        $estero = $mysqli->real_escape_string("ESTERO: ".$obj['q20_universitaEstera20']);
        $university = $mysqli->real_escape_string($estero);
    } else 
        $university = $mysqli->real_escape_string(format_uni($uni1));
    $study = $mysqli->real_escape_string($obj['q7_corsoDi']);
    $data = $mysqli->real_escape_string(format_data($obj['q45_data']));
    //$data = $mysqli->real_escape_string("2017-11-10");
    $stato2016 = $mysqli->real_escape_string($obj['q44_stato20152016']);
    $stato2017 = $mysqli->real_escape_string($obj['q46_stato20162017']);
    $appr = $mysqli->real_escape_string($obj['q51_approvazione']);
    $from_url = $obj['q49_clickTo'];
    
    $stringa = "SELECT * FROM " . $table . " WHERE oid = '" . $sid . "'";
    $result1 = $mysqli->query($stringa);
    
    #header ("location: www.google.it?num=" . $result1->num_rows);

    if ($result1->num_rows > 0) {
        $stringa = "UPDATE " . $table . " SET nome = '" . $name . "', cognome = '" . $surname . "', luogo_nascita = '" . $birthplace . "', data_nascita = '" . $birthday . "', CF = '" . $cf . "', indirizzo = '" . $address . "', cap = '" . $cap . "', email = '" . $email . "', uni = '" . $university . "', studi = '" . $study . "', data = '" . $data . "', appr = '" . $appr . "' WHERE id = '" . $sid . "'";
        #$stringa = "INSERT IGNORE INTO " . $table . " (id, nome, cognome, CF, email, uni, studi, data, q2016, q2017, appr) VALUES ('" . $sid . "', '" . $name . "', '" . $surname . "', '" . $cf . "', '" . $email . "', '" . $university . "', '" . $study . "', '" . $data . "', '" . $stato2016 . "', '" . $stato2017 . "', '" . $appr . "')";
        $result = $mysqli->query($stringa);
        #echo "Existing Record Updated!";
    } else {
        //  if (strpos($from_url, 'edit') == false) { //If $from_url does NOT contain "edit", then add record
        $stringa = "INSERT IGNORE INTO " . $table . " (id, nome, cognome, luogo_nascita, data_nascita, CF, indirizzo, cap, email, uni, studi, data, q2016, q2017, appr) VALUES (UUID_SHORT(), '" . $name . "', '" . $surname . "', '" . $birthplace . "', '" . $birthday . "', '" . $cf . "', '" . $address . "', '" .  $cap . "', '" .  $email . "', '" . $university . "', '" . $study . "', '" . $data . "', '" . $stato2016 . "', '" . $stato2017 . "', '" . $appr . "')";
        #echo $stringa;
        $result = $mysqli->query($stringa);
        if ($result === false) {echo "SQL error:".$mysqli->error;}
    }
    //}
}

$mysqli->close();
?>