<?php
// get VID from session
require('../access.php');
$ID = $_SESSION['ID'];

if ($ID!='admin'){
    header('Location: ' . '../badPrivileges.php'); 
    exit();
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

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');


// function
function quota_to_color($s) {
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

function status_to_color($s) {
    if ($s == '-') {
        return 'danger';
    } elseif ($s == 'Pagato') {
        return 'success';
    } 
}

function quota_to_status($q) {
    if ($q == '-') {
        return 'Espulso AGA2017';
    } else {
        return 'Membro attivo';
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
        // ID is sent via GET method
        $sid = $_GET['id'];
        $red_page = $_GET['page'];

        #echo $red_page;

        // select single row, using ID
        $stringa = "SELECT * FROM db_AISF_2016 WHERE id = '".$sid."'";
        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // should never be more than one result! (because ID must be unique)
        if ($entries != 1) {
            echo "CONFLICTING IDs !!<br>";
        }

        // fetch data
        $row = $result->fetch_array();

        $result->free();
        $mysqli->close();

        ?>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-7"><h4>ID: <font color="green"><?php echo $ID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <table class='table'>
                    <tr>
                        <th>Campo</th>
                        <th>Valore</th>
                    </tr>
                    <tr>
                        <td><div class="tiny_skip"></div></td>
                        <td><div class="tiny_skip"></div></td>
                    </tr>
                    <tr>
                        <td>ID</td>
                        <td><?php echo $row['id'];?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cognome</td>
                        <td><b><?php echo $row['cognome'];?></b></td>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td><?php echo $row['nome'];?></td>
                    </tr>
                    <tr>
                        <td>Luogo e data di nascita</td>
                        <td><?php echo $row['luogo_nascita'] . ", " . format_date($row['data_nascita']);?></td>
                    </tr>
                    <tr>
                        <td>Codice Fiscale</td>
                        <td><?php echo $row['CF'];?></td>
                    </tr>
                    <tr>
                        <td>Indirizzo di residenza</td>
                        <td><?php echo $row['indirizzo'];?></td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td><?php echo $row['email'];?></td>
                    </tr>
                    <tr>
                        <td>Università</td>
                        <td><?php echo $row['uni'];?></td>
                    </tr>
                    <tr>
                        <td>Corso di Studi</td>
                        <td><?php echo $row['studi'];?></td>
                    </tr>
                    <tr>
                        <td>Data di iscrizione</td>
                        <td><?php echo format_date($row['data']);?></td>
                    </tr>
                    <tr>
                        <td>Metodo di pagamento</td>
                        <td><?php echo $row['metodo'];?></td>
                    </tr>
                    <tr>
                        <td>Quota anno 2016</td>
                        <?php
                        $backcolor = quota_to_color($row['q2016']);
                        //$col_status = "<td class=\"" . $backcolor . "\">" . $row['q2016'] . "</td>";
                        echo "<td class=\"" . $backcolor . "\">" . $row['q2016'] . "</td>"
                        ?>
                    </tr>
                    <tr>
                        <td>Stato</td>
                        <?php
                            $backcolor = status_to_color($row['q2017']);
                            //$col_status = "<td class=\"" . $backcolor . "\">" . $row['q2016'] . "</td>";
                        echo "<td class=\"" . $backcolor . "\">" . quota_to_status($row['q2017']) . "</td>"
                        ?>
                    </tr>
                </table>
            </div>
            <div class="col-md-1"> 
                <?php echo "<a class=\"btn btn-default\" href=\"" . $red_page . "\" style=\"float: left;\" >Back</a>";?>
            </div>
            <div class="col-md-1"> 
                <a class="btn btn-primary" href="../index.php" >Home</a>
                <a class="btn btn-default" href="index_old.php" >Home 2016</a>
                <a class="btn btn-default" href="search_old.php?query=&cfilter=all&cfilter2=all&sorting=data">Full list 2016</a>
            </div>
            <div class="col-md-1"></div>

        </div>

        <br>
    </body>
</html>