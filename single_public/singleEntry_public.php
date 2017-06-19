<?php
// get VID from session

// connect to db
$dbinfo = explode("\n", file_get_contents('../loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// function
function status_to_color($s) {
    if ($s == '-') {
        return 'warning';
    } elseif ($s == 'Pagato') {
        return 'success';
    } elseif ($s == 'Non Pagato') {
        return 'danger';
    } else {
        return 'info';
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


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Single entry result</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <style>
            .tiny_skip{
                margin-top: 5px;
                margin-bottom: 5px}
            .small_skip{
                margin-top: 20px;
                margin-bottom: 20px}
            .med_skip{
                margin-top: 40px;
                margin-bottom: 40px}
        </style>
    </head>
    <body>

        <?php
        $email = $_POST['email'];
        $pwd = $_POST['password'];
        $sid = $_POST['id'];
        
        //echo $email . "<br>" . $pwd . "<br>" . $sid . "<br> CANE";

        //echo "cane: ".$email."  ".$pwd;

        // select single row, using ID

        if ($sid==''){
            $stringa = "SELECT * FROM " . $table . " WHERE email = '".$email."' AND password = '".$pwd."'";
            $result = $mysqli->query($stringa);
            $entries = $result->num_rows;
        } else {
            $stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
            $result = $mysqli->query($stringa);
            $entries = $result->num_rows;
        }

        // should never be more than one result! (because ID must be unique)
        if ($entries == 0) {
            echo "
                <body> 
                    <br>
                    <div class=\"row\">
                        <div class=\"col-md-4\"></div>
                        <div class=\"col-md-4\" style=\"text-align:center\"><img style=\"width:342px;height:124px;\" src=\"../AISF_logo.png\" alt=\"AISF_logo\"></div>
                        <div class=\"col-md-4\"></div>
                    </div>
                    <br> 
                    <div class=\"row\">
                        <div class=\"col-md-2\"></div>
                        <div class=\"col-md-8\">
                            <h1 style=\"text-align:center\">La tua iscrizione all'AISF</h1>
                        </div>
                    <div class=\"col-md-2\"></div>
                    </div>  
                    <br><br>
                    <div class=\"row\" style=\"text-align:center\">
                        <div class=\"col-md-2\"></div>
                        <div class=\"col-md-8\"><b>email o password errati!</b></div>
                        <div class=\"col-md-2\"></div>
                    </div>
                    <br><br>
                    <div class=\"row\" style=\"text-align:center\">
                        <div class=\"col-md-2\"></div>
                        <div class=\"col-md-8\"><a class=\"btn btn-default\" href=\"single_login.php\" >Riprova</a></div>
                        <div class=\"col-md-2\"></div>
                    </div>
                </body>";
        } else if ($entries > 1){
            echo "errore irreversibile!";
        } else {

            // fetch data
            $row = $result->fetch_array();

            $result->free();
            $mysqli->close();

            $backcolor = status_to_color($row['q2017']);
            if ($row['metodo']=='Bonifico'){
                $metodo = 'Bonifico bancario';
            } else if ($row['metodo']=='-'){
                $metodo = '-';
            } else {
                $metodo = 'Pagamento <b>PayPal</b> da parte di '.$row['metodo'];
            }
            
            if ($row['q2016']=='-'){
                $q2016 = 'Non applicabile';
            } else {
                $q2016 = $row['q2016'];
            }

            echo "  
            <html>
                <body>
                 <br>
                    <div class=\"row\">
                        <div class=\"col-md-4\"></div>
                        <div class=\"col-md-4\" style=\"text-align:center\"><img style=\"width:342px;height:124px;\" src=\"../AISF_logo.png\" alt=\"AISF_logo\"></div>
                        <div class=\"col-md-4\"></div>
                    </div>
                    <br> 
                    <div class=\"row\">
                        <div class=\"col-md-2\"></div>
                        <div class=\"col-md-8\">
                            <h2 style=\"text-align:center\">La tua iscrizione all'AISF</h2>
                        </div>
                    </div>
                    <div class=\"med_skip\">

                    <div class=\"row\">
                        <div class=\"col-md-3\"></div>
                        <div class=\"col-md-6\">
                            <table class='table'>
                                <tr>
                                <th width=\"30%\">Campo</th>
                                <th width=\"70%\">Valore</th>
                                </tr>
                                <tr>
                                <td><div class=\"tiny_skip\"></div></td>
                                <td><div class=\"tiny_skip\"></div></td>
                                </tr>
                                <tr>
                                    <td>Cognome</td>
                                    <td><b>".$row['cognome']."</b></td>
                                </tr>
                                <tr>
                                    <td>Nome</td>
                                    <td>".$row['nome']."</td>
                                </tr>
                                <tr>
                                    <td>Luogo e data di nascita</td>
                                    <td>".$row['luogo_nascita'] . ", " . format_date($row['data_nascita'])."</td>
                                </tr>
                                <tr>
                                    <td>Codice Fiscale</td>
                                    <td>".$row['CF']."</td>
                                </tr>
                                <tr>
                                    <td>Indirizzo di residenza</td>
                                    <td>".$row['indirizzo']."</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>".$row['email']."</td>
                                </tr>
                                <tr>
                                    <td>Università</td>
                                    <td>".$row['uni']."</td>
                                </tr>
                                <tr>
                                    <td>Corso di Studi</td>
                                    <td>".$row['studi']."</td>
                                </tr>
                                <tr>
                                    <td><div class=\"tiny_skip\"></div></td>
                                    <td><div class=\"tiny_skip\"></div></td>
                                </tr>
                                <tr>
                                    <td>Data di iscrizione</td>
                                    <td>".format_date($row['data'])."</td>
                                </tr>
                                <tr>
                                    <td>Quota anno 2016</td>
                                    <td>" . $q2016 . "</td>
                                </tr>
                                <tr>
                                    <td>Quota anno 2017</td>
                                    <td class=\"" . $backcolor . "\">" . $row['q2017'] . "</td>
                                </tr>
                                <tr>
                                    <td>Metodo di pagamento</td>
                                    <td>".$metodo."</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
               <div class=\"row\">
                    <div class=\"col-md-3\"></div>
                        <div class=\"col-md-6\">
                             <form action=\"single_login.php\" method=\"GET\" style=\"float: left;\">
                               <input type=\"submit\" value=\"Logout\" class=\"btn btn-info\"></input>
                             </form>
                             <form action=\"public_entry_edit.php\" method=\"POST\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                               <input type=\"submit\" value=\"Modifica dati\" class=\"btn btn-default\"></input>
                            </form>
                            <form action=\"public_edit_pwd.php\" method=\"POST\" style=\"float: left;\">
                               <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                               <input type=\"hidden\" name=\"email\" value=\"" . $row['email'] . "\"></input>
                               <input type=\"submit\" value=\"Modifica password\" class=\"btn btn-default\"></input>
                            </form>
                        </div>
                    <div class=\"col-md-3\"></div>
                </div>
                <div class=\"med_skip\"></div>
                <div class=\"med_skip\"></div>
                <div class=\"med_skip\"></div>
                </body>
        </html>";
        }
        ?>
        
        