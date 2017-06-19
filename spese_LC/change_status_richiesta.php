<?php

require('../access.php');
$ID = $_SESSION['ID'];

require "../PHPMailer-master/PHPMailerAutoload.php";

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
$status = $_GET['new_status'];

if ($status!=''){
    $stringa = "UPDATE spese_LC SET status='".$status."' WHERE RID = '" . $rid . "'";
    $result = $mysqli->query($stringa);
    
    $stringa = "SELECT LC FROM spese_LC WHERE RID = '" . $rid . "'";
    $result = $mysqli->query($stringa);
    $row=$result->fetch_assoc();    
    
    $stringa = "SELECT email FROM LC WHERE LC = '" . $row['LC'] . "'";

    
    $result = $mysqli->query($stringa);
    $row=$result->fetch_assoc();
    $lc_email = $row['email'];

    if ($status=='pagato'){
        $stringa = "SELECT LC,importo FROM spese_LC WHERE RID = '" . $rid . "'";
        $result = $mysqli->query($stringa);
        $row=$result->fetch_assoc();

        $importo = floatval($row['importo']);

        $stringa = "UPDATE LC SET fondi=fondi-".$importo." WHERE LC = '" . $row['LC'] . "'";
        $result = $mysqli->query($stringa);
    }

    if ($status!='pagato'){
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
        $mail->addAddress("esecutivo@ai-sf.it");     // Add a recipient
        $mail->addAddress($lc_email);
        #$mail->addAddress('ellen@example.com');               // Name is optional
        #$mail->addReplyTo('info@example.com', 'Information');
        #$mail->addCC($lc_email);
        #$mail->addBCC('bcc@example.com');

        #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Richiesta finanziamento '.$rid;


        $corpo = "Lo stato della richiesta di finanziamento <b>".$rid."</b> è stato modificato su <b>".$status."<br>";

        $corpo .= "<br><br>Mail generata automaticamente dal sistema AISF.";

        $mail->Body = $corpo;
        $mail->send();
    } 
    //echo $stringa;

    #$jotformAPI = new JotForm($api);
    #$edit = array("40" => $name, "41" => $surname, "26" => $cf, "5" => $email);
    #$jotformAPI->editSubmission($sid, $edit);

    //$result->free();
    $mysqli->close();

    #echo $sid;    

    header("location: " . "single_mov.php?rid=".$rid);
} else {
    header("location: " . "single_mov.php?rid=".$rid);
}

?>