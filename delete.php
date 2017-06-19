<?php

require('access.php');
$ID = $_SESSION['ID'];

require "PHPMailer-master/PHPMailerAutoload.php";
include "jotform-api-php/JotForm.php";

if ($ID!='admin'){
    header('Location: ' . 'https://www.ai-sf.it/dbaisf/badPrivileges.php'); 
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
$reason = $_GET['reason'];
$red_page = $_GET['page'];

$stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
$result = $mysqli->query($stringa);
$row = $result->fetch_array();

if ($reason=='not_paid'){
    $mail = new PHPMailer;

    #$mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'ass.it.stud.fisica@gmail.com';                 // SMTP username
    $mail->Password = $gmail_pwd;                           // SMTP password
    $mail->SMTPSecure = 'ssl';    // Enable encryption, 'ssl' also accepted
    $mail->Port = 587;

    $mail->From = 'ass.it.stud.fisica@gmail.com';
    $mail->FromName = 'Associazione Italiana Studenti di Fisica';
    $mail->addAddress($row['email']);     // Add a recipient
    #$mail->addAddress('ellen@example.com');               // Name is optional
    #$mail->addReplyTo('info@example.com', 'Information');
    #$mail->addCC('cc@example.com');
    #$mail->addBCC('bcc@example.com');

    #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Quota associativa AISF';
    $mail->Body    = "Ciao " . $row['nome'] .",<br><p>Non abbiamo ricevuto la tua quota associativa entro 15 giorni dalla tua registrazione.<br>
       Per questo motivo siamo costretti a cancellare la tua iscrizione all'Associazione.<br>
       Puoi registrarti nuovamente all'AISF tramite il modulo che trovi alla pagina <a href='www.ai-sf.it/iscrizione'>www.ai-sf.it/iscrizione</a>, ricordandoti di versare la quota associativa entro i termini stabiliti.<br>
       Cordiali saluti,<br><br><br>L'Associazione Italiana Studenti di Fisica.";
    $mail->send();
}

$stringa = "DELETE FROM " . $table . " WHERE id = '".$sid."'";
$result = $mysqli->query($stringa);


#$jotformAPI = new JotForm($api);
#$jotformAPI-> deleteSubmission($sid);

//$result->free();
$mysqli->close();

#echo $sid;

header("location:" . $red_page);


?>