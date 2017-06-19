<?php
// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];

require "../PHPMailer-master/PHPMailerAutoload.php";

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

function status_to_color($s) {
    if ($s == '-') {
        return 'warning';
    } elseif ($s == 'Pagato') {
        return 'success';
    } elseif ($s == 'Non Pagato') {
        return 'danger';
    }
}

function status_to_btncolor($s) {
    if ($s == '-') {
        return 'btn-warning';
    } elseif ($s == 'Pagato') {
        return 'btn-success';
    } elseif ($s == 'Non Pagato') {
        return 'btn-danger';
    }
}

function appr_to_btncolor($s) {
    if ($s == 'Approvato') {
        return 'btn-info';
    } elseif ($s == 'Non Approvato') {
        return 'btn-warning';
    } else {
        return 'btn-danger';
    }
}

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
        <style>
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
            #blanket {
                background-color:#111;
                opacity: 0.65;
                *background:none;
                position:absolute;
                z-index: 9001;
                top:0px;
                left:0px;
                width:100%;
            }

            #popUpDiv {
                position:absolute;
                background:url(pop-back.jpg) no-repeat;
                width:400px;
                height:400px;
                border:5px solid #000;
                z-index: 9002;
            }
        </style>
    </head>
    <body>

        <?php


        // retrieve values from POST methods
        $nome = $_POST['nome']; 
        $cognome = $_POST['cognome']; 
        $cf = $_POST['cf'];
        $email = $_POST['email'];
        $studi = $_POST['studi'];
        $metodo = $_POST['metodo']; // sort
        
        //echo $nome . "  " . $cognome;

        $stringa = "SELECT * FROM " . $table . " WHERE nome=\"".$nome."\" AND cognome=\"".$cognome."\" AND CF='".$cf."'";
        //echo $stringa;

        $result = $mysqli->query($stringa);
        
        //print_r($result);
        $entries = $result->num_rows;
        if ($entries == 0){
        ?>
        <script type="text/javascript">
            var x=window.alert("I tuoi dati non sono presenti nel database AISF. Prima di procedere al pagamento devi registrarti!")
            window.location.replace("http://ai-sf.it/iscrizione");
        </script>

        <?php
        } elseif ($entries > 1){
        ?>
        <script>
            var x=window.alert("I tuoi dati sono registrati in multipli nel database. Contatta l'amministratore scrivendo a ass.it.stud.fisica@gmail.com")
            window.location.replace("http://ai-sf.it/");
        </script>
        <?php
        } 

        $row = $result->fetch_assoc();


        if ($metodo == 'bonifico') {

            $stringa = "UPDATE " . $table . " SET metodo='Bonifico' WHERE id='" . $row['id'] ."'";
            $result = $mysqli->query($stringa);

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
            $mail->addAddress($email);     // Add a recipient
            #$mail->addAddress('ellen@example.com');               // Name is optional
            #$mail->addReplyTo('info@example.com', 'Information');
            #$mail->addCC('cc@example.com');
            #$mail->addBCC('bcc@example.com');

            #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
            #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'Quota associativa AISF';

            if ($studi == 'triennale' || $studi == 'magistrale'){
                #echo 'Bonifico!!';
                $mail->Body    = "Ciao " . $row['nome'] .",
            <br><br>La tua iscrizione all'AISF è avvenuta con successo!<br>
            Per convalidare la tua iscrizione devi effettuare il pagamento di <b>5.00€</b> tramite bonifico bancario a:
            <br><br>
            Beneficiario: Associazione Italiana Studenti di Fisica<br>
            IBAN: IT64N0335901600100000133131<br>
            BIC/SWIFT: BCITITMX<br>
            Causale: Quota associativa anno 2017 - " . $row['nome'] . " " . $row['cognome'] ."
            <br><br>
            Il bonifico deve essere effettuato entro 15 giorni da oggi. Diversamente, la tua iscrizione non sarà più valida e verrà cancellata.
            <br>
            Per problemi o informazioni scrivere a ass.it.stud.fisica@gmail.com<br>
            Grazie per la tua iscrizione e a presto!<br>
            <br>
            <br>
            L'Associazione Italiana Studenti di Fisica<br>
            www.ai-sf.it";
                #$mail->AltBody = "Ciao " . $row['nome'] .",Abbiamo ricevuto la tua quota associativa!
                #    Puoi modificare ed aggiornare i tuoi dati in ogni momento usando questo link: 'http://submit.jotform.co/form.php?formID=61562917217963&sid=". $sid ."&mode=edit'. Grazie per aver aderito all'AISF!L'Associazione Italiana Studenti di Fisica";
            } else {
                $mail->Body    = "Ciao " . $row['nome'] .",
                    <br><br>La tua iscrizione all'AISF è avvenuta con successo!<br>
                    Per convalidare la tua iscrizione devi effettuare il pagamento di <b>10.00€</b> tramite bonifico bancario a:
                <br><br>
                Beneficiario: Associazione Italiana Studenti di Fisica<br>
                IBAN: IT64N0335901600100000133131<br>
                BIC/SWIFT: BCITITMX<br>
                Causale: Quota associativa anno 2017 - " . $row['nome'] . " " . $row['cognome'] ."
                <br><br>
                Il bonifico deve essere effettuato entro 15 giorni da oggi. Diversamente, la tua iscrizione non sarà più valida e verrà cancellata.
                <br>
                Per problemi o informazioni scrivere a ass.it.stud.fisica@gmail.com<br>
                Grazie per la tua iscrizione e a presto!<br>
                <br>
                <br>
                L'Associazione Italiana Studenti di Fisica<br>
                www.ai-sf.it";
            }
            $mail->send();
            echo "
                <div class=\"small_skip\"></div>
                <div class=\"row\">
                    <div class=\"col-md-4\"></div>
                    <div class=\"col-md-4\" style=\"text-align:center\"><img style=\"width:342px;height:124px;\" src=\"../AISF_logo.png\" alt=\"AISF_logo\"></div>
                    <div class=\"col-md-4\"></div>
                </div>
                <div class=\"med_skip\"></div>
                <div class=\"row\">
                    <div class=\"col-md-2\"></div>
                    <div class=\"col-md-8\" align=\"center\">
                        <h4>La tua registrazione è avvenuta con successo!</h4>
                        <p>Ti abbiamo inviato un'email all'indirizzo <a>" . $email . "</a> con le indicazioni per effettuare il bonifico.</p>
                    </div>
                    <div class=\"col-md-2\"></div>
                </div>";
        } else {
            if ($studi == 'triennale' || $studi == 'magistrale'){
                echo "
                <div class=\"med_skip\"></div>
                <div class=\"row\">
                <div class=\"col-md-2\"></div>
                <div class=\"col-md-8\" align=\"center\">
                   <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
                        <input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
                        <input type=\"hidden\" name=\"hosted_button_id\" value=\"6U9255K2N85DS\">
                        <input type=\"hidden\" name=\"custom\" value='".$row['id']."'>
                        <input type=\"image\" src=\"https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal è il metodo rapido e sicuro per pagare e farsi pagare online.\">
                        <img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/it_IT/i/scr/pixel.gif\" width=\"1\" height=\"1\">
                    </form>
                </div>
                <div class=\"col-md-2\"></div>";
            } else if ($studi == 'phd'){
                echo "
                <div class=\"med_skip\"></div>
                <div class=\"row\">
                <div class=\"col-md-2\"></div>
                <div class=\"col-md-8\" align=\"center\">
                    <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
                          <input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
                          <input type=\"hidden\" name=\"hosted_button_id\" value=\"QQ2P8TS3R9J62\">
                          <input type=\"hidden\" name=\"custom\" value='".$row['id']."'>
                          <input type=\"image\" src=\"https://www.paypalobjects.com/it_IT/IT/i/btn/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"PayPal è il metodo rapido e sicuro per pagare e farsi pagare online.\">
                          <img alt=\"\" border=\"0\" src=\"https://www.paypalobjects.com/it_IT/i/scr/pixel.gif\" width=\"1\" height=\"1\">
                    </form>
                    </div>
                    <div class=\"col-md-2\"></div>";
            }
        }
        ?>
    </body>
</html>