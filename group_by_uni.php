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
$table_lc = $dbinfo[17];

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
        // query db
        $stringa = "SELECT uni1,COUNT(uni1) AS count FROM 
                        (SELECT CASE WHEN uni LIKE 'ESTERO%' THEN 'ESTERO' ELSE uni END AS uni1 FROM ".$table.") 
                            AS t1 GROUP BY uni1 ORDER BY count DESC";

        #echo $stringa;

        $result = $mysqli->query($stringa);
        $entries = $result->num_rows;

        #echo $entries;
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
            <div class="col-md-11">
                <a class="btn btn-default" href="index.php" >Home</a>
                <a class="btn btn-default" href="search.php?query=&sorting=data" >Full list</a>
            </div>
        </div>

        <div class="small_skip"></div>
        
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <table class='table'>
                    <tr>
                        <th>Università</th>
                        <th>Membri</th>
                    </tr>

                    <?php

                    // build table printing the rows found

                    while($row = $result->fetch_assoc())
                    {

                       $col_uni = "<td>".$row['uni1']."</td>";
                        $col_cnt = "<td>".$row['count']."</td>";
                    
                        echo "<tr>" . $col_uni . $col_cnt . "</tr>";
                    }

                    $result->free();
                    $mysqli->close();

                    ?>
                </table>

            </div>
            <div class="col-md-3"></div>
        </div>

    </body>
</html>