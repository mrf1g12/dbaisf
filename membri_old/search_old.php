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

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
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
        $nome = $_GET['nome']; 
        $cognome = $_GET['cognome']; 
        $uni = $_GET['uni'];
        $qfilter = $_GET['qfilter'];
        $afilter = $_GET['afilter'];
        $sorting = $_GET['sorting']; // sort

        #$sorting = ( $sorting == 'LCNC' ? 'ISNULL(LCNC),LCNC' : $sorting);
        #$sorting = ( $sorting == 'CONTRIBUTION' ? 'ISNULL(CONTRIBUTION),CONTRIBUTION' : $sorting);

        // string of gets in this url. need it to send it to the quick check-in, so that it can get me back here
        //$oldget = "query=" . $query . "&sorting=" . $sorting . "&cfilter=" . $cfilter;

        // filtering by uni
        if ($nome!="" AND $cognome!="") {
            $query = " WHERE (nome LIKE \"%".$nome."%\" AND cognome LIKE \"%".$cognome."%\") ";
        } elseif ($nome!="" AND $cognome=="") {
            $query = " WHERE (nome LIKE \"%".$nome."%\") ";
        } elseif ($nome=="" AND $cognome!="") {
            $query = " WHERE (cognome LIKE \"%".$cognome."%\") ";
        } else {
            $query = " WHERE (nome LIKE \"%%\" AND cognome LIKE \"%%\") ";
        }


        // filtering by uni
        if ($uni!="") {
            $ufilter = " AND uni LIKE \"%" . $uni ."%\"";
        }
        else {
            $ufilter = "";
        }
        
         // filtering by quota
        if ($qfilter=='Espulso') {
            $qfilter2 = " AND q2017 = \"-\"";
        } elseif ($qfilter=='Attivo') {
            $qfilter2 = " AND q2017 = \"Pagato\"";
        }

        //ordering
        if ($sorting=='Nome') {
            $sorting2 = " ORDER BY nome";
        } elseif ($sorting=='Cognome') {
            $sorting2 = " ORDER BY cognome ASC";
        } elseif ($sorting=='uni'){
            $sorting2 = " ORDER BY uni ASC";
        } elseif ($sorting=='approvato'){
            $sorting2 = " ORDER BY appr ASC";
        } elseif ($sorting=='quota'){
            $sorting2 = " ORDER BY q2017 ASC";
        } elseif ($sorting=='data'){
            $sorting2 = " ORDER BY data DESC";
        } else {
            $sorting2 = "";
        }

        // query db
        $stringa = "SELECT * FROM db_AISF_2016 " . $query . $ufilter . $qfilter2 . $sorting2;

        //echo $stringa;

        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        // output number of rows found
        //echo "Compatible entries: ".$entries.". <br><br>";

        ?>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-8"><h4>ID: <font color="green"><?php echo $ID;?></font></h4></div>
            <div class="col-md-3"></div>
        </div>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-9"><?php echo "number of matching entries: <b>".$entries."</b><br><br>"; ?>
            </div>
            <div class="col-md-1">
                <a class="btn btn-primary" href="../index.php" >Home</a>
                <a class="btn btn-default" href="index_old.php" >Home 2016</a>
                <a class="btn btn-default" href="search_old.php?query=&sorting=data" >Full list 2016</a>
            </div>
            <div class="col-md-1"></div>
        </div>

        <div class="small_skip"></div>

        <div class="row" >
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <?php
                $stringa1 = "SELECT cognome,nome,email,uni,studi,data,q2016,q2017 FROM db_AISF_2016 " . $query . $ufilter . $qfilter2 . $sorting2;
                $stringa_email = "SELECT email FROM db_AISF_2016 " . $query . $ufilter . $qfilter2 . $sorting2;
                echo "
                    <form action=\"download.php\" method=\"GET\" style=\"float: left;\">
                        <input type=\"submit\" value=\"Download\" class=\"btn btn-default\"></input>
                        <input type=\"hidden\" name=\"query\" value='" . $stringa1 . "'></input>
                        <input type=\"hidden\" name=\"type\" value=\"normal\"></input>
                    </form>";
                echo "
                    <form action=\"download.php\" method=\"GET\" style=\"float: left;\">
                        <input type=\"submit\" value=\"Download email\" class=\"btn btn-default\"></input>
                        <input type=\"hidden\" name=\"query\" value='" . $stringa_email . "'></input>
                        <input type=\"hidden\" name=\"type\" value=\"email\"></input>
                    </form>"; 
                if ($ID=='admin'){
                    $stringa1 = "SELECT * FROM db_AISF_2016 " . $query . $ufilter . $sorting2;
                    echo "<form action=\"download.php\" method=\"GET\" style=\"float: left;\">
                                    <input type=\"submit\" value=\"Download full\" class=\"btn btn-default\"></input>
                                    <input type=\"hidden\" name=\"query\" value='" . $stringa1 . "'></input>
                                    <input type=\"hidden\" name=\"type\" value=\"full\"></input>
                            </form>";
                }
                ?>
            </div>
        </div>

        <div class="med_skip"></div>

        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">

                <table class='table'>
                    <tr>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Università</th>
                        <th>Studi</th>
                        <th>Data</th>
                        <th>Stato</th>
                    </tr>

                    <?php

                    // build table printing the rows found
                    $thisPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                    while($row = $result->fetch_assoc())
                    {

                        // various columns
                        $col_name = "<td>".$row['nome']."</td>";

                        $col_email = "<td>".$row['email']."</td>";

                        $col_uni = "<td>".$row['uni']."</td>";

                        $col_stud = "<td>".$row['studi']."</td>";

                        $col_data = "<td>".$row['data']."</td>";

                        //$col_q2016 = "<td>".$row['q2016']. "</td>";

                        $thisPage = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

                        if ($ID=='admin'){
                            $linkto = "singleEntry_old.php?id=" . $row['id']."&page=".urlencode($thisPage)."";
                            $col_surname = "<td><a href=\"".$linkto."\">".$row['cognome']."</a></td>";
                        } else { 
                            $col_surname = "<td>".$row['cognome']."</td>";
                        }
                        $backcolor = status_to_color($row['q2017']);
                        if ($row['q2016']!='Onorario'){
                            $col_stato = "<td class=\"" . $backcolor . "\">" . quota_to_status($row['q2017']) . "</td>";    
                        } else {
                            $col_stato = "<td>Membro onorario</td>";    
                        }
                     
                        echo "<tr>" . $col_surname . $col_name . $col_email . $col_uni . $col_stud . $col_data . $col_stato . "</tr>";

                    }

                    $result->free();
                    $mysqli->close();

                    ?>
                </table>

            </div>
            <div class="col-md-1"></div>
        </div>


        <br>

    </body>
</html>