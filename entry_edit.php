<?php
// get VID from session
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
    return date("d/m/y", $time);
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
        $red_page = $_GET['single_page'];

        // select single row, using ID
        $stringa = "SELECT * FROM " . $table . " WHERE id = '".$sid."'";
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
                <form method="GET" action="edit_record.php">
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
                        <td>
                            <?php echo 
                                "<textarea name=\"new_surname\" rows=\"1\" cols=\"40\">" . $row['cognome'] . "</textarea>" ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td>
                            <?php echo 
                                "<textarea name=\"new_name\" rows=\"1\" cols=\"40\">" . $row['nome'] . "</textarea>" ;?>
                        </td>
                    </tr>
                    <tr>
                        <td>Luogo di nascita</td>
                        <td>
                           <?php 
                            echo 
                                "<textarea name=\"new_birthplace\" rows=\"1\" cols=\"40\">" .$row['luogo_nascita']."</textarea>";
                            ?>
                        </td>
                    <tr>
                        <td>Data di nascita</td>
                        <td>
                            <?php
                            echo 
                                "<textarea name=\"new_birthday\" rows=\"1\" cols=\"20\">".$row['data_nascita']."</textarea>";      
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Indirizzo di residenza</td>
                        <td>
                            <?php 
                            echo 
                                "<textarea name=\"new_address\" rows=\"2\" cols=\"40\">". $row['indirizzo'] ."</textarea>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Codice Fiscale</td>
                        <td>
                            <?php 
                             echo 
                                "<textarea name=\"new_cf\" rows=\"1\" cols=\"40\">". $row['CF'] ."</textarea>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>
                            <?php 
                             echo "<textarea name=\"new_email\" rows=\"1\" cols=\"40\">". $row['email'] ."</textarea>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Università</td>
                        <td>
                            <?php 
                            echo "<textarea name=\"new_uni\" rows=\"1\" cols=\"40\">". $row['uni'] ."</textarea>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Corso di Studi</td>
                        <td>
                            <?php 
                            echo "<textarea name=\"new_studi\" rows=\"1\" cols=\"40\">". $row['studi'] ."</textarea>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Data di iscrizione</td>
                        <td>
                            <?php 
                            echo "<textarea name=\"new_data\" rows=\"1\" cols=\"40\">".$row['data'] ."</textarea>";
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
                    echo "<input type=\"hidden\" name=\"id\" value=\"" . $sid . "\"></input>";     
                    echo "<input type=\"hidden\" name=\"single_page\" value=\"" . $red_page . "\"></input>"; 
                       # echo "<input type=\"submit\" value=\"Modifica\" class=\"btn btn-default\" onclick=\"return confirm('Vuoi veramente modificare questo record?')\" style=\"float: center;\"></input>";
                ?>
                </form >
            </div>
        </div>



        <br>
        <div class="col-md-2"></div>
        <a class="btn btn-default" href="index.php" style="float: left;" >New search</a>
        <a class="btn btn-default" href="search.php?query=&sorting=SURNAME_STRIP&cfilter=all&cfilter2=all&sorting=data" style="float: left;" >Full list</a>
        <?php
            echo "<a class=\"btn btn-default\" href=\"singleEntry.php?id=" . $sid . "\" style=\"float: left;\" >Back</a>";
        ?>

    </body>
</html>