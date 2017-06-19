<?php
// get VID from session
require('../access.php');
$ID = $_SESSION['ID'];

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

function status_to_color($s) {
    if ($s == 'pending') {
        return 'warning';
    } elseif ($s == 'approvato') {
        return 'info';
    } elseif ($s == 'respinto') {
        return 'danger';
    } elseif ($s == 'pagato')
    {
        return 'success';
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
        </style>
    </head>
    <body>

        <?php


        // retrieve values from GET methods
        $lc_sigla = $_GET['lc'];
        $rid = $_GET['rid'];

        #$sorting = ( $sorting == 'LCNC' ? 'ISNULL(LCNC),LCNC' : $sorting);
        #$sorting = ( $sorting == 'CONTRIBUTION' ? 'ISNULL(CONTRIBUTION),CONTRIBUTION' : $sorting);

        // string of gets in this url. need it to send it to the quick check-in, so that it can get me back here
        //$oldget = "query=" . $query . "&sorting=" . $sorting . "&cfilter=" . $cfilter;


        // query db
        if ($rid!=''){
            $stringa = "SELECT LC,data,oggetto,importo,RID,status FROM spese_LC WHERE RID='".$rid."' ORDER BY data DESC";
        } else if ($lc_sigla!='' && $rid=='') {
            $stringa = "SELECT LC,data,oggetto,importo,RID,status FROM spese_LC WHERE lc_sigla='".$lc_sigla."' ORDER BY data DESC";
        } else {
            $stringa = "SELECT LC,data,oggetto,importo,RID,status FROM spese_LC ORDER BY data DESC";
        }

        //echo $stringa;

        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // output number of rows found
        //echo "Compatible entries: ".$entries.". <br><br>";

        $thisPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        ?>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-8"><h4>ID: <font color="green"><?php echo $ID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2">
                <a class="btn btn-default" href="../index.php" >Home</a>
                <a class="btn btn-default" href="../search.php?query=&sorting=data" >Full list</a>
            </div>
        </div>
        <div class="row" align="center">
            <h2>Movimenti Comitati Locali</h2>
        </div>
        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <table class='table'>
                    <tr>
                        <td><b>RID</b></td>
                        <td><b>LC</b></td>
                        <td><b>Data</b></td>
                        <td><b>Oggetto</b></td>
                        <td><b>Importo</b></td>
                        <td><b>Stato</b></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                    <?php

                    if ($ID=='admin' || $ID=='esecutivo_aisf'){
                        while($row = $result->fetch_assoc())
                        {
                            $backcolor = status_to_color($row['status']);
                            $linkto = "https://www.ai-sf.it/dbaisf/spese_lc/single_mov.php?rid=" . $row['RID']."&page=".urlencode($thisPage);
                            echo "<tr>
                            <td><a href='".$linkto."'>".$row['RID']."</a></td>
                            <td>".$row['LC']."</td>
                            <td>".$row['data']."</td>
                            <td>".$row['oggetto']."</td>
                            <td><b>".$row['importo']." €</b></td>
                            <td class=\"" . $backcolor . "\">".$row['status']."</td>
                        </tr>";

                        }
                    } else {
                        while($row = $result->fetch_assoc())
                        {
                            $backcolor = status_to_color($row['status']);
                            $linkto = "https://www.ai-sf.it/dbaisf/spese_lc/single_mov.php?rid=" . $row['RID']."&id=".$ID."&page=".urlencode($thisPage);
                            echo "<tr>";
                            if ($row['LC']==$ID){
                                echo "<td><a href='".$linkto."'>".$row['RID']."</a></td>";
                            } else{
                                echo "<td>".$row['RID']."</td>";
                            }
                            echo "<td>".$row['LC']."</td>
                                        <td>".$row['data']."</td>
                                        <td>".$row['oggetto']."</td>
                                        <td><b>".$row['importo']." €</b></td>
                                        <td class=\"" . $backcolor . "\">".$row['status']."</td>
                                </tr>";
                        }
                    }

                    $result->free();
                    $mysqli->close();

                    ?>
                </table>

            </div>
            <div class="col-md-3"></div>
        </div>


        <br>

    </body>
</html>