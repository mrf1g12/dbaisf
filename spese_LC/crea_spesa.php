<?php
require('../access.php');
$ID = $_SESSION['ID'];

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

function format_date($date){
    $time = strtotime($date);
    return date("d/m/Y", $time);
}

function fixFilesArray(&$files)
{
    $names = array( 'name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

    foreach ($files as $key => $part) {
        // only deal with valid keys and multiple files
        $key = (string) $key;
        if (isset($names[$key]) && is_array($part)) {
            foreach ($part as $position => $value) {
                $files[$position][$key] = $value;
            }
            // remove old key reference
            unset($files[$key]);
        }
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
        $lc_sigla = $_POST['lc'];     
        $oggetto = $_POST['subject']; 
        $descrizione1 = $_POST['descrizione']; 
        $descrizione = str_replace("'", "''", $descrizione1);
        $importo = $_POST['importo'];
        $cofinanziato = $_POST['cofinanziato'];
        $cofinanziatori = $_POST['cofinanziatori'];
        $beneficiario = $_POST['beneficiario'];
        $iban = $_POST['iban'];

        $stringa1 = "SELECT LC,email FROM LC WHERE lc_sigla='".$lc_sigla."'";
        $result = $mysqli->query($stringa1);

        $row=$result->fetch_assoc();
        $lc = trim($row['LC']);
        $lc_email = $row['email'];
        $data = date('Y-m-d');

        $stringa = "INSERT IGNORE INTO spese_LC (LC,lc_sigla,data,oggetto,descrizione,cofinanziatori,beneficiario,IBAN,importo,status) VALUES ('".$lc."','".$lc_sigla."', '".$data."','".$oggetto."','".$descrizione ."','".$cofinanziatori."','".$beneficiario."','".$iban."','". $importo . "','pending')";
        //$stringa = "INSERT IGNORE INTO spese_LC SET (LC) VALUES ('". $lc . "')";
        //echo $stringa;
        $result = $mysqli->query($stringa);

        $stringa = "SELECT LAST_INSERT_ID()";
        $result = $mysqli->query($stringa);
        $row = $result -> fetch_assoc();
        $id = $row['LAST_INSERT_ID()'];
        $rid = $lc_sigla.$id;


        //UPLOAD FILE
        $baseurl = "www.ai-sf.it/dbaisf/spese_lc/";
        $url = array();
        $existed = array();

        //$file_ary = rearrange($_FILES['fileToUpload']);
        if (!empty($_FILES['fileToUpload']['name'][0])){
            //echo "culo!";
            //print_r($_FILES['fileToUpload']['name']);
            // echo "<br><br>";*
            fixFilesArray($_FILES['fileToUpload']);
            foreach ($_FILES['fileToUpload'] as $position => $file) {
                //echo "filename: ".$file['name'];
                $errors= array();
                $file_name = preg_replace('/\s+/', '_',$file['name']);
                $file_size =$file['size'];
                $file_tmp =preg_replace('/\s+/', '_',$file['tmp_name']);
                $file_type=$file['type'];
                $file_ext=strtolower(explode('.',$file_name)[1]);

                $expensions= array("pdf","txt");

                if(in_array($file_ext,$expensions)=== false){
                    $errors[]="Formato file non valido, usare file PDF.";
                }

               /* if($file_size > 500000){
                    $errors[]='Dimensione file troppo grande.';
                }*/

                //$new_file_name = $uid ."_".$contribution."." . $file_ext;
                $path = "docs/". $lc_sigla . "/" . $rid . "/";
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if(empty($errors)==true){
                    $target_file = $path.$file_name;
                    if (file_exists($target_file)) {
                        array_push($existed,$file_name);
                    } else {
                        move_uploaded_file($file_tmp,$target_file);
                        array_push($url,$baseurl.$target_file);
                    }
                    //echo "Success";
                }else{
                    echo "<script type=\"text/javascript\">"."alert('ERROR: ".$errors[0]." ".$error[1]."');"."</script>";
                }
                //       }
                /* echo "<div class=\"row\">
                        <div class=\"col-md-3\" style=\"text-align:center\"></div>
                            <div class=\"col-md-6\" align=\"center\">
                            <h3>SUCCESS!</h3>
                            Your abstract was successfully uploaded. We have sent you a confirmation email.
                        </div>
                        <div class=\"col-md-3\"></div>
                    </div>";*/
            }
            $url_string="";
            if (count($url)>0){
                foreach ($url as $value){
                    $url_string .= $value . " "; 
                }
            }
        }


        $stringa = "UPDATE spese_LC SET RID='".$rid."', url_doc='".$url_string."' WHERE ID='".$id."'";
        $result = $mysqli->query($stringa);


        /*       //print_r($result);
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
*/

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
        #$mail->addAddress('m.refiorentin@gmail.com');               // Name is optional
        #$mail->addReplyTo('info@example.com', 'Information');
        #$mail->addCC($lc_email);
        #$mail->addBCC('bcc@example.com');

        #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Richiesta finanziamento LC '.$lc;


        $corpo = "Al Comitato Esecutivo AISF,
            <br><br>Richiesta di finanziamento da parte del Comitato Locale di <b>".$lc."</b><br><br><br>
            <u>Data richiesta:</u> ".format_date($data)."<br>
            <u>Codice richiesta:</u> <b>".$rid."</b><br>
            <u>Oggetto:</u> ".$oggetto."<br>
            <u>Importo:</u> <b>".$importo." euro.</b><br><br>
            <u>Descrizione:</u> <br>".$descrizione."<br><br>";
        if ($cofinanziato == 'yes'){
            $corpo .= "<u>Cofinanziato da:</u> ".$cofinanziatori."<br>";
        }
        if (count($url)>0){
            $corpo .= "<u>Documenti</u> all'url:<br>";
            foreach ($url as $value){
                $corpo .= $value . "<br>";
            }
            echo "<br>";
        }
        $corpo .= "<u>Stato richiesta: </u><b> in attesa.</b><br><br><br>Mail generata automaticamente dal sistema AISF.";

        $mail->Body = $corpo;
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
                        <h4>Richiesta di finanziamento inoltrata con successo!</h4>
                    <br><br><br>
                        ";

        if (count($url)>0){
            echo "<div class=\"row\" align=\"center\">
                        <table class='table'>
                        <tr>
                            <th>Sono stati caricati i seguenti file:</th>
                        </tr>";
            foreach($url as $value){
                echo "<tr> 
                        <td><a href=\"https://".$value."\">".$value."</a></td>
                        </tr>";
            }
            echo "</table>
                    </div>";
        }
        if (count($existed)>0){
            echo "<div class=\"row\" align=\"center\">
                    <table class='table'>
                        <tr>
                            <th>I seguenti file erano già presenti e <b>non</b> sono stati caricati:</th>
                        </tr>";
            foreach($existed as $value){
                echo "<tr>
                        <td>".$value."</td>
                       </tr>";
            }
            echo "</table>
                    </div>";
        }
        echo "È stata inviata un'email a esecutivo@ai-sf.it e a ".$lc_email." con un riepilogo dei dati.
                </div>
                    <div class=\"col-md-2\"></div>
                </div>
                <div class='med_skip'></div>
                <div class=\"row\" align='center'>
                    <a class=\"btn btn-default\" href=\"https://www.ai-sf.it/dbaisf/\">Home</a>
                </div>
                
                ";
        ?>
    </body>
</html>