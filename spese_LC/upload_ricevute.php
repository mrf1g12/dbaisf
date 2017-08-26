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
        $rid = $_POST['rid'];     

        $stringa1 = "SELECT LC,email FROM LC WHERE lc_sigla='".$lc_sigla."'";
        $result = $mysqli->query($stringa1);

        $row=$result->fetch_assoc();
        $lc = trim($row['LC']);
        $lc_email = $row['email'];
        $data = date('Y-m-d');


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

                $expensions= array("pdf");

                if(in_array($file_ext,$expensions)=== false){
                    $errors[]="Formato file non valido, usare file PDF.";
                }

                /*if($file_size > 200000){
                    $errors[]='Dimensione file troppo grande.';
                }
*/
                //$new_file_name = $uid ."_".$contribution."." . $file_ext;
                $path = "docs/". $lc_sigla . "/" . $rid . "/";
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                if(empty($errors)==true){
                    $target_file = $path.$file_name;
                    if (file_exists($target_file)) {
                        array_push($existed,$target_file);
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
        #$mail->addAddress($lc_email);
        #$mail->addAddress('ellen@example.com');               // Name is optional
        #$mail->addReplyTo('info@example.com', 'Information');
        #$mail->addCC($lc_email);
        #$mail->addBCC('bcc@example.com');

        #$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
        #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Richiesta finanziamento '.$rid;


        $corpo = "Alla richiesta di finanziamento <b>".$rid."</b> sono stati caricati i seguenti file:<br><ul>";
        foreach ($url as $value){
            $corpo .= "<li>".$value."</li>";
        }
        echo "</ol><br>";
        
        $corpo .= "<br><br>Mail generata automaticamente dal sistema AISF.";

        $mail->Body = $corpo;
        $mail->send();

        //echo $url_string;


        $stringa = "UPDATE spese_LC SET url_doc=CONCAT(url_doc,'".$url_string."') WHERE RID='".$rid."'";
        //$stringa = "INSERT IGNORE INTO spese_LC SET (LC) VALUES ('". $lc . "')";
        //echo $stringa;
        $result = $mysqli->query($stringa);
        
        

        header('Location: ' . "single_mov.php/?rid=".$rid."&id=".$ID); 
        #header('Location: ' . "search_movimenti.php"); 

        
        ?>
    </body>
</html>