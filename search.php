<?php
// get VID from session
require('access.php');
$ID = $_SESSION['ID'];

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
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
        if ($qfilter=='Pagato') {
            $qfilter2 = " AND q2017 = \"Pagato\"";
        } elseif ($qfilter=='Non Pagato') {
            $qfilter2 = " AND q2017 = \"Non Pagato\"";
        } elseif ($qfilter=='-') {
            $qfilter2 = " AND q2017 = \"-\"";
        } else {
            $qfilter2 = "";
        }

        // filtering by approvato
        if ($afilter=='Approvato') {
            $afilter2 = " AND appr = \"Approvato\"";
        } elseif ($afilter=='Non Approvato') {
            $afilter2 = " AND appr = \"Non Approvato\"";
        } else {
            $afilter2 = "";
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
        $stringa = "SELECT * FROM " . $table . $query . $ufilter . $qfilter2 . $afilter2 . $sorting2;

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
            <div class="col-md-2">
                <a class="btn btn-default" href="index.php" >Home</a>
                <a class="btn btn-default" href="search.php?query=&sorting=data" >Full list</a>
            </div>
        </div>

        <div class="small_skip"></div>

        <div class="row" >
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <?php
                $stringa1 = "SELECT cognome,nome,email,uni,studi,data,q2017,appr FROM " . $table . $query . $ufilter . $qfilter2 . $afilter2 . $sorting2;
                $stringa_email = "SELECT email FROM " . $table . $query . $ufilter . $qfilter2 . $afilter2 . $sorting2;
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
                    $stringa1 = "SELECT * FROM " . $table . $query . $ufilter . $qfilter2 . $afilter2 . $sorting2;
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
                        <th>Quota 2017</th>
                        <th>Approvazione</th>
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
                            $linkto = "https://www.ai-sf.it/dbaisf/singleEntry.php?id=" . $row['id']."&page=".urlencode($thisPage)."";
                            $col_surname = "<td><a href=\"".$linkto."\">".$row['cognome']."</a></td>";

                            $backcolor = status_to_btncolor($row['q2017']);
                            $backcolor_appr = appr_to_btncolor($row['appr']);
                            //$col_q2017 = "<td class=\"" . $backcolor . "\" onClick=\"document.location.href='http://www.google.com';\" onmouseover=\"\" style=\"cursor: pointer;\">" . $row['q2017'] . "</td>";
                            if ($row['q2017']!='Onorario'){
                                $col_q2017 = "<td>
                                <form action=\"pagato.php\" method=\"GET\">
                                    <input type=\"submit\" value=\"". $row['q2017'] ."\" class=\"btn btn-default; " . $backcolor . "\" style=\"width:100%\">
                                    </input>
                                    <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                                    <input type=\"hidden\" name=\"statusquo\" value=\"" . $row['q2017'] . "\"></input>
                                    <input type=\"hidden\" name=\"page\" value=\"".$thisPage."\"></input>
                                </form>
                            </td>";
                            } else {
                                $col_q2017 = "<td>
                                    <input type=\"submit\" value=\"Membro onorario\" class=\"btn btn-default; disabled=\"disabled\" style=\"width:100%\">
                                    </input>
                            </td>"; 
                            }
                            $col_appr = "<td>
                                <form action=\"appr.php\" method=\"GET\">
                                    <input type=\"submit\" value=\"". $row['appr'] ."\" class=\"btn btn-default; " . $backcolor_appr . "\" style=\"width:100%\">
                                    </input>
                                    <input type=\"hidden\" name=\"id\" value=\"" . $row['id'] . "\"></input>
                                    <input type=\"hidden\" name=\"statusquo\" value=\"" . $row['appr'] . "\"></input>
                                    <input type=\"hidden\" name=\"page\" value=\"".$thisPage."\"></input>
                                </form>
                            </td>";
                        } else { 
                            $col_surname = "<td>".$row['cognome']."</td>";
                            $backcolor = status_to_color($row['q2017']);
                            if ($row['q2017']!='Onorario'){
                                $col_q2017 = "<td class=\"" . $backcolor . "\">" . $row['q2017'] . "</td>";    
                            } else {
                                $col_q2017 = "<td>Membro onorario</td>";    
                            }
                            $col_appr = "<td>".$row['appr']. "</td>";
                        }

                        echo "<tr>" . $col_surname . $col_name . $col_email . $col_uni . $col_stud . $col_data . $col_q2017 . $col_appr . "</tr>";

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