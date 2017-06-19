<?php

require('access.php');
$ID = $_SESSION['ID'];

if ($ID!='admin'){
    header('Location: ' . 'badPrivileges.php'); 
}

// connect to db
$dbinfo = explode("\n", file_get_contents('loginDB.txt'))[0];
$dbinfo = explode(" ", $dbinfo);
$user = $dbinfo[1];
$password = $dbinfo[3];
$db = $dbinfo[5];
$host = $dbinfo[7];
$port = $dbinfo[9];
$table = $dbinfo[11];
$api = $dbinfo[13];
$gmail_pwd = $dbinfo[15];

$mysqli = new mysqli($host, $user, $password, $db);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// utf-8 encoding
mysqli_set_charset($mysqli, 'utf8');

$stringa_sameCF = "SELECT nome,cognome,CF,COUNT(*) FROM ".$table." GROUP BY nome,cognome,CF HAVING COUNT(*) > 1";
$result_sameCF = $mysqli->query($stringa_sameCF);

$stringa_diffCF = "SELECT nome, cognome, CF FROM (SELECT nome, cognome, CF, COUNT( * ) FROM  ".$table." GROUP BY nome, cognome HAVING COUNT( * ) >1 ) AS t1 WHERE  `CF` NOT IN (SELECT  `CF` FROM (SELECT nome, cognome, CF, COUNT( * ) FROM  ".$table." GROUP BY nome, cognome, CF HAVING COUNT( * ) >1) AS t2)";
$result_diffCF = $mysqli->query($stringa_diffCF);
    
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
        
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8"><h4>Duplicati</h4></div>
            <div class="col-md-2"></div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table class='table'>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th width="30%">Info</th>
                    </tr>
        <?php

        while($row = $result_sameCF->fetch_assoc()){
            echo "  <tr>
                        <td><a href=\"search.php?nome=".$row['nome']."&cognome=".$row['cognome']."\">".$row['cognome']."</a></td>
                        <td>".$row['nome']."</td>
                        <td class=\"btn-danger\" width=\"30%\"></td>
                    </tr>";
        }
        echo "<tr>
        <td class=\"small_skip\"></td>
        <td class=\"small_skip\"></td>
        <td class=\"small_skip\"></td>
        </tr>";
        while($row = $result_diffCF->fetch_assoc()){
            echo "  <tr>
                        <td class=\"btn-default\"><a href=\"search.php?nome=".$row['nome']."&cognome=".$row['cognome']."\">".$row['cognome']."</a></td>
                        <td>".$row['nome']."</td>
                        <td class=\"btn-warning\" width=\"30%\">Codice Fiscale Differente</td>
                    </tr>";
        }


        #$jotformAPI = new JotForm($api);
        #$jotformAPI-> deleteSubmission($sid);

        //$result->free();
        $mysqli->close();

        #echo $sid;    

        #header("location:" . "search.php?query=&cfilter=all&cfilter2=all&sorting=data");


        ?>