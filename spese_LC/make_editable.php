<?php
// get VID from session
require('../access.php');
$ID = $_SESSION['ID'];

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
        <title>Modifica richiesta singola</title>
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
        $rid = $_GET['rid'];

        // select single row, using ID
        $stringa = "SELECT * FROM spese_LC WHERE RID = '".$rid."'";
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
            <div class="col-md-3"></div>
            <div class="col-md-6" align="center">
                <h3>Dettagli richiesta</h3>
            </div>
            <div class="col-md-1"> 
                <?php echo "<a class=\"btn btn-default\" href=\"" . $red_page . "\" style=\"float: left;\" >Back</a>";?>
            </div>
            <div class="col-md-2"> 
                <a class="btn btn-default" href="../index.php" >Home</a>
                <a class="btn btn-default" href="search_movimenti.php">Lista movimenti</a>
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <form method="GET" action="edit_richiesta.php">
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
                            <td>RID</td>
                            <td><?php echo $row['RID'];?></td>
                        </tr>
                        <tr>
                            <td>Comitato Locale</td>
                            <td><b><?php echo $row['LC'];?></b></td>
                        </tr>
                        <tr>
                            <td>Data invio richiesta</td>
                            <td><?php echo format_date($row['data']);?></td>
                        </tr>
                        <tr>
                            <td>Nome evento</td>
                            <td><?php echo $row['oggetto'];?></td>
                        </tr>
                        <tr>
                            <td>Descrizione e finalità</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_descrizione\" rows=\"4\" cols=\"40\">" . $row['descrizione'] . "</textarea>" ;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Importo</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_importo\" rows=\"1\" cols=\"40\">" . $row['importo'] . "</textarea>" ;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Beneficiario</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_beneficiario\" rows=\"1\" cols=\"40\">" . $row['beneficiario'] . "</textarea>" ;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>IBAN</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_iban\" rows=\"1\" cols=\"40\">" . $row['IBAN'] . "</textarea>" ;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Documenti</td>
                            <td>
                                <?php 
                                echo "<textarea name=\"new_url_doc\" rows=\"4\" cols=\"40\">" . $row['url_doc'] . "</textarea>" ;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type="submit" value="Modifica" class="btn btn-default" onclick="return confirm('Vuoi veramente modificare questo record?')" style="float: center;"/>
                            </td>
                        </tr>
                    </table>
                    <?php
                    echo "<input type=\"hidden\" name=\"rid\" value=\"" . $rid . "\"></input>";     
                    ?>
                </form>
            </div>


        </div>


        <br>
        <div class="col-md-2"></div>
        <a class="btn btn-default" href="search_movimenti.php" style="float: left;" >Full list</a>
        <?php
        echo "<a class=\"btn btn-default\" href=\"single_mov.php?rid=" . $rid . "\" style=\"float: left;\" >Back</a>";
        ?>

    </body>
</html>