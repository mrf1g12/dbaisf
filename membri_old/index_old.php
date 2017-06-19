<?php

require('../access.php');
$ID = $_SESSION['ID'];

// get VID from session
#require('access.php');
#$ID = $_SESSION['ID'];

#date_default_timezone_set('Australia/Melbourne');

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


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Archivio AISF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--><link rel="stylesheet" type="text/css" href="style.css"/></!-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
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

        <br>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align:center"><img style="width:342px;height:124px;" src="../AISF_logo.png" alt="AISF_logo"></div>
            <div class="col-md-4"></div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <h2 style="text-align:center">Archivio Telematico AISF</h2>
            </div>
            <div class="col-md-2"></div>
        </div>

        <br>

        <div class="row" style="text-align:center">
            <div class="col-md-2"></div>
            <div class="col-md-8"><h3>Autenticato come ID =
                <font color="green"><?php echo $ID;?>    </font>
                <small><a href="logout.php" ><b>LOG OUT</b></a></small>
                </h3></div>
            <div class="col-md-2">
                <a class="btn btn-primary" href="../index.php" >Home</a>
            </div>
        </div>

        <br><br><br>

        <div class="row" style="text-align:center">
            <form action="search_old.php" method="GET">

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div class="col-md-8">
                        <hr>
                        <h3>Anagrafica <span style="color:#FF0000">membri 2016</span></h3>
                    </div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="small_skip" style="text-align:left"></div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-3"><b>Cerca per nome</b></div>
                    <div  class="col-md-3"><b>Cerca per cognome</b></div>
                    <div class="col-md-2"><b>Cerca per Università</b></div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-3">
                        <input type="text" name="nome" placeholder="lasciare vuoto per 'tutti'" size=30/>
                    </div>
                    <div  class="col-md-3">
                        <input type="text" name="cognome" placeholder="lasciare vuoto per 'tutti'" size=30/>
                    </div>
                    <div  class="col-md-2">
                        <input type="text" name="uni" placeholder="lasciare vuoto per 'tutte'" size=30/>
                    </div>
                </div>

                <div class="small_skip" style="text-align:left">
                    <div></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div class="col-md-3"><b>Filtra su stato</b></div>
                    <div class="col-md-3"><b>Ordina per</b></div>
                    <div class="col-md-2"></div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div class="col-md-3">
                        <select name="qfilter">
                            <option value="all"></option>
                            <option value="Espulso">Espulso</option>
                            <option value="Attivo">Attivo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sorting">
                            <option value="data">Data iscrizione</option>
                            <option value="Cognome">Cognome</option>
                            <option value="Nome">Nome</option>
                            <option value="uni">Università</option>
                        </select>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2"></div>
                    <div  class="col-md-2"></div>
                </div>

                <div class="small_skip" style="text-align:left">
                    <div></div>
                </div>

                <div class="row" style="text-align:left">
                    <div  class="col-md-2"></div>
                    <div  class="col-md-2"><input type="submit" value="Ricerca" class="btn btn-primary"/></div>
                </div>
            </form>
        </div>


        <br><br>
        <div class="med_skip"></div>
        <div class="med_skip"></div>
        <p><small>

            <?php
            echo 'Current PHP version: ' . phpversion() . '<br>';
            ?>
            EV.MRF.
            </small></p>


    </body>
</html>