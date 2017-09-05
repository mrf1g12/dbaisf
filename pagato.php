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


$sid = $_GET['id']; 
$statusquo = $_GET['statusquo'];
$red_page = $_GET['page'];

$stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
$result = $mysqli->query($stringa);
$entries = $result->num_rows;

// should never be more than one result! (because ID must be unique)
if ($entries != 1) {
    echo "CONFLICTING IDs !!<br>";
}

// fetch data
$row = $result->fetch_array();



#$jotformAPI = new JotForm($api);

if ($statusquo=='Non Pagato' OR $statusquo=='-'){
    $stringa = "UPDATE " . $table . " SET q2017_2018='Pagato' WHERE id='" . $sid ."'";
    $result = $mysqli->query($stringa);
    #$edit = array("46" => "Pagato");
    #$jotformAPI->editSubmission($sid, $edit);

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

    $mail->Subject = 'Quota associativa AISF anno 2017/2018';
    $mail->Body    = "Ciao " . $row['nome'] .",<br><p>Abbiamo ricevuto la tua quota associativa!<br>
        Puoi modificare ed aggiornare i tuoi dati in ogni momento scrivendo a ass.it.stud.fisica@ai-sf.it</p><br><p>Grazie per aver aderito all'AISF!</p><p></p><p>L'Associazione Italiana Studenti di Fisica</p>";
    $mail->send();
    /*
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Message has been sent';
        }
        */


} else {
    $stringa = "UPDATE " . $table . " SET q2017='Non Pagato' WHERE id='" . $sid ."'";
    $result = $mysqli->query($stringa);
    #$edit = array("46" => "Non Pagato");
    #$jotformAPI->editSubmission($sid, $edit);
}

//$result->free();
$mysqli->close();

#echo $red_page;    

header("location: " . $red_page);

?>