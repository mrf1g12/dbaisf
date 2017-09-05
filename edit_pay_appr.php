<?php

require('access.php');
$ID = $_SESSION['ID'];

require "PHPMailer-master/PHPMailerAutoload.php";

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Search results</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- <script src="javascript/bootbox.min.js"></script>
<script>
$(document).on("click", ".alert", function(e) {
bootbox.confirm("Are you sure?", function(result){
if (result) {
var myVariable = <?php echo json_encode($row['id']); ?>;
document.location = "delete.php?id=" + myVariable;
} else{
document.location = "search.php";
}
});
}
);
</script>-->
        <style>
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-8"><h4>ID: <font color="green"><?php echo $ID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9"><?php echo "number of matching entries: <b>".$entries."</b><br><br>"; ?>
            </div>
            <div class="col-md-2">
                <a class="btn btn-default" href="index.php" >Home</a>
                <a class="btn btn-default" href="search.php?query=&sorting=data" >Full list</a>
            </div>
        </div>

        <div class="small_skip"></div>


        <?php


        $type = $_GET['type'];
        $string = $_GET['name_string'];

        $list = explode("\n",$string);
        $multipli = array();
        $inesistenti = array();
        $successi = array();

        if ($type=='pagamento'){
            foreach ($list as $row){
                #echo $row . "<br>";
                $name_surname = explode(",",$row);

                $stringa1 = "SELECT * FROM " . $table . " WHERE nome=\"".trim($name_surname[0])."\" AND cognome=\"".trim($name_surname[1])."\"";
                $result1 = $mysqli->query($stringa1);
                if ($result1->num_rows == 1) {
                    $record = $result1->fetch_assoc(); 
                    $stringa = "UPDATE " . $table . " SET q2017_2018='Pagato' WHERE id = '" . $record['id'] . "'";
                    $result = $mysqli->query($stringa);


                    //send confirmation email
                    $mail = new PHPMailer;
                    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = 'ass.it.stud.fisica@gmail.com';                 // SMTP username
                    $mail->Password = $gmail_pwd;                           // SMTP password
                    $mail->SMTPSecure = 'ssl';    // Enable encryption, 'ssl' also accepted
                    $mail->Port = 587;

                    $mail->From = 'ass.it.stud.fisica@gmail.com';
                    $mail->FromName = 'Associazione Italiana Studenti di Fisica';
                    $mail->addAddress($record['email']);     // Add a recipient
                    $mail->isHTML(true);                                  // Set email format to HTML

                    $mail->Subject = 'Quota associativa AISF';
                    $mail->Body    = "Ciao " . $record['nome'] .",<br><p>Abbiamo ricevuto la tua quota associativa!<br>
        Puoi modificare ed aggiornare i tuoi dati in ogni momento usando questo <a href=http://submit.jotform.co/form.php?formID=61562917217963&sid=". $record['id'] ."&mode=edit>link</a></p><br><p>Grazie per aver aderito all'AISF!</p><p></p><p>L'Associazione Italiana Studenti di Fisica</p>";
                    $mail->AltBody = "Ciao " . $record['nome'] .",Abbiamo ricevuto la tua quota associativa!
        Puoi modificare ed aggiornare i tuoi dati in ogni momento usando questo link: 'http://submit.jotform.co/form.php?formID=61562917217963&sid=". $record['id'] ."&mode=edit'. Grazie per aver aderito all'AISF!L'Associazione Italiana Studenti di Fisica";
                    $mail->send();
                    $nominativo1 = $name_surname[0]." ".$name_surname[1];
                    array_push($successi,$nominativo1);
                    #header("Location: " . "search.php?nome=".$name_surname[0]."&cognome=".$name_surname[1]); 
                } elseif ($result1->num_rows > 1){
                    #echo $name_surname[1];
                    #header("Location: " . "search.php?nome=".$name_surname[0]."&cognome=".$name_surname[1]); 
                    $nominativo2 = $name_surname[0]." ".$name_surname[1];
                    array_push($multipli,$nominativo2);
                } else {
                    $nominativo3 = $name_surname[0]." ".$name_surname[1];
                    array_push($inesistenti,$nominativo3);
                }
            }
        } elseif ($type=='approvato'){
            foreach ($list as $row){
                #echo $row . "<br>";
                $name_surname = explode(",",$row);

                $stringa1 = "SELECT * FROM " . $table . " WHERE nome='".trim($name_surname[0])."' AND cognome=\"".trim($name_surname[1])."\"";
                $result1 = $mysqli->query($stringa1);
                
                if ($result1->num_rows == 1) {
                    $record = $result1->fetch_assoc(); 
                    $stringa = "UPDATE " . $table . " SET appr='Approvato' WHERE id = '" . $record['id'] . "'";
                    $result = $mysqli->query($stringa);
                    $nominativo1 = $name_surname[0]." ".$name_surname[1];
                    array_push($successi,$nominativo1);
                    //header("Location: " . "search.php?nome=".$name_surname[0]."&cognome=".$name_surname[1]); 
                } elseif ($result1->num_rows > 1){
                    //header("Location: " . "search.php?nome=".$name_surname[0]."&cognome=".$name_surname[1]); 
                     $nominativo2 = $name_surname[0]." ".$name_surname[1];
                    array_push($multipli,$nominativo2);
                } else {
                    $nominativo3 = $name_surname[0]." ".$name_surname[1];
                    array_push($inesistenti,$nominativo3);
                }
            }
        }

        #echo $name_surname[0] ."  -   ".$name_surname[1] . "<br>";

        ?>

        <div class="row">
            <?php 
            if ($type=='pagamento') {
                echo "  <div class=\"col-md-2\"></div>
                            <div class=\"col-md-8\"><h4>Status Pagamenti</h4></div>
                            <div class=\"col-md-2\"></div>";
            } else { echo "  <div class=\"col-md-2\"></div>
                            <div class=\"col-md-8\"><h4>Status Approvazione</h4></div>
                            <div class=\"col-md-2\"></div>";} 
            ?> 
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class='table'>
                    <tr>
                        <th width="30%">Log</th>
                        <th>Nominativo</th>
                    </tr>

                    <?php
                    foreach ($successi as $succ){
                        echo "  <tr>
                                        <td class=\"btn-success\" width=\"30%\">Record aggiornato correttamente</td><td>"
                            .$succ."</td>
                                    </tr>";

                    }
                    foreach ($multipli as $mult){
                        #echo explode(" ",$mult)[1];
                        echo "  <tr>
                                        <td class=\"btn-warning\" width=\"30%\">Record multipli per</td><td>
                                            <a href=\"search.php?cognome=".trim(explode(" ",$mult)[1])."\">"
                            .$mult."</a></td>
                                    </tr>";

                    }
                    foreach ($inesistenti as $inex){
                        echo "  <tr>
                                        <td class=\"btn-danger\" width=\"30%\">Record non trovato per</td><td>
                                            <a href=\"search.php?cognome=".trim(explode(" ",$inex)[1])."\">"
                            .$inex."</a></td>
                                    </tr>";

                    }  
                    ?>
                </table>
            </div>
            <div class="col-md-2"></div>
        </div>


    </body>
</html>